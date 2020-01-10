<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pos_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    function getSetting()
    {
        $q = $this->db->get('pos_settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
     function getSettings()
    {
        $q = $this->db->get('settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function updateSetting($data)
    {
        $this->db->where('pos_id', '1');
        if ($this->db->update('pos_settings', $data)) {
            return true;
        }
        return false;
    }

    public function recipe_count($category_id, $warehouse_id, $subcategory_id = NULL, $brand_id = NULL)
    {
		
		$this->db->select('recipe.*');
		$this->db->join('recipe', "recipe.id = warehouses_recipe.recipe_id AND recipe.active = 1");
		
		$this->db->where('warehouses_recipe.warehouse_id', $warehouse_id);
		if ($brand_id) {
            $this->db->where('recipe.brand', $brand_id);
        } elseif ($category_id) {
            $this->db->where('recipe.category_id', $category_id);
        }
        if ($subcategory_id) {
            $this->db->where('recipe.subcategory_id', $subcategory_id);
        }
		$this->db->order_by("recipe.name", "asc");
		$query = $this->db->get("warehouses_recipe");
        if ($query->num_rows() > 0) {
			
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
			
            return count($data);
        }
        return false;
		
    }

    public function fetch_recipe($category_id, $warehouse_id, $limit, $start, $subcategory_id = NULL, $brand_id = NULL)
    {
		
		$this->db->select('recipe.*');
		$this->db->join('recipe', "recipe.id = warehouses_recipe.recipe_id AND recipe.active = 1");
		
		$this->db->where('warehouses_recipe.warehouse_id', $warehouse_id);
		if ($brand_id) {
            $this->db->where('recipe.brand', $brand_id);
        } elseif ($category_id) {
            $this->db->where('recipe.category_id', $category_id);
        }
        if ($subcategory_id) {
            $this->db->where('recipe.subcategory_id', $subcategory_id);
        }
		$this->db->limit($limit, $start);
		$this->db->order_by("recipe.name", "asc");
		$query = $this->db->get("warehouses_recipe");
		
		
       	
		
        if ($query->num_rows() > 0) {
			
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
			
            return $data;
        }
        return false;
    }

    public function registerData($user_id)
    {
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $q = $this->db->get_where('pos_register', array('user_id' => $user_id, 'status' => 'open'), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function openRegister($data)
    {
        if ($this->db->insert('pos_register', $data)) {
            return true;
        }
        return FALSE;
    }

    public function getOpenRegisters()
    {
        $this->db->select("date, user_id, cash_in_hand, CONCAT(" . $this->db->dbprefix('users') . ".first_name, ' ', " . $this->db->dbprefix('users') . ".last_name, ' - ', " . $this->db->dbprefix('users') . ".email) as user", FALSE)
            ->join('users', 'users.id=pos_register.user_id', 'left');
        $q = $this->db->get_where('pos_register', array('status' => 'open'));
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;

    }
	
	public function getTablelist($warehouse_id){
		$this->db->select("restaurant_areas.name AS areas_name, restaurant_tables.area_id, 'tables' ");
		$this->db->join("restaurant_areas", "restaurant_areas.id = restaurant_tables.area_id");
		$this->db->where("restaurant_tables.warehouse_id", $warehouse_id);
		$this->db->group_by("restaurant_tables.area_id");
		$q = $this->db->get("restaurant_tables");
		
		if ($q->num_rows() > 0) {
			 foreach ($q->result() as $row){
				 $this->db->select("restaurant_tables.id AS table_id, restaurant_tables.name AS table_name, restaurant_tables.max_seats AS table_seat");
				 $this->db->where("restaurant_tables.area_id", $row->area_id);
				 $this->db->where("restaurant_tables.warehouse_id", $warehouse_id);
				 $t = $this->db->get("restaurant_tables");
				 
				 if ($t->num_rows() > 0) {
					 foreach ($t->result() as $tow){
						 $table[$row->area_id][] = $tow;
					 }
					 $row->tables = $table[$row->area_id];
				 }
				 
				 $data[] = $row;
			 }
			
			 return $data;
		 }
		return FALSE;
	}


public function checkTimeoutNotify($id){
				
		$notify = "SELECT N.id FROM ".$this->db->dbprefix('notiy')." AS N
		WHERE N.order_item_id = '".$id."'";

		$x = $this->db->query($notify);
		
		if ($x->num_rows() > 0) {
			return $x->row();
		}
		 return FALSE;
	}

	public function checkTables($table_id = NULL, $order_type = NULL){
		
		$current_date = date('Y-m-d');
		$this->db->select("orders.id ")
		->where('orders.table_id', $table_id)
		->where('orders.order_cancel_status', 0)
		->where('orders.payment_status', NULL)
		->where('orders.order_type', $order_type)
		->where('DATE(date)', $current_date);
		$q = $this->db->get('orders');		
		
		if ($q->num_rows() > 0) {
			 $data['status'] = 'success';
			 return $data;
			  
		 }	
		
		return FALSE;
	}
	
	public function getAllTablesWithCustomerRequest($warehouse_id){
		
		$current_date = date('Y-m-d');
		$this->db->select("sales.*, ,restaurant_tables.name as tablename,restaurant_areas.name as areaname");
		$this->db->join("restaurant_tables", "restaurant_tables.id = sales.sales_table_id",'left');
		$this->db->join("restaurant_areas", "restaurant_areas.id = restaurant_tables.area_id",'left');
		$this->db->where('sales.warehouse_id', $warehouse_id);
		$this->db->where('sales.sale_status', 'Process');
		$this->db->where('sales.cancel_status', 0);
		$this->db->where('sales.bilgenerator_type', 1);
		$this->db->where('DATE(date)', $current_date);

		$q = $this->db->get('sales');
				
		
		if ($q->num_rows() > 0) {

            foreach ($q->result() as $row) {
				
				$this->db->select("bils.*, diccounts_for_customer.name AS customer_discount_name, diccounts_for_customer.discount_type AS customer_discount_type, diccounts_for_customer.value AS customer_value");
				$this->db->join("diccounts_for_customer", "diccounts_for_customer.id = bils.customer_discount_id");
				$this->db->where('bils.sales_id', $row->id);
				$b = $this->db->get('bils');
				

				if ($b->num_rows() > 0) {
					foreach ($b->result() as $bil_row) {
						
						$bils[$row->id][] = $bil_row;
					}
					$row->bils = $bils[$row->id];
					$data[] = $row;	
				}
				
			}
			
			return $data;
		}
		return FALSE;
	}
	
	public function getAllSalesWithbiller($sales_type_id = NULL){
		
		$current_date = date('Y-m-d');
		$this->db->select("sales.*, ,restaurant_tables.name as tablename,restaurant_areas.name as areaname,'bils'");
		$this->db->join("restaurant_tables", "restaurant_tables.id = sales.sales_table_id",'left');
		$this->db->join("restaurant_areas", "restaurant_areas.id = restaurant_tables.area_id",'left');
		if(!empty($sales_type_id)){
			$this->db->where('sales.sales_type_id', $sales_type_id);	
		}
		
		$this->db->where('sales.warehouse_id', $this->session->userdata('warehouse_id'));
		$this->db->where('sales.sale_status', 'Process');
		$this->db->where('sales.cancel_status', 0);
		$this->db->where('DATE(date)', $current_date);

		$s = $this->db->get('sales');

		if ($s->num_rows() > 0) {

            foreach ($s->result() as $row) {
				
				$this->db->select("bils.*,companies.credit_limit,companies.name customer_name,companies.customer_type,companies.id company_id,companies.credit_limit");
				$this->db->join("companies", "companies.id = bils.customer_id",'left');
				//$this->db->join("companies_paymentmode_details cp", "cp.company_id=companies.id",'left');
				//$this->db->join("deposits d", "d.company_id=companies.id and (companies.customer_type='postpaid' AND cp.status='active') or (companies.customer_type='prepaid' AND cp.status='paid')",'left');
				
				$this->db->where('bils.sales_id', $row->id);
				$b = $this->db->get('bils');
				// AND cp.due_date > CURRENT_TIMESTAMP
				if ($b->num_rows() > 0) {
					foreach ($b->result() as $bil_row) {
						
						$bils[$row->id][] = $bil_row;
					}
					$row->bils = $bils[$row->id];
					$data[] = $row;	
				}
				
			}
			return $data;
		}

		return FALSE;
	}
	public function getAllBillingDatas(){
		
		$current_date = date('Y-m-d');
		$this->db->select("sales.*, ,restaurant_tables.name as tablename,restaurant_areas.name as areaname,'bils'");
		$this->db->join("restaurant_tables", "restaurant_tables.id = sales.sales_table_id",'left');
		$this->db->join("restaurant_areas", "restaurant_areas.id = restaurant_tables.area_id",'left');
		/*if(!empty($sales_type_id)){
			$this->db->where('sales.sales_type_id', $sales_type_id);	
		}*/
		$this->db->where('sales.warehouse_id', $this->session->userdata('warehouse_id'));
		$this->db->where('sales.sale_status', 'Closed');
		$this->db->where('sales.cancel_status', 0);
		$this->db->where('DATE(date)', $current_date);

		$s = $this->db->get('sales');

		if ($s->num_rows() > 0) {

            foreach ($s->result() as $row) {
				
				$this->db->select("bils.*");
				$this->db->where('bils.sales_id', $row->id);
				$b = $this->db->get('bils');
				

				if ($b->num_rows() > 0) {
					foreach ($b->result() as $bil_row) {
						
						$bils[$row->id][] = $bil_row;
					}
					$row->bils = $bils[$row->id];
					$data[] = $row;	
				}
				
			}
			
			return $data;
		}

		return FALSE;
	}	
	public function getAllDoordeliveryorder(){
		$current_date = date('Y-m-d');
		
		$this->db->select("orders.split_id , orders.customer_id,  'order'");
		$this->db->where("orders.order_type", 3);
		$this->db->where("orders.payment_status", NULL);
		$this->db->where('orders.order_cancel_status', 0);
		$this->db->where('DATE(date)', $current_date);
		$this->db->where('orders.warehouse_id', $this->session->userdata('warehouse_id'));
		$this->db->group_by('orders.split_id');
		$t = $this->db->get("orders");
		if ($t->num_rows() > 0) {
			foreach($t->result() as $row){
				
				$this->db->select("id ");
				$checkbils = $this->db->get_where('sales', array('sales_split_id' => $row->split_id));
				if ($checkbils->num_rows() == 0) {
						
					 $this->db->select("orders.id, orders.customer_id, kitchen_orders.id AS kitchen, kitchen_orders.status,orders.order_type, orders.seats_id, orders.order_status, orders.reference_no, orders.date, orders.split_id, orders.table_id, 'items' AS item")
					->join('kitchen_orders', 'kitchen_orders.sale_id = orders.id', 'left');
					
					$o = $this->db->get_where('orders', array('orders.split_id' => $row->split_id));
					$split[$row->id][] = $row;
					if ($o->num_rows() > 0) {
						
						foreach($o->result() as $oow){
							
							$this->db->select("order_items.*, recipe.image, recipe.khmer_name")
							->join('recipe', 'recipe.id = order_items.recipe_id');
							$i = $this->db->get_where('order_items', array('sale_id' => $oow->id));
							
							if($i->num_rows() > 0){
								
								foreach($i->result() as $item){
									$item_list[$oow->id][] = $item;
								}
								
							}
							
							$oow->item = $item_list[$oow->id];
							
							$order[$row->split_id][] = $oow;
						}
					}
					
				}
					$row->order = $order[$row->split_id];					
					
				
				$data[] = $row;
			}
			return $data;	
		}
		return FALSE;
	}
	public function getAllTakeawayorder(){
		$current_date = date('Y-m-d');
		
		$this->db->select("orders.split_id, orders.customer_id,  'order'");
		$this->db->where("orders.order_type", 2);
		$this->db->where("orders.payment_status", NULL);
		$this->db->where('orders.order_cancel_status', 0);
		$this->db->where('DATE(date)', $current_date);
		$this->db->where('orders.warehouse_id', $this->session->userdata('warehouse_id'));
		$this->db->group_by('orders.split_id');
		$t = $this->db->get("orders");
		if ($t->num_rows() > 0) {
			foreach($t->result() as $row){
						
				$this->db->select("id ");
				$checkbils = $this->db->get_where('sales', array('sales_split_id' => $row->split_id));
				if ($checkbils->num_rows() == 0) {
								
					 $this->db->select("orders.id, orders.customer_id, kitchen_orders.id AS kitchen, kitchen_orders.status,orders.order_type, orders.seats_id, orders.order_status, orders.reference_no, orders.date, orders.split_id, orders.table_id, 'items' AS item")
					 
					->join('kitchen_orders', 'kitchen_orders.sale_id = orders.id', 'left')
					->where('orders.split_id', $row->split_id)
					->where('DATE(date)', $current_date);
					
					$o = $this->db->get('orders');
					$split[$row->id][] = $row;
					if ($o->num_rows() > 0) {
						
						foreach($o->result() as $oow){
							
							$this->db->select("order_items.*, recipe.image, recipe.khmer_name")
							->join('recipe', 'recipe.id = order_items.recipe_id');
							$i = $this->db->get_where('order_items', array('sale_id' => $oow->id));
							
							if($i->num_rows() > 0){
								
								foreach($i->result() as $item){
									$item_list[$oow->id][] = $item;
								}
								
							}
							
							$oow->item = $item_list[$oow->id];
							
							$order[$row->split_id][] = $oow;
						}
					}
					
				}
					$row->order = $order[$row->split_id];					
					
				
				$data[] = $row;
			}
			
			return $data;	
		}
		return FALSE;
	}
	
	public function getAllTablesorder($table_id = NULL){
		$current_date = date('Y-m-d');
		
		$this->db->select("restaurant_tables.id, restaurant_tables.name, restaurant_tables.max_seats, restaurant_tables.warehouse_id, restaurant_areas.name AS area_name, kitchen_orders.waiter_id, 'split_order' ", FALSE)
		->join("restaurant_table_orders", "restaurant_table_orders.table_id = restaurant_tables.id")
		//->join("kitchen_orders", "kitchen_orders.waiter_id = ".$this->session->userdata('user_id')." AND  kitchen_orders.sale_id = restaurant_table_orders.order_id")
		->join("kitchen_orders", "kitchen_orders.sale_id = restaurant_table_orders.order_id")
		->join('orders', 'orders.id = restaurant_table_orders.order_id  AND orders.order_cancel_status = 0')
		->join("restaurant_areas", "restaurant_areas.id = restaurant_tables.area_id");
		if(!empty($table_id)){
		$this->db->where('restaurant_tables.id', $table_id);
		}
		
		if($this->GP['pos-view_allusers_orders']==0){
		    $this->db->where('kitchen_orders.waiter_id',$this->session->userdata('user_id'));
		}
		$this->db->where('DATE(date)', $current_date);
		$this->db->where("orders.order_type", 1);
		$this->db->where('orders.payment_status', NULL);
		$this->db->where('orders.order_status !=', 'Closed');
		$this->db->where('restaurant_tables.warehouse_id', $this->session->userdata('warehouse_id'));
		$this->db->group_by("restaurant_table_orders.table_id");
		
		$t = $this->db->get('restaurant_tables');		
		
		if ($t->num_rows() > 0) {

            foreach ($t->result() as $row) {
				
				
		$this->db->select("orders.id,restaurant_table_sessions.split_id, companies.name,orders.customer_id, restaurant_table_sessions.table_id, restaurant_table_sessions.session_started, 'order' ", FALSE)
				->join('orders', 'orders.split_id = restaurant_table_sessions.split_id AND orders.order_cancel_status = 0')
				->join("companies", "companies.id = orders.customer_id",'left');
				$this->db->where('restaurant_table_sessions.table_id', $row->id);
				$this->db->where("orders.order_type", 1);
				$this->db->where('orders.payment_status', NULL);
				$this->db->where('DATE(date)', $current_date);
				$this->db->group_by('restaurant_table_sessions.split_id');
				$s = $this->db->get('restaurant_table_sessions');
				if ($s->num_rows() > 0) {
						
												
					foreach ($s->result() as $sow) {
						
						//$this->db->select("id ");
						//$checkbils = $this->db->get_where('sales', array('sales_split_id' => $sow->split_id));
						//if ($checkbils->num_rows() == 0) {
								
						 $this->db->select("orders.id, orders.customer_id, kitchen_orders.id AS kitchen, kitchen_orders.status,orders.order_type, orders.seats_id, orders.order_status, orders.reference_no, orders.date, orders.split_id, orders.table_id, 'items' AS item")
						->join('kitchen_orders', 'kitchen_orders.sale_id = orders.id', 'left');
						$this->db->where('orders.split_id', $sow->split_id);
						$this->db->where('orders.table_id', $sow->table_id);
						$this->db->where('orders.order_cancel_status', 0);
						$this->db->where('DATE(date)', $current_date);
						$o = $this->db->get('orders');
						
						$split[$row->id][] = $sow;
						if ($o->num_rows() > 0) {
							
							foreach($o->result() as $oow){
								
								$this->db->select("order_items.*, recipe.image, recipe.khmer_name")
								->join('recipe', 'recipe.id = order_items.recipe_id');
								$i = $this->db->get_where('order_items', array('sale_id' => $oow->id));
								
								if($i->num_rows() > 0){
									
									foreach($i->result() as $item){
										$item_list[$oow->id][] = $item;
									}
									
								}
								
								$oow->item = $item_list[$oow->id];
								
								$order[$sow->split_id][] = $oow;
							}
						}
						$sow->order = $order[$sow->split_id];	
						
						//}
						
						
				}
					
						
					
					
					$row->split_order = $split[$row->id];
				}else{
					$row->split_order = array();
				}
				
				
                $data[] = $row;
				
				

            }
			
			return $data;
        }
		
		return FALSE;
	}
	
	public function getOrderitemlist($order_id, $table_id, $split_id, $user_id){
		$this->db->select("orders.reference_no, kitchen_orders.id AS kitchen, kitchen_orders.waiter_id AS waiter, order_items.id AS item_id, order_items.recipe_id, order_items.recipe_name, order_items.recipe_type, order_items.unit_price, order_items.quantity, order_items.subtotal ")
		->join('kitchen_orders', 'kitchen_orders.sale_id = orders.id AND kitchen_orders.waiter_id = '.$user_id.'', 'left')
		->join('order_items', 'order_items.sale_id = orders.id AND order_items.kitchen_id = kitchen_orders.id', 'left');
		
		$q = $this->db->get_where('orders', array('orders.reference_no' => $order_id, 'orders.table_id' => $table_id, 'orders.split_id' => $split_id));
		if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = $row;
            }
			
            return $data;
        }
        return FALSE;
	}
	
	
	
	public function getBil($table_id, $split_id, $user_id){
		
		
		$this->db->select("orders.id AS order, kitchen_orders.id AS kitchen, 'order_item' AS order_item")
		->join('kitchen_orders', 'kitchen_orders.sale_id = orders.id');
		if(!empty($table_id)){
			$this->db->where('orders.table_id', $table_id);
		}
		$this->db->where('orders.split_id', $split_id);
		$this->db->where('orders.order_cancel_status', 0);
		//$this->db->where('orders.order_status', 'Closed');
		
		$q = $this->db->get('orders');
		
		if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
				
				
			  $this->db->select("order_items.id,order_items.recipe_id, order_items.recipe_code, order_items.recipe_name, order_items.recipe_type, order_items.option_id, order_items.net_unit_price, order_items.unit_price, order_items.quantity, order_items.warehouse_id, order_items.item_tax, order_items.tax_rate_id, order_items.tax, order_items.discount, order_items.item_discount, order_items.subtotal, order_items.serial_no, order_items.real_unit_price, order_items.sale_item_id, order_items.recipe_unit_id, order_items.recipe_unit_code, order_items.unit_quantity, order_items.comment, order_items.gst, order_items.cgst, order_items.sgst, order_items.igst", FALSE);
				$i = $this->db->get_where('order_items', array('order_items.sale_id' => $row->order, 'order_items.kitchen_id' => $row->kitchen,  'order_items.order_item_cancel_status	' => 0));
				
			
				if ($i->num_rows() > 0) {
					foreach (($i->result()) as $iow) {
						$item[$row->order][] = $iow;
					}
					$row->order_item = $item[$row->order];
				}else{
					$row->order_item = array();	
				}
				
                $data['items'][] = $row->order_item;
            }
			
			$this->db->select("orders.*");
			if(!empty($table_id)){
				$this->db->where('orders.table_id', $table_id);
			}
			$this->db->where('orders.split_id', $split_id);
			$this->db->where('orders.order_cancel_status', 0);
			//$this->db->where('orders.order_status', 'Closed');
			$this->db->group_by('orders.split_id');			
			$o = $this->db->get('orders');
			foreach (($o->result()) as $result) {
				$data['order'][] = $result;
				
			}
            return $data;
        }
        return FALSE;
	}
	
	
	public function updateNewSales($order_data, $item_data){
		
		if($this->db->insert('sales', $order_data)){
			
			$sale_id = $this->db->insert_id();
			foreach ($item_data as $item) {
                $item->sale_id = $sale_id;
                $this->db->insert('sale_items', $item);
			}
			return TRUE;
		}
		return FALSE;
	}
	
	public function getSplititemlist($table_id, $split_id, $user_id){
		$this->db->select("orders.reference_no, kitchen_orders.id AS kitchen, kitchen_orders.waiter_id AS waiter, order_items.id AS item_id, order_items.recipe_id, order_items.recipe_name, order_items.recipe_type, order_items.unit_price, order_items.quantity, order_items.subtotal ")
		->join('kitchen_orders', 'kitchen_orders.sale_id = orders.id AND kitchen_orders.waiter_id = '.$user_id.'', 'left')
		->join('order_items', 'order_items.sale_id = orders.id AND order_items.kitchen_id = kitchen_orders.id', 'left');
		
		$q = $this->db->get_where('orders', array('orders.table_id' => $table_id, 'orders.split_id' => $split_id));
		$this->db->select();
		if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = $row;
            }
			
            return $data;
        }
        return FALSE;
	}
	
	public function getBillCashierPrintdata($sale_id){

     $this->db->select("sale_items.recipe_name,sale_items.quantity,sale_items.subtotal,sale_items.unit_price") 
         
             ->join('sale_items', 'sales.id = sale_items.sale_id');

        $q = $this->db->get_where('sales', array('sales.id' => $sale_id));

        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = $row;
            }
            
            return $data;
        }
       
        return FALSE;
    }
	
   public function getAllTablesbiller(){

        $this->db->select("sales.reference_no,sales.id");    
             /*->join('sale_items', 'sale_items.sale_id = sales.id');*/
        $q = $this->db->get('sales');

        /*$this->db->select("orders.reference_no, orders.split_id,orders.total,orders.total_tax, orders.shipping,orders.grand_total,orders.total_items");        
        $q = $this->db->get_where('orders', array('orders.split_id' => $split_id));*/
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = $row;
            }
            
            return $data;
        }
       
        return FALSE;

        
        /*$this->db->select("restaurant_tables.id, restaurant_tables.name, restaurant_tables.max_seats, 'split_order' ", FALSE);
        $t = $this->db->get('restaurant_tables');       
        
        if ($t->num_rows() > 0) {

            foreach ($t->result() as $row) {
                $this->db->select("restaurant_table_sessions.split_id, restaurant_table_sessions.table_id, 'order' ", FALSE)
                ->group_by('restaurant_table_sessions.split_id');
                $s = $this->db->get_where('restaurant_table_sessions', array('restaurant_table_sessions.table_id' => $row->id));
                if ($s->num_rows() > 0) {
                        
                        foreach ($s->result() as $sow) {
                                
                                 $this->db->select("orders.id, kitchen_orders.status,orders.order_type, orders.seats_id, orders.order_status, orders.reference_no, orders.date, orders.split_id, orders.table_id")
                                ->join('kitchen_orders', 'kitchen_orders.sale_id = orders.id', 'left');
                                
                                $o = $this->db->get_where('orders', array('orders.split_id' => $sow->split_id, 'orders.table_id' => $sow->table_id));
                                $split[$row->id][] = $sow;
                                if ($o->num_rows() > 0) {
                                    
                                    foreach($o->result() as $oow){
                                        $order[$sow->split_id][] = $oow;
                                    }
                                }
                                $sow->order = $order[$sow->split_id];                   
                        }
                    
                    
                    $row->split_order = $split[$row->id];
                }else{
                    $row->split_order = array();
                }
                
                $data[] = $row;

            }
            
            return $data;
        }
        
        return FALSE;*/
    } 
	public function getBilleritemlist($order_id, $table_id, $split_id, $user_id){
		
        $this->db->select("orders.reference_no, kitchen_orders.id AS kitchen, kitchen_orders.waiter_id AS waiter, order_items.id AS item_id, order_items.recipe_id, order_items.recipe_name, order_items.recipe_type, order_items.unit_price, order_items.quantity, order_items.subtotal ")
        ->join('kitchen_orders', 'kitchen_orders.sale_id = orders.id AND kitchen_orders.waiter_id = '.$user_id.'', 'left')
        ->join('order_items', 'order_items.sale_id = orders.id AND order_items.kitchen_id = kitchen_orders.id', 'left');
        
        $q = $this->db->get_where('orders', array('orders.reference_no' => $order_id, 'orders.table_id' => $table_id, 'orders.split_id' => $split_id));
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = $row;
            }
            
            return $data;
        }
        return FALSE;
    }

    
     public function getSalesData($sale_id){

        $this->db->select("sales.*");     

        $q = $this->db->get_where('sales', array('sales.id' => $sale_id));

        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = $row;
            }
            
            return $data;
        }
       
        return FALSE;
    }
	
    public function closeRegister($rid, $user_id, $data)
    {
        if (!$rid) {
            $rid = $this->session->userdata('register_id');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        if ($data['transfer_opened_bills'] == -1) {
            $this->db->delete('suspended_bills', array('created_by' => $user_id));
        } elseif ($data['transfer_opened_bills'] != 0) {
            $this->db->update('suspended_bills', array('created_by' => $data['transfer_opened_bills']), array('created_by' => $user_id));
        }
        if ($this->db->update('pos_register', $data, array('id' => $rid, 'user_id' => $user_id))) {
            return true;
        }
        return FALSE;
    }

    public function getUsers()
    {
        $q = $this->db->get_where('users', array('company_id' => NULL));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
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

    public function getrecipeByCode($code)
    {
        $this->db->like('code', $code, 'both')->order_by("code");
        $q = $this->db->get('recipe');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getWHrecipe($code, $warehouse_id)
    {
        $this->db->select('recipe.*, warehouses_recipe.quantity, categories.id as category_id, categories.name as category_name')
            ->join('warehouses_recipe', 'warehouses_recipe.recipe_id=recipe.id', 'left')
            ->join('categories', 'categories.id=recipe.category_id', 'left')
            ->group_by('recipe.id');
        $q = $this->db->get_where("recipe", array('recipe.code' => $code));
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;
    }

    public function getrecipeOptions($recipe_id, $warehouse_id, $all = NULL)
    {
        $wpv = "( SELECT option_id, warehouse_id, quantity from {$this->db->dbprefix('warehouses_recipe_variants')} WHERE recipe_id = {$recipe_id}) FWPV";
        $this->db->select('recipe_variants.id as id, recipe_variants.name as name, recipe_variants.price as price, recipe_variants.quantity as total_quantity, FWPV.quantity as quantity', FALSE)
            ->join($wpv, 'FWPV.option_id=recipe_variants.id', 'left')
            //->join('warehouses', 'warehouses.id=recipe_variants.warehouse_id', 'left')
            ->where('recipe_variants.recipe_id', $recipe_id)
            ->group_by('recipe_variants.id');

        if (! $this->Settings->overselling && ! $all) {
            $this->db->where('FWPV.warehouse_id', $warehouse_id);
            $this->db->where('FWPV.quantity >', 0);
        }
        $q = $this->db->get('recipe_variants');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function getrecipeAddons($recipe_id, $all = NULL)
    {
      	$this->db->select("recipe_addon.*, recipe.name AS addon, recipe.price")->join('recipe', 'recipe.id = recipe_addon.addon_id')->where('recipe_addon.recipe_id', $recipe_id);
        $q = $this->db->get('recipe_addon');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	

    public function getrecipeComboItems($pid, $warehouse_id)
    {
		
        $this->db->select('recipe.id as id, recipe_combo_items.item_code as code, recipe_combo_items.quantity as qty, recipe.name as name, recipe.type as type')
            ->join('recipe', 'recipe.code=recipe_combo_items.item_code', 'left')
            ->join('warehouses_recipe', 'warehouses_recipe.recipe_id=recipe.id', 'left')
            ->where('warehouses_recipe.warehouse_id', $warehouse_id)
            ->group_by('recipe_combo_items.id');
			
        $q = $this->db->get_where('recipe_combo_items', array('recipe_combo_items.recipe_id' => $pid));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			
            return $data;
        }
        return FALSE;
    }

    public function updateOptionQuantity($option_id, $quantity)
    {
        if ($option = $this->getrecipeOptionByID($option_id)) {
            $nq = $option->quantity - $quantity;
            if ($this->db->update('recipe_variants', array('quantity' => $nq), array('id' => $option_id))) {
                return TRUE;
            }
        }
        return FALSE;
    }

    public function addOptionQuantity($option_id, $quantity)
    {
        if ($option = $this->getrecipeOptionByID($option_id)) {
            $nq = $option->quantity + $quantity;
            if ($this->db->update('recipe_variants', array('quantity' => $nq), array('id' => $option_id))) {
                return TRUE;
            }
        }
        return FALSE;
    }

    public function getrecipeOptionByID($id)
    {
        $q = $this->db->get_where('recipe_variants', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getrecipeWarehouseOptionQty($option_id, $warehouse_id)
    {
        $q = $this->db->get_where('warehouses_recipe_variants', array('option_id' => $option_id, 'warehouse_id' => $warehouse_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function updaterecipeOptionQuantity($option_id, $warehouse_id, $quantity, $recipe_id)
    {
        if ($option = $this->getrecipeWarehouseOptionQty($option_id, $warehouse_id)) {
            $nq = $option->quantity - $quantity;
            if ($this->db->update('warehouses_recipe_variants', array('quantity' => $nq), array('option_id' => $option_id, 'warehouse_id' => $warehouse_id))) {
                $this->site->syncVariantQty($option_id, $warehouse_id);
                return TRUE;
            }
        } else {
            $nq = 0 - $quantity;
            if ($this->db->insert('warehouses_recipe_variants', array('option_id' => $option_id, 'recipe_id' => $recipe_id, 'warehouse_id' => $warehouse_id, 'quantity' => $nq))) {
                $this->site->syncVariantQty($option_id, $warehouse_id);
                return TRUE;
            }
        }
        return FALSE;
    }

    public function addSale($data = array(), $items = array(), $payments = array(), $sid = NULL)
    {
        $cost = $this->site->costing($items);
        // $this->sma->print_arrays($cost);

        if ($this->db->insert('sales', $data)) {
            $sale_id = $this->db->insert_id();

            foreach ($items as $item) {

                $item['sale_id'] = $sale_id;
                $this->db->insert('sale_items', $item);
                $sale_item_id = $this->db->insert_id();
                if ($data['sale_status'] == 'completed' && $this->site->getrecipeByID($item['recipe_id'])) {

                    $item_costs = $this->site->item_costing($item);
                    foreach ($item_costs as $item_cost) {
                        if (isset($item_cost['date']) || isset($item_cost['pi_overselling'])) {
                            $item_cost['sale_item_id'] = $sale_item_id;
                            $item_cost['sale_id'] = $sale_id;
                            $item_cost['date'] = date('Y-m-d', strtotime($data['date']));
                            if(! isset($item_cost['pi_overselling'])) {
                                $this->db->insert('costing', $item_cost);
                            }
                        } else {
                            foreach ($item_cost as $ic) {
                                $ic['sale_item_id'] = $sale_item_id;
                                $ic['sale_id'] = $sale_id;
                                $ic['date'] = date('Y-m-d', strtotime($data['date']));
                                if(! isset($ic['pi_overselling'])) {
                                    $this->db->insert('costing', $ic);
                                }
                            }
                        }
                    }
                }
            }

            if ($data['sale_status'] == 'completed') {
                $this->site->syncPurchaseItems($cost);
            }

            $msg = array();
            if (!empty($payments)) {
                $paid = 0;
                foreach ($payments as $payment) {
                    if (!empty($payment) && isset($payment['amount']) && $payment['amount'] != 0) {
                        $payment['sale_id'] = $sale_id;
                        $payment['reference_no'] = $this->site->getReference('pay');
                        if ($payment['paid_by'] == 'ppp') {
                            $card_info = array("number" => $payment['cc_no'], "exp_month" => $payment['cc_month'], "exp_year" => $payment['cc_year'], "cvc" => $payment['cc_cvv2'], 'type' => $payment['cc_type']);
                            $result = $this->paypal($payment['amount'], $card_info);
                            if (!isset($result['error'])) {
                                $payment['transaction_id'] = $result['transaction_id'];
                                $payment['date'] = $this->sma->fld($result['created_at']);
                                $payment['amount'] = $result['amount'];
                                $payment['currency'] = $result['currency'];
                                unset($payment['cc_cvv2']);
                                $this->db->insert('payments', $payment);
                                $this->site->updateReference('pay');
                                $paid += $payment['amount'];
                            } else {
                                $msg[] = lang('payment_failed');
                                if (!empty($result['message'])) {
                                    foreach ($result['message'] as $m) {
                                        $msg[] = '<p class="text-danger">' . $m['L_ERRORCODE'] . ': ' . $m['L_LONGMESSAGE'] . '</p>';
                                    }
                                } else {
                                    $msg[] = lang('paypal_empty_error');
                                }
                            }
                        } elseif ($payment['paid_by'] == 'stripe') {
                            $card_info = array("number" => $payment['cc_no'], "exp_month" => $payment['cc_month'], "exp_year" => $payment['cc_year'], "cvc" => $payment['cc_cvv2'], 'type' => $payment['cc_type']);
                            $result = $this->stripe($payment['amount'], $card_info);
                            if (!isset($result['error'])) {
                                $payment['transaction_id'] = $result['transaction_id'];
                                $payment['date'] = $this->sma->fld($result['created_at']);
                                $payment['amount'] = $result['amount'];
                                $payment['currency'] = $result['currency'];
                                unset($payment['cc_cvv2']);
                                $this->db->insert('payments', $payment);
                                $this->site->updateReference('pay');
                                $paid += $payment['amount'];
                            } else {
                                $msg[] = lang('payment_failed');
                                $msg[] = '<p class="text-danger">' . $result['code'] . ': ' . $result['message'] . '</p>';
                            }
                        } elseif ($payment['paid_by'] == 'authorize') {
                            $authorize_arr = array("x_card_num" => $payment['cc_no'], "x_exp_date" => ($payment['cc_month'].'/'.$payment['cc_year']), "x_card_code" => $payment['cc_cvv2'], 'x_amount' => $payment['amount'], 'x_invoice_num' => $sale_id, 'x_description' => 'Sale Ref '.$data['reference_no'].' and Payment Ref '.$payment['reference_no']);
                            list($first_name, $last_name) = explode(' ', $payment['cc_holder'], 2);
                            $authorize_arr['x_first_name'] = $first_name;
                            $authorize_arr['x_last_name'] = $last_name;
                            $result = $this->authorize($authorize_arr);
                            if (!isset($result['error'])) {
                                $payment['transaction_id'] = $result['transaction_id'];
                                $payment['approval_code'] = $result['approval_code'];
                                $payment['date'] = $this->sma->fld($result['created_at']);
                                unset($payment['cc_cvv2']);
                                $this->db->insert('payments', $payment);
                                $this->site->updateReference('pay');
                                $paid += $payment['amount'];
                            } else {
                                $msg[] = lang('payment_failed');
                                $msg[] = '<p class="text-danger">' . $result['msg'] . '</p>';
                            }
                        } else {
                            if ($payment['paid_by'] == 'gift_card') {
                                $this->db->update('gift_cards', array('balance' => $payment['gc_balance']), array('card_no' => $payment['cc_no']));
                                unset($payment['gc_balance']);
                            } elseif ($payment['paid_by'] == 'deposit') {
                                $customer = $this->site->getCompanyByID($data['customer_id']);
                                $this->db->update('companies', array('deposit_amount' => ($customer->deposit_amount-$payment['amount'])), array('id' => $customer->id));
                            }
                            unset($payment['cc_cvv2']);
                            $this->db->insert('payments', $payment);
                            $this->site->updateReference('pay');
                            $paid += $payment['amount'];
                        }
                    }
                }
                $this->site->syncSalePayments($sale_id);
            }

            $this->site->syncQuantity($sale_id);
            if ($sid) {
                $this->deleteBill($sid);
            }
            $this->sma->update_award_points($data['grand_total'], $data['customer_id'], $data['created_by']);
            $this->site->updateReference('pos');
            return array('sale_id' => $sale_id, 'message' => $msg);

        }

        return false;
    }
	
	
	public function addKitchen($data = array(), $items = array(), $kitchen = array(), $notification_array = array(), $warehouse_id, $user_id)
	{		
		if ($this->db->insert('orders', $data)){
				$sale_id = $this->db->insert_id();
				
				$this->db->select('orders.*, restaurant_tables.name AS table_name');
				$this->db->join('restaurant_tables', 'restaurant_tables.id = orders.table_id', 'left');		
			   $this->db->where('orders.id', $sale_id);
				$t = $this->db->get('orders');
				$kit = array();
				if ($t->num_rows() > 0) {
					$orders_details =  $t->row();
					$kit['orders_details'] = $orders_details;
				}
				
				
				$kitchen_details = $this->db->select('restaurant_kitchens.id, restaurant_kitchens.name')->get('restaurant_kitchens');
				
				if ($kitchen_details->num_rows() > 0) {					
					foreach (($kitchen_details->result()) as $kitchen_row) {
						
						$printers_details = $this->db->select('*')->where('id', $kitchen_row->id)->get('printers');
						
						if ($printers_details->num_rows() > 0) {
							$kitchen_row->printers_details =  $printers_details->row();
							
						}
						
						foreach($items as $key => $kit_item){
							if($kitchen_row->id == $kit_item['kitchen_type_id']){
								
								$addons = $this->site->getAddonByRecipe($kit_item['recipe_id'], $kit_item['addon_id']);
								$get_item =  $this->site->getrecipeByID($kit_item['get_item']);
									
								$kitchen_row->kit_o[$key]['recipe_id'] = $kit_item['recipe_id'];
								
								if($this->Settings->user_language == 'khmer' || true){
									$khmer_name = $this->site->getrecipeKhmer($kit_item['recipe_id']);	
									$khmer_image = $this->site->getrecipeKhmerimage($kit_item['recipe_id']);
									
									if(!empty($khmer_name)){
										$kitchen_row->kit_o[$key]['recipe_name'] = $khmer_name;
										
									}else{
										$kitchen_row->kit_o[$key]['recipe_name'] = $kit_item['recipe_name'];
									}
								}else{
									$kitchen_row->kit_o[$key]['recipe_name'] = $kit_item['recipe_name'];
								}
								
								$kitchen_row->kit_o[$key]['en_recipe_name'] = $kit_item['recipe_name'];
								$kitchen_row->kit_o[$key]['khmer_recipe_image'] = !empty($khmer_image) ? (base_url().'assets/language/'.$khmer_image) : '';
								
								
								$kitchen_row->kit_o[$key]['quantity'] = $kit_item['quantity'];
								
								$kitchen_row->kit_o[$key]['get_item_name'] = $get_item->name;
								$kitchen_row->kit_o[$key]['total_get_quantity'] = $get_item->total_get_quantity;
								
								foreach($addons as $addons_row){
                                   		$addon_name = '<small class="text-danger">'.$addons_row->addon_name.' , '.'</small>';
                                	}
									
								$kitchen_row->kit_o[$key]['addons'] = $addon_name;
								
								//$kitchen_row->kit_o[$kitchen_row->id][] = $kit_item['quantity'];
							}
						}
						$kit['kitchens'][] = $kitchen_row;
					}
					
				}else{
					$kit[] = '';	
				}
				
				$this->site->create_notification($notification_array);
				
				$msg = array();
				$msg[] = 'Your order has been success';
				$kitchen['sale_id'] = $sale_id;
				
				$this->db->insert('restaurant_table_orders', array('order_id' => $sale_id, 'table_id' => $data['table_id']));
				$this->db->insert('restaurant_table_sessions', array('table_id' => $data['table_id'],'order_id' => $sale_id, 'split_id' => $data['split_id'], 'customer_id' => $data['customer_id'], 'session_started' => date('Y-m-d H:i:s')));
				
				if($this->db->insert('kitchen_orders', $kitchen)){
					$kitchen_id = $this->db->insert_id();
					
					foreach ($items as $item) {
						$item['sale_id'] = $sale_id;
						$item['kitchen_id'] = $kitchen_id;
						$this->db->insert('order_items', $item);
					}
					$msg[] = 'Order sent to kitchen process. wait few mintues';
					
				}
				
				
				return array('sale_id' => $sale_id, 'message' => $msg, 'kitchen_data' => $kit);
		}
		return false;
	}

    public function getrecipeoneByCode($code)
    {
        $q = $this->db->get_where('recipe', array('code' => $code), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getrecipeByName($name)
    {
        $q = $this->db->get_where('recipe', array('name' => $name), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;
    }

    public function getAllBillerCompanies()
    {
        $q = $this->db->get_where('companies', array('group_name' => 'biller'));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }

    public function getAllCustomerCompanies()
    {
        $q = $this->db->get_where('companies', array('group_name' => 'customer'));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }

    public function getCompanyByID($id)
    {
        $q = $this->db->get_where('companies', array('id' => $id), 1);        
        if ($q->num_rows() > 0) {        	
            return $q->row();
        }
        return FALSE;
    }

    public function getAllrecipe()
    {
        $q = $this->db->query('SELECT * FROM recipe ORDER BY id');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }

    public function getrecipeByID($id)
    {

        $q = $this->db->get_where('recipe', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;
    }

    public function getAllTaxRates()
    {
        $q = $this->db->get('tax_rates');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }

    public function getTaxRateByID($id)
    {

        $q = $this->db->get_where('tax_rates', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;
    }

    public function updaterecipeQuantity($recipe_id, $warehouse_id, $quantity)
    {

        if ($this->addQuantity($recipe_id, $warehouse_id, $quantity)) {
            return true;
        }

        return false;
    }

    public function addQuantity($recipe_id, $warehouse_id, $quantity)
    {
        if ($warehouse_quantity = $this->getrecipeQuantity($recipe_id, $warehouse_id)) {
            $new_quantity = $warehouse_quantity['quantity'] - $quantity;
            if ($this->updateQuantity($recipe_id, $warehouse_id, $new_quantity)) {
                $this->site->syncrecipeQty($recipe_id, $warehouse_id);
                return TRUE;
            }
        } else {
            if ($this->insertQuantity($recipe_id, $warehouse_id, -$quantity)) {
                $this->site->syncrecipeQty($recipe_id, $warehouse_id);
                return TRUE;
            }
        }
        return FALSE;
    }

    public function insertQuantity($recipe_id, $warehouse_id, $quantity)
    {
        if ($this->db->insert('warehouses_recipe', array('recipe_id' => $recipe_id, 'warehouse_id' => $warehouse_id, 'quantity' => $quantity))) {
            return true;
        }
        return false;
    }

    public function updateQuantity($recipe_id, $warehouse_id, $quantity)
    {
        if ($this->db->update('warehouses_recipe', array('quantity' => $quantity), array('recipe_id' => $recipe_id, 'warehouse_id' => $warehouse_id))) {
            return true;
        }
        return false;
    }

    public function getrecipeQuantity($recipe_id, $warehouse)
    {
        $q = $this->db->get_where('warehouses_recipe', array('recipe_id' => $recipe_id, 'warehouse_id' => $warehouse), 1);
        if ($q->num_rows() > 0) {
            return $q->row_array(); //$q->row();
        }
        return FALSE;
    }

    public function getItemByID($id)
    {
        $q = $this->db->get_where('sale_items', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllSales()
    {
        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function sales_count()
    {
        return $this->db->count_all("sales");
    }

    public function fetch_sales($limit, $start)
    {
        $this->db->limit($limit, $start);
        $this->db->order_by("id", "desc");
        $query = $this->db->get("sales");

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getAllInvoiceItems($sale_id)
    {
        if ($this->pos_settings->item_order == 0) {
            $this->db->select('bil_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, recipe_variants.name as variant, recipe.details as details, recipe.khmer_name, recipe.hsn_code as hsn_code')
            ->join('recipe', 'recipe.id=bil_items.recipe_id', 'left')
            ->join('tax_rates', 'tax_rates.id=bil_items.tax_rate_id', 'left')
            ->join('recipe_variants', 'recipe_variants.id=bil_items.option_id', 'left')
            ->group_by('bil_items.id')
            ->order_by('id', 'asc');
        } elseif ($this->pos_settings->item_order == 1) {
            $this->db->select('bil_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, recipe_variants.name as variant, categories.id as category_id, categories.name as category_name, recipe.details as details, recipe.khmer_name, recipe.hsn_code as hsn_code')
            ->join('tax_rates', 'tax_rates.id=bil_items.tax_rate_id', 'left')
            ->join('recipe_variants', 'recipe_variants.id=bil_items.option_id', 'left')
            ->join('recipe', 'recipe.id=bil_items.recipe_id', 'left')
            ->join('categories', 'categories.id=recipe.category_id', 'left')
            ->group_by('bil_items.id')
            ->order_by('categories.id', 'asc');
        }

        $q = $this->db->get_where('bil_items', array('bil_id' => $sale_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getSuspendedSaleItems($id)
    {
        $q = $this->db->get_where('suspended_items', array('suspend_id' => $id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getSaleItems($id)
    {
        $q = $this->db->get_where('sale_items', array('sale_id' => $id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getSuspendedSales($user_id = NULL)
    {
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $q = $this->db->get_where('suspended_bills', array('created_by' => $user_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }


    public function getOpenBillByID($id)
    {

        $q = $this->db->get_where('suspended_bills', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;
    }

    public function getInvoiceByID($id)
    {
    	$this->db->select("bils.*,tax_rates.name as tax_name, tax_rates.rate as tax_rate")
	    ->join('tax_rates', 'tax_rates.id = bils.tax_id','left')
	    ->where('bils.id', $id);
		$q = $this->db->get('bils');
        
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;
    }

    public function bills_count()
    {
        if (!$this->Owner && !$this->Admin) {
            $this->db->where('created_by', $this->session->userdata('user_id'));
        }
        return $this->db->count_all_results("suspended_bills");
    }

    public function fetch_bills($limit, $start)
    {
        if (!$this->Owner && !$this->Admin) {
            $this->db->where('created_by', $this->session->userdata('user_id'));
        }
        $this->db->limit($limit, $start);
        $this->db->order_by("id", "asc");
        $query = $this->db->get("suspended_bills");

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getTodaySales()
    {
        $sdate = date('Y-m-d 00:00:00');
        $edate = date('Y-m-d 23:59:59');
        $this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('type', 'received')->where('sales.date >=', $sdate)->where('payments.date <=', $edate);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getCosting()
    {
        $date = date('Y-m-d');
        $this->db->select('SUM( COALESCE( purchase_unit_cost, 0 ) * quantity ) AS cost, SUM( COALESCE( sale_unit_price, 0 ) * quantity ) AS sales, SUM( COALESCE( purchase_net_unit_cost, 0 ) * quantity ) AS net_cost, SUM( COALESCE( sale_net_unit_price, 0 ) * quantity ) AS net_sales', FALSE)
            ->where('date', $date);

        $q = $this->db->get('costing');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTodayCCSales()
    {
        $date = date('Y-m-d 00:00:00');
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cc_slips, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('paid_by', 'CC');

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTodayCashSales()
    {
        $date = date('Y-m-d 00:00:00');
        $this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('paid_by', 'cash');

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTodayRefunds()
    {
        $date = date('Y-m-d 00:00:00');
        $this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS returned', FALSE)
            ->join('sales', 'sales.id=payments.return_id', 'left')
            ->where('type', 'returned')->where('payments.date >', $date);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTodayExpenses()
    {
        $date = date('Y-m-d 00:00:00');
        $this->db->select('SUM( COALESCE( amount, 0 ) ) AS total', FALSE)
            ->where('date >', $date);

        $q = $this->db->get('expenses');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTodayCashRefunds()
    {
        $date = date('Y-m-d 00:00:00');
        $this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS returned', FALSE)
            ->join('sales', 'sales.id=payments.return_id', 'left')
            ->where('type', 'returned')->where('payments.date >', $date)->where('paid_by', 'cash');

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTodayChSales()
    {
        $date = date('Y-m-d 00:00:00');
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('paid_by', 'Cheque');

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTodayPPPSales()
    {
        $date = date('Y-m-d 00:00:00');
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('paid_by', 'ppp');

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTodayStripeSales()
    {
        $date = date('Y-m-d 00:00:00');
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('paid_by', 'stripe');

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTodayAuthorizeSales()
    {
        $date = date('Y-m-d 00:00:00');
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('paid_by', 'authorize');

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterSales($date, $user_id = NULL)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date);
        $this->db->where('payments.created_by', $user_id);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }


    public function getRegisterCCSales($date, $user_id = NULL)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }

        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }

        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cc_slips, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('bils', 'bils.id=payments.bill_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('payments.paid_by', 'CC');
        $this->db->where('payments.created_by', $user_id);

        $q = $this->db->get('payments');
        // print_r($this->db->error());die;
        if ($q->num_rows() > 0) {

            return $q->row();

        }
        return false;
    }

    public function getRegisterCashSales($date, $user_id = NULL)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('bils', 'bils.id=payments.bill_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('payments.paid_by', 'cash');
        $this->db->where('payments.created_by', $user_id);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterRefunds($date, $user_id = NULL)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS returned', FALSE)
            ->join('bils', 'bils.id=payments.bill_id', 'left')
            ->where('type', 'returned')->where('payments.date >', $date);
        $this->db->where('payments.created_by', $user_id);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterCashRefunds($date, $user_id = NULL)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS returned', FALSE)
            ->join('bils', 'bils.id=payments.bill_id', 'left')
            ->where('type', 'returned')->where('payments.date >', $date)->where('payments.paid_by', 'cash');
        $this->db->where('payments.created_by', $user_id);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterExpenses($date, $user_id = NULL)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('SUM( COALESCE( amount, 0 ) ) AS total', FALSE)
            ->where('date >', $date);
        $this->db->where('created_by', $user_id);

        $q = $this->db->get('expenses');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterChSales($date, $user_id = NULL)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('bils', 'bils.id=payments.bill_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('payments.paid_by', 'Cheque');
        $this->db->where('payments.created_by', $user_id);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterGCSales($date, $user_id = NULL)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('bils', 'bils.id=payments.bill_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('payments.paid_by', 'gift_card');
        $this->db->where('payments.created_by', $user_id);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterPPPSales($date, $user_id = NULL)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('bils', 'bils.id=payments.bill_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('payments.paid_by', 'ppp');
        $this->db->where('payments.created_by', $user_id);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterStripeSales($date, $user_id = NULL)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('bils', 'bils.id=payments.bill_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('payments.paid_by', 'stripe');
        $this->db->where('payments.created_by', $user_id);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterAuthorizeSales($date, $user_id = NULL)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('bils', 'bils.id=payments.bill_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('payments.paid_by', 'authorize');
        $this->db->where('payments.created_by', $user_id);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function suspendSale($data = array(), $items = array(), $did = NULL)
    {
        $sData = array(
            'count' => $data['total_items'],
            'biller_id' => $data['biller_id'],
            'customer_id' => $data['customer_id'],
            'warehouse_id' => $data['warehouse_id'],
            'customer' => $data['customer'],
            'date' => $data['date'],
            'suspend_note' => $data['suspend_note'],
            'total' => $data['grand_total'],
            'order_tax_id' => $data['order_tax_id'],
            'order_discount_id' => $data['order_discount_id'],
            'created_by' => $this->session->userdata('user_id')
        );

        if ($did) {

            if ($this->db->update('suspended_bills', $sData, array('id' => $did)) && $this->db->delete('suspended_items', array('suspend_id' => $did))) {
                $addOn = array('suspend_id' => $did);
                end($addOn);
                foreach ($items as &$var) {
                    $var = array_merge($addOn, $var);
                }
                if ($this->db->insert_batch('suspended_items', $items)) {
                    return TRUE;
                }
            }

        } else {

            if ($this->db->insert('suspended_bills', $sData)) {
                $suspend_id = $this->db->insert_id();
                $addOn = array('suspend_id' => $suspend_id);
                end($addOn);
                foreach ($items as &$var) {
                    $var = array_merge($addOn, $var);
                }
                if ($this->db->insert_batch('suspended_items', $items)) {
                    return TRUE;
                }
            }

        }
        return FALSE;
    }

    public function deleteBill($id)
    {

        if ($this->db->delete('suspended_items', array('suspend_id' => $id)) && $this->db->delete('suspended_bills', array('id' => $id))) {
            return true;
        }

        return FALSE;
    }

    public function getInvoicePayments($bill_id)
    {
        $q = $this->db->get_where("payments", array('bill_id' => $bill_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }

        return FALSE;
    }

    function stripe($amount = 0, $card_info = array(), $desc = '')
    {
        $this->load->admin_model('stripe_payments');
        //$card_info = array( "number" => "4242424242424242", "exp_month" => 1, "exp_year" => 2016, "cvc" => "314" );
        //$amount = $amount ? $amount*100 : 3000;
        unset($card_info['type']);
        $amount = $amount * 100;
        if ($amount && !empty($card_info)) {
            $token_info = $this->stripe_payments->create_card_token($card_info);
            if (!isset($token_info['error'])) {
                $token = $token_info->id;
                $data = $this->stripe_payments->insert($token, $desc, $amount, $this->default_currency->code);
                if (!isset($data['error'])) {
                    $result = array('transaction_id' => $data->id,
                        'created_at' => date($this->dateFormats['php_ldate'], $data->created),
                        'amount' => ($data->amount / 100),
                        'currency' => strtoupper($data->currency)
                    );
                    return $result;
                } else {
                    return $data;
                }
            } else {
                return $token_info;
            }
        }
        return false;
    }

    function paypal($amount = NULL, $card_info = array(), $desc = '')
    {
        $this->load->admin_model('paypal_payments');
        //$card_info = array( "number" => "5522340006063638", "exp_month" => 2, "exp_year" => 2016, "cvc" => "456", 'type' => 'MasterCard' );
        //$amount = $amount ? $amount : 30.00;
        if ($amount && !empty($card_info)) {
            $data = $this->paypal_payments->Do_direct_payment($amount, $this->default_currency->code, $card_info, $desc);
            if (!isset($data['error'])) {
                $result = array('transaction_id' => $data['TRANSACTIONID'],
                    'created_at' => date($this->dateFormats['php_ldate'], strtotime($data['TIMESTAMP'])),
                    'amount' => $data['AMT'],
                    'currency' => strtoupper($data['CURRENCYCODE'])
                );
                return $result;
            } else {
                return $data;
            }
        }
        return false;
    }

    public function authorize($authorize_data)
    {
        $this->load->library('authorize_net');
        // $authorize_data = array( 'x_card_num' => '4111111111111111', 'x_exp_date' => '12/20', 'x_card_code' => '123', 'x_amount' => '25', 'x_invoice_num' => '15454', 'x_description' => 'References');
        $this->authorize_net->setData($authorize_data);

        if( $this->authorize_net->authorizeAndCapture() ) {
            $result = array(
                'transaction_id' => $this->authorize_net->getTransactionId(),
                'approval_code' => $this->authorize_net->getApprovalCode(),
                'created_at' => date($this->dateFormats['php_ldate']),
            );
            return $result;
        } else {
            return array('error' => 1, 'msg' => $this->authorize_net->getError());
        }
    }

    public function addPayment($payment = array(), $customer_id = null)
    {
        if (isset($payment['sale_id']) && isset($payment['paid_by']) && isset($payment['amount'])) {
            $payment['pos_paid'] = $payment['amount'];
            $inv = $this->getInvoiceByID($payment['sale_id']);
            $paid = $inv->paid + $payment['amount'];
            if ($payment['paid_by'] == 'ppp') {
                $card_info = array("number" => $payment['cc_no'], "exp_month" => $payment['cc_month'], "exp_year" => $payment['cc_year'], "cvc" => $payment['cc_cvv2'], 'type' => $payment['cc_type']);
                $result = $this->paypal($payment['amount'], $card_info);
                if (!isset($result['error'])) {
                    $payment['transaction_id'] = $result['transaction_id'];
                    $payment['date'] = $this->sma->fld($result['created_at']);
                    $payment['amount'] = $result['amount'];
                    $payment['currency'] = $result['currency'];
                    unset($payment['cc_cvv2']);
                    $this->db->insert('payments', $payment);
                    $paid += $payment['amount'];
                } else {
                    $msg[] = lang('payment_failed');
                    if (!empty($result['message'])) {
                        foreach ($result['message'] as $m) {
                            $msg[] = '<p class="text-danger">' . $m['L_ERRORCODE'] . ': ' . $m['L_LONGMESSAGE'] . '</p>';
                        }
                    } else {
                        $msg[] = lang('paypal_empty_error');
                    }
                }
            } elseif ($payment['paid_by'] == 'stripe') {
                $card_info = array("number" => $payment['cc_no'], "exp_month" => $payment['cc_month'], "exp_year" => $payment['cc_year'], "cvc" => $payment['cc_cvv2'], 'type' => $payment['cc_type']);
                $result = $this->stripe($payment['amount'], $card_info);
                if (!isset($result['error'])) {
                    $payment['transaction_id'] = $result['transaction_id'];
                    $payment['date'] = $this->sma->fld($result['created_at']);
                    $payment['amount'] = $result['amount'];
                    $payment['currency'] = $result['currency'];
                    unset($payment['cc_cvv2']);
                    $this->db->insert('payments', $payment);
                    $paid += $payment['amount'];
                } else {
                    $msg[] = lang('payment_failed');
                    $msg[] = '<p class="text-danger">' . $result['code'] . ': ' . $result['message'] . '</p>';
                }

            } elseif ($payment['paid_by'] == 'authorize') {
                $authorize_arr = array("x_card_num" => $payment['cc_no'], "x_exp_date" => ($payment['cc_month'].'/'.$payment['cc_year']), "x_card_code" => $payment['cc_cvv2'], 'x_amount' => $payment['amount'], 'x_invoice_num' => $inv->id, 'x_description' => 'Sale Ref '.$inv->reference_no.' and Payment Ref '.$payment['reference_no']);
                list($first_name, $last_name) = explode(' ', $payment['cc_holder'], 2);
                $authorize_arr['x_first_name'] = $first_name;
                $authorize_arr['x_last_name'] = $last_name;
                $result = $this->authorize($authorize_arr);
                if (!isset($result['error'])) {
                    $payment['transaction_id'] = $result['transaction_id'];
                    $payment['approval_code'] = $result['approval_code'];
                    $payment['date'] = $this->sma->fld($result['created_at']);
                    unset($payment['cc_cvv2']);
                    $this->db->insert('payments', $payment);
                    $paid += $payment['amount'];
                } else {
                    $msg[] = lang('payment_failed');
                    $msg[] = '<p class="text-danger">' . $result['msg'] . '</p>';
                }

            } else {
                if ($payment['paid_by'] == 'gift_card') {
                    $gc = $this->site->getGiftCardByNO($payment['cc_no']);
                    $this->db->update('gift_cards', array('balance' => ($gc->balance - $payment['amount'])), array('card_no' => $payment['cc_no']));
                } elseif ($customer_id && $payment['paid_by'] == 'deposit') {
                    $customer = $this->site->getCompanyByID($customer_id);
                    $this->db->update('companies', array('deposit_amount' => ($customer->deposit_amount-$payment['amount'])), array('id' => $customer_id));
                }
                unset($payment['cc_cvv2']);
                $this->db->insert('payments', $payment);
                $paid += $payment['amount'];
            }
            if (!isset($msg)) {
                if ($this->site->getReference('pay') == $data['reference_no']) {
                    $this->site->updateReference('pay');
                }
                $this->site->syncSalePayments($payment['sale_id']);
                return array('status' => 1, 'msg' => '');
            }
            return array('status' => 0, 'msg' => $msg);

        }
        return false;
    }

    public function addPrinter($data = array()) {
        if($this->db->insert('printers', $data)) {
            return $this->db->insert_id();
        }
        return false;
    }

    public function updatePrinter($id, $data = array()) {
        if($this->db->update('printers', $data, array('id' => $id))) {
            return true;
        }
        return false;
    }

    public function deletePrinter($id) {
        if($this->db->delete('printers', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }

    public function getPrinterByID($id) {
        $q = $this->db->get_where('printers', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllPrinters() {
        $q = $this->db->get('printers');
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    
    public function getAllTablesWithKitchen($kitchen_type){
		
		$current_date = date('Y-m-d');
		
        $this->db->select("orders.id, orders.biller_id, restaurant_tables.name as tablename, restaurant_tables.name table_name,orders.order_type, orders.order_status,orders.reference_no,users.username,orders.split_id,'order_items' ")
        ->join('kitchen_orders', 'orders.id = kitchen_orders.sale_id AND (kitchen_orders.chef_id = "'.$this->session->userdata('user_id').'" OR kitchen_orders.chef_id = 0) ')
		->join('order_items', 'order_items.sale_id = orders.id', 'inner')
        ->join('users', 'users.id = kitchen_orders.waiter_id')
        ->join('restaurant_tables', 'restaurant_tables.id = orders.table_id','left')
		->where('order_items.kitchen_type_id', $kitchen_type)
		//->join('sales', 'sales.sales_split_id = orders.split_id', 'inner')
		->where('orders.order_status', 'Open')
		->where('order_items.order_item_cancel_status', 0)
		->where('orders.order_cancel_status', 0)
		
		->where('DATE(date)', $current_date)
		->where_in('order_items.item_status', array('Inprocess','Preparing'))
		->where('orders.warehouse_id', $this->session->userdata('warehouse_id'))
		->group_by('orders.id');
		$t = $this->db->get('orders');

        if ($t->num_rows() > 0) {

           foreach ($t->result() as $row) {

           $this->db->select("order_items.sale_id,order_items.id, order_items.recipe_id, order_items.recipe_name,order_items.item_status as status,order_items.quantity, order_items.addon_id, order_items.buy_id, order_items.buy_quantity, order_items.get_item, order_items.get_quantity, order_items.total_get_quantity, recipe.image, recipe.khmer_name, order_items.time_started,recipe.preparation_time")
                ->join('orders', 'order_items.sale_id = orders.id')
				->join('recipe', 'recipe.id = order_items.recipe_id', 'left')
                ->join('kitchen_orders', 'kitchen_orders.sale_id = orders.id')
				->where('order_items.sale_id', $row->id)
				//->where('order_items.order_item_cancel_status', 0)
				->where('order_items.kitchen_type_id', $kitchen_type)
				->where('DATE(date)', $current_date)
				->where('orders.warehouse_id', $this->session->userdata('warehouse_id'));
				//->where_in('order_items.item_status', array('Inprocess','Preparing'))
				//->or_where('order_items.item_status', 'Preparing');
				
				//->group_by('order_items.sale_id');
				
                $s = $this->db->get('order_items');
              
                if ($s->num_rows() > 0) {

                        foreach ($s->result() as $sow) {

                             $split[$row->id][] = $sow;                                             
                        }  

                    $row->order_items = $split[$row->id];
                }else{
                    $row->order_items = array();
                }
                
                $data[] = $row;
            }

			
            return $data;
        }
        
        return FALSE;
    }
	
	 public function getorderKitchenprint($kitchen_type, $order_id = NULL){
		
		$current_date = date('Y-m-d');
		
        $this->db->select("orders.id, orders.biller_id, restaurant_tables.name as tablename, restaurant_tables.name table_name,orders.order_type, orders.order_status,orders.reference_no,users.username,orders.split_id,'order_items' ")
        ->join('kitchen_orders', 'orders.id = kitchen_orders.sale_id AND (kitchen_orders.chef_id = "'.$this->session->userdata('user_id').'" OR kitchen_orders.chef_id = 0) ')
		->join('order_items', 'order_items.sale_id = orders.id', 'inner')
        ->join('users', 'users.id = kitchen_orders.waiter_id')
        ->join('restaurant_tables', 'restaurant_tables.id = orders.table_id','left')
		->where('order_items.kitchen_type_id', $kitchen_type)
		//->join('sales', 'sales.sales_split_id = orders.split_id', 'inner')
		->where('orders.order_status', 'Open')
		->where('order_items.order_item_cancel_status', 0)
		->where('orders.order_cancel_status', 0)
		
		->where('DATE(date)', $current_date)
		->where('orders.id', $order_id)
		->where_in('order_items.item_status', array('Inprocess','Preparing'))
		->where('orders.warehouse_id', $this->session->userdata('warehouse_id'))
		->group_by('orders.id');
		$t = $this->db->get('orders');

        if ($t->num_rows() > 0) {

           foreach ($t->result() as $row) {

           $this->db->select("order_items.sale_id,order_items.id, order_items.recipe_id, order_items.recipe_name,order_items.item_status as status,order_items.quantity, order_items.addon_id, order_items.buy_id, order_items.buy_quantity, order_items.get_item, order_items.get_quantity, order_items.total_get_quantity, recipe.image, recipe.khmer_name, order_items.time_started,recipe.preparation_time")
                ->join('orders', 'order_items.sale_id = orders.id')
				->join('recipe', 'recipe.id = order_items.recipe_id', 'left')
                ->join('kitchen_orders', 'kitchen_orders.sale_id = orders.id')
				->where('order_items.sale_id', $row->id)
				//->where('order_items.order_item_cancel_status', 0)
				->where('order_items.kitchen_type_id', $kitchen_type)
				->where('DATE(date)', $current_date)
				->where('orders.warehouse_id', $this->session->userdata('warehouse_id'));
				//->where_in('order_items.item_status', array('Inprocess','Preparing'))
				//->or_where('order_items.item_status', 'Preparing');
				
				//->group_by('order_items.sale_id');
				
                $s = $this->db->get('order_items');
              
                if ($s->num_rows() > 0) {

                        foreach ($s->result() as $sow) {

                             $split[$row->id][] = $sow;                                             
                        }  

                    $row->order_items = $split[$row->id];
                }else{
                    $row->order_items = array();
                }
                
                $data[] = $row;
            }

			
            return $data;
        }
        
        return FALSE;
    }
	
    public function updateKitchenstatus($notification_array, $status, $order_id, $order_item_id, $current_status, $user_id,$timelog_array){

		$q = $this->db->select('reference_no, table_id, customer_id')->where('id', $order_id)->get('orders');
		
		if ($q->num_rows() > 0) {
            $order_number =  $q->row('reference_no');
			$table_id =  $q->row('table_id');
			$customer_id =  $q->row('customer_id');
        }
		
		$k = $this->db->select('waiter_id')->where('sale_id', $order_id)->get('kitchen_orders');
		if ($k->num_rows() > 0) {
            $waiter_id =  $k->row('waiter_id');
        }
		
		$notification_array['insert_array']['msg'] = 'The order ['.$order_number.'] item has been '.$current_status.'.';
		$notification_array['insert_array']['to_user_id'] = $waiter_id;
		$notification_array['insert_array']['table_id'] = $table_id;
		
		$notification_array['customer_msg'] = 'The order ['.$order_number.'] item has been '.$current_status.'.';
		$notification_array['customer_id'] = $customer_id;
		
		$this->site->create_notification($notification_array);

		$order_item_id = explode(',', $order_item_id);

		$q = $this->db->where('kitchen_orders.sale_id', $order_id);
		$q = $this->db->update('kitchen_orders', array('chef_id' => $user_id, 'status' => 'Booked'));
		
        $kitchen_array = array(
            'item_status' => $current_status	    
        );
	if($current_status=="Ready"):
	    $kitchen_array['time_end'] = date('Y-m-d H:i:s');	
	endif;
		foreach($order_item_id as $item_id){
			
				$timelog_array['item_id'] = $item_id;
				$this->db->insert('time_log',  $timelog_array);
			}


		if(!empty($order_item_id)){
			foreach($order_item_id as $item_id){
				$this->db->where('order_item_cancel_status', 0);
				$this->db->where('id', $item_id);
				$this->db->update('order_items',  $kitchen_array);
			}
			return true;
		}
        return false;
    }


   /* public function getTodayCCSales()
    {
        $date = date('Y-m-d 00:00:00');
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cc_slips, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('paid_by', 'CC');

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }*/


   /*public function updateKitchenstatus($status, $order_item_id, $current_status, $user_id){
        $kitchen_array = array(
            'item_status' => $current_status,
        );        
       $this->db->where('id', $order_item_id);
        if ($this->db->update('order_items',  $kitchen_array)) {
            return true;
        }
        return false;
    }*/

    public function getTableSplitCount($split_id)
    {
          
        $this->db->select('orders.id')
         ->join('order_items', 'orders.id=order_items.sale_id');
        $split_count = $this->db->get_where('orders', array('orders.split_id' => $split_id,'order_items.order_item_cancel_status' => 0));

	
      $this->db->select('orders.id')
         ->join('order_items', 'orders.id=order_items.sale_id');
        $split_closed_count = $this->db->get_where('orders', array('orders.split_id' => $split_id,'order_items.item_status' => 'Closed','order_items.order_item_cancel_status' => 0));
		
		  
		$order_array = array(
            'order_status' => "Closed",
        );       
        if ($split_count->num_rows()  == $split_closed_count->num_rows()) {
			
			$this->db->where('split_id', $split_id);
			$o = $this->db->update('orders', $order_array);
					
            return TRUE;
			
        }
        else{
            return FALSE;
        }
        
		
        return FALSE; 
    }
	
	public function getTableOrderCount($order_id)
    {
          
        $this->db->select('orders.id')
         ->join('order_items', 'orders.id=order_items.sale_id');
        $order_count = $this->db->get_where('orders', array('orders.id' => $order_id,'order_items.order_item_cancel_status' => 0));

	
      $this->db->select('orders.id')
         ->join('order_items', 'orders.id=order_items.sale_id');
        $order_closed_count = $this->db->get_where('orders', array('orders.id' => $order_id,'order_items.item_status' => 'Closed','order_items.order_item_cancel_status' => 0));
		
		  
		$order_array = array(
            'order_status' => "Closed",
        );       
        if ($order_count->num_rows()  == $order_closed_count->num_rows()) {
			
			$this->db->where('id', $order_id);
			$o = $this->db->update('orders', $order_array);
					
            return TRUE;
			
        }
        else{
            return FALSE;
        }
        
		
        return FALSE; 
    }

    public function updateOrderstatus($status, $order_item_id, $current_status, $user_id, $notification_array,$timelog_array){
		
	
		$order_item_id = explode(',', $order_item_id);
		
	
		$this->db->select('orders.reference_no, orders.customer_id, orders.table_id, order_items.sale_id')
         ->join('orders', 'orders.id = order_items.sale_id', 'left');
        $q = $this->db->get_where('order_items', array('order_items.id' => $order_item_id[0]));
		
		
		
		if ($q->num_rows() > 0) {
            $order_number =  $q->row('reference_no');
			$table_id =  $q->row('table_id');
			$sale_id =  $q->row('sale_id');
			$customer_id = $q->row('customer_id');
        }
		
		$k = $this->db->select('chef_id')->where('sale_id', $sale_id)->get('kitchen_orders');
		if ($k->num_rows() > 0) {
            $chef_id =  $k->row('chef_id');
        }
		
		$notification_array['customer_msg'] = 'The order ['.$order_number.'] item has been '.$current_status.'.';
		$notification_array['customer_id'] = $customer_id;
		
		$notification_array['insert_array']['msg'] = 'The order ['.$order_number.'] item has been '.$current_status.'.';
		$notification_array['insert_array']['to_user_id'] = $chef_id;
		$notification_array['insert_array']['table_id'] = $table_id;
		
		$this->site->create_notification($notification_array);
		
        $order_item_array = array(
            'item_status' => $current_status,
        );

        $order_itemendtime = array(
        	//'time_end' => date('Y-m-d H:m:s'),
        );
		
		if($current_status = 'Served')
		{
		//foreach($order_item_id as $item_id){
		//		$this->db->where('id', $item_id);
		//		$this->db->update('order_items',  $order_itemendtime);
		//	}
		}	
		foreach($order_item_id as $item_id){
				$timelog_array['item_id'] = $item_id;
				$this->db->insert('time_log',  $timelog_array);
			}

		if(!empty($order_item_id)){
			foreach($order_item_id as $item_id){
				$this->db->where('id', $item_id);
				$this->db->update('order_items',  $order_item_array);
			}
			return true;
		}


        return false;
    }
    public function CancelOrdersItem($notification_array, $cancel_remarks, $order_item_id, $user_id, $split_id,$timelog_array,$cancelQty){
		$q = $this->db->select('*')->where('id', $order_item_id)->get('order_items');
		if ($q->num_rows() > 0) {
            $sale_id =  $q->row('sale_id');
        }
		
		$k = $this->db->select('waiter_id, chef_id')->where('sale_id', $sale_id)->get('kitchen_orders');
		if ($k->num_rows() > 0) {
            $waiter_id =  $k->row('waiter_id');
			$chef_id =  $k->row('chef_id');
        }
		
		if(!empty($split_id)){
			 $notification_array['insert_array']['role_id'] = KITCHEN;
			 $notification_array['insert_array']['to_user_id'] = $waiter_id;
		 }else{
			 $notification_array['insert_array']['role_id'] = WAITER;
			 $notification_array['insert_array']['to_user_id'] = $chef_id;
		 }
		
		
		$this->site->create_notification($notification_array);
		
        $order_item_array = array(
            'order_item_cancel_id' => $user_id,
            'order_item_cancel_note' => $cancel_remarks,
            'order_item_cancel_status' => 1,
            //'item_status' => 'Cancel',
	    //'quantity' => $q->row('quantity')-$cancelQty
        );
	if($cancelQty!='all'){ //cancel item
	    $insertNew = $q->row();
	    $insertNew->quantity = $cancelQty;
	    $insertNew->item_status = 'Cancel';
		$insertNew->order_item_cancel_status = 1;
	    unset($insertNew->id);
	    $this->db->insert('order_items',  $insertNew);
	    $order_item_array = array(
		'quantity' => $q->row('quantity')-$cancelQty
		);
	}else{
	    $order_item_array = array(
		'item_status' => 'Cancel',
		'order_item_cancel_status' => 1
		);
	}
        $this->db->insert('time_log',  $timelog_array);

		$this->db->where('id', $order_item_id);
		if ($this->db->update('order_items',  $order_item_array)) {
			$order = $this->db->select('order_items.order_item_cancel_status')
			->join('order_items', 'order_items.sale_id = orders.id')
			->where('orders.split_id', $split_id)
			->where('order_items.order_item_cancel_status', 0)
			->get('orders');
			
			if($order->num_rows() == 0){
				$this->db->where('orders.split_id', $split_id);
				$orderupdate = $this->db->update('orders', array( 'order_cancel_id' => $user_id, 'order_cancel_note' => 'All item order cancel', 'order_cancel_status' => 1));
			}			
			return true;
		}
		return false;

    }


    public function CancelSale($cancel_remarks, $sale_id, $user_id, $notification_array){

    	$q = $this->db->select('sales_split_id, sales_table_id')->where('id', $sale_id)->get('sales');
		if ($q->num_rows() > 0) {
            $split_id =  $q->row('sales_split_id');
			$table_id =  $q->row('sales_table_id');
        }
        /*echo $split_id;*/
		
		$k = $this->db->select('id ')->where('split_id', $split_id)->get('orders');
		if ($k->num_rows() > 0) {
            $id =  $k->row('id');
        }
		
		$k = $this->db->select('waiter_id, chef_id')->where('sale_id', $id)->get('kitchen_orders');
		if ($k->num_rows() > 0) {
            $waiter_id =  $k->row('waiter_id');
			$chef_id =  $k->row('chef_id');
        }
		
		$notification_array['insert_array']['msg'] = 'Cashier has benn cancel this bil ('.$split_id.')';
		$notification_array['insert_array']['table_id'] = $table_id;
		$notification_array['insert_array']['type'] = 'Cashier cancel bils';
		 $notification_array['insert_array']['role_id'] = WAITER;
		 $notification_array['insert_array']['to_user_id'] = $waiter_id;
		
		
		
		$this->site->create_notification($notification_array);
        /*echo $id;die;*/
        $sale_aray = array(
            'canceled_user_id' => $user_id,
            'cancel_remarks' => $cancel_remarks,
            'cancel_status' => 1,
        );
        $order_item_array = array(
            'order_item_cancel_id' => $user_id,
            'order_item_cancel_note' => $cancel_remarks,
            'order_item_cancel_status' => 1,
        );

        $order_array = array(
            'order_cancel_id' => $user_id,
            'order_cancel_note' => $cancel_remarks,
            'order_cancel_status' => 1,
            'payment_status' => 'Cancelled',
        );
 	
         $bill_array = array(
            'updated_by' => $user_id,
            'updated_at' => date('Y-m-d H:i:s'),
            'payment_status' => 'Cancelled',
            'bil_status' => 'Cancelled',
        );

		$this->db->update('bils', $bill_array, array('sales_id' => $sale_id));
	    $this->db->update('orders', $order_array, array('id' => $id));

		$this->db->update('order_items', $order_item_array, array('sale_id' => $id));
			
		$this->db->where('id', $sale_id);
		if ($this->db->update('sales',  $sale_aray)) {
			/*if(!empty($id)){

			}*/
			return true;
		}
		return false;

    }

	 public function CancelWaiterOrders($order_id, $user_id){
        $order_array = array(
            'order_cancel_id' => $user_id,
            'order_cancel_note' => 'Waiter cancel',
            'order_cancel_status' => 1,
        );
        
		$this->db->where('id', $order_id);
		if ($this->db->update('orders',  $order_array)) {
			return true;
		}
		return false;

    }
	
    public function InsertBill($order_data = array(), $order_item = array(), $billData = array(), $splitData = array(), $sales_total = NULL, $delivery_person = NULL,$timelog_array = NULL, $notification_array = array(),$order_item_id =array())
    {		
    	
    	$sales_array = array(
		            'grand_total' => $sales_total,
					'delivery_person_id' => $delivery_person
		        );
		
		$this->site->create_notification($notification_array);
    	foreach ($timelog_array as $time) {
              	$res = $this->db->insert('time_log', $time);
        }      	
    	
		
		
        if ($this->db->insert('sales', $order_data)) {
            $sale_id = $this->db->insert_id();
            
            $this->db->update('sales', $sales_array, array('id' => $sale_id));

              foreach ($billData as $key =>  $bills) {
              	$bills['sales_id'] = $sale_id;
		$bills['table_whitelisted'] = $this->isTableWhitelisted($order_data['sales_table_id']);
              	$this->db->insert('bils', $bills);
					$bill_id = $this->db->insert_id();
					//$bill_number = sprintf("%'.05d", $bill_id);
		   $bill_number = $this->site->generate_bill_number($bills['table_whitelisted']);
              	   $this->db->update('bils', array('bill_number' => $bill_number), array('id' => $bill_id));
				  
				foreach ($splitData[$key]  as $bill_items) {
					$bill_items['bil_id'] = $bill_id;
					$this->db->insert('bil_items', $bill_items);
				}
              }

            foreach ($order_item as $item) {
                $this->db->insert('sale_items', $item);
                $sale_item_id = $this->db->insert_id();
                $this->db->update('sale_items', array('sale_id' =>  $sale_id), array('id' => $sale_item_id));

            }
			$kitchen_array = array(
                      'item_status' => 'Closed',	    
                      'time_end' => date('Y-m-d H:i:s'),	    
			        );
				
			if(!empty($order_item_id)){
						foreach($order_item_id as $item_id){
							$this->db->where('order_item_cancel_status', 0);
							$this->db->where('order_item_cancel_status', 'Inprocess');
							$this->db->where('id', $item_id);
							$this->db->update('order_items',  $kitchen_array);
						}
						
					}
           
            if ($order_data['sale_status'] == 'completed') {
            }
                     
            return true;
        }
        return false;
    } 
	
    public function Payment($update_bill = array(), $bill_id =NULL, $payment = array(), $multi_currency = array(), $salesid = NULL, $sales_bill = array(), $order_split_id = NULL, $notification_array,$updateCreditLimit)
    {      
	
		$q = $this->db->select('sales_split_id, sales_table_id')->where('id', $salesid)->get('sales');

		$bilno = $this->db->select('bill_number')->where('id', $bill_id)->get('bils');
		
		$bill_no = $bilno->row('bill_number');

		if ($q->num_rows() > 0) {
            $split_id =  $q->row('sales_split_id');
			$table_id =  $q->row('sales_table_id');
        }
		$k = $this->db->select('id ')->where('split_id', $split_id)->get('orders');
		if ($k->num_rows() > 0) {
            $id =  $k->row('id');
        }
		
		$k = $this->db->select('waiter_id, chef_id')->where('sale_id', $id)->get('kitchen_orders');
		if ($k->num_rows() > 0) {
            $waiter_id =  $k->row('waiter_id');
			$chef_id =  $k->row('chef_id');
        }
		
		$notification_array['insert_array']['msg'] = 'Cashier has been payment this bil ('.$bill_no.'';
		$notification_array['insert_array']['table_id'] = $table_id;
		$notification_array['insert_array']['type'] = 'Cashier payment bils';
		 $notification_array['insert_array']['role_id'] = WAITER;
		 $notification_array['insert_array']['to_user_id'] = $waiter_id;
		
		
		
		$this->site->create_notification($notification_array);
		
    	if ($this->db->update('bils', $update_bill, array('id' => $bill_id))){
			$this->db->update('sales', $sales_bill, array('id' => $salesid));
    			$order_count = $this->db->get_where('bils', array('sales_id' => $salesid));
				$order_count =$order_count->num_rows();
				
				 $order_closed_count = $this->db->get_where('bils', array('bils.sales_id' => $salesid,'bils.payment_status' => 'Completed'));
				 $order_closed_count =$order_closed_count->num_rows();

    		foreach ($payment as $item) {
		    $item['customer_payment_type'] = $updateCreditLimit['customer_type'];
		    $this->db->insert('payments', $item);
		    $pid = $this->db->insert_id();
		    if($pid && $item['paid_by']=='credit'){
			$creditedAmt = $item['pos_paid'];
			$d_q = $this->db->get_where('deposits', array('company_id' => $updateCreditLimit['company_id'],'credit_balance!='=>0))->result_array();
			$amountpayable = $item['pos_paid'];
			foreach($d_q as $dep => $depositRow){			    
			    if($amountpayable<=$depositRow['credit_balance']){
				$payableamt = $amountpayable;
				$this->db->set('credit_balance', 'credit_balance-'.$payableamt,false);
				$this->db->set('credit_used', 'credit_used+'.$payableamt,false);
				$this->db->where('id',$depositRow['id']);
				$this->db->update('deposits');//echo 'exit';exit;
				$amountpayable =0;
				break;
			    }else{
				$payableamt = $depositRow['credit_balance'];
				$this->db->set('credit_balance', 'credit_balance-'.$payableamt,false);
				$this->db->set('credit_used', 'credit_used+'.$payableamt,false);
				$this->db->where('id',$depositRow['id']);
				$this->db->update('deposits');
				$amountpayable = $amountpayable-$payableamt;
				
			    }
			}
			if($updateCreditLimit['customer_type']=="postpaid") {
			    if($amountpayable>0){
				$date = date('Y-m-d H:i:s');
				$deposit_data = array(
				    'date' => $date,
				    'credit_amount' => $amountpayable,
				    'credit_used' => $amountpayable,
				    'paid_by' => 'postpaid',
				    'company_id' => $updateCreditLimit['company_id'],
				    'created_by' => $this->session->userdata('user_id'),
				    'added_on' => date('Y-m-d H:i:s'),
				);
				if ($this->db->insert('deposits', $deposit_data)) {
				    $this->db->set('credit_limit', 'credit_limit+'.$deposit_data['credit_amount'],false);
					$this->db->where('id',$deposit_data['company_id']);
					$this->db->update('companies');
				}
			    }
			    $com = $this->db->get_where('companies', array('id' => $updateCreditLimit['company_id']))->row_array();
			    $postpaid_bill['company_id'] = $updateCreditLimit['company_id'];
			    $postpaid_bill['credit_amount'] = $creditedAmt;
			    $postpaid_bill['amount_payable'] = $creditedAmt;
			    $postpaid_bill['bill_id'] = $bill_id;
			    $postpaid_bill['created_on'] = date('Y-m-d H:i:s');
			    $postpaid_bill['due_date'] = date('Y-m-d H:i:s',strtotime('+'.$com['credit_days'].' days', strtotime(date('Y-m-d H:i:s'))));		 $postpaid_bill['status'] = 9;
			    $this->db->insert('companies_postpaid_bills', $postpaid_bill);
			    $this->db->insert_id();
			}
			$this->db->set('credit_limit', 'credit_limit-'.$creditedAmt,false);
			$this->db->where('id',$updateCreditLimit['company_id']);
			$this->db->update('companies');//echo 'exit';exit;
       
		    }
    		
    		}
			foreach ($multi_currency as $currency) {
    			$this->db->insert('sale_currency', $currency);
    		}
    		 
	    		$sales_array = array(
		            'sale_status' => "Closed",
		            'payment_status' => "Paid",
		        );

		        $tables_array = array(
		            'session_end' => date('Y-m-d H:m:s'),
		        );   


			    if ($order_count  == $order_closed_count) {
			    	
			         $this->db->update('sales', $sales_array, array('id' => $salesid));
			         $this->db->update('orders', $sales_array, array('split_id' =>  $order_split_id));

			        $res =  $this->db->update('restaurant_table_sessions', $tables_array, array('split_id' =>  $order_split_id));
			       
		        }
		        
    	 return true;
    	}    	
    	return false;
    }   

    public function getAllBillitems($id =NULL)
    {
    	 $Billitems = "SELECT BI.recipe_name,BI.net_unit_price,SUM(BI.quantity) AS quantity,SUM(BI.subtotal) AS subtotal,R.khmer_name
                    FROM ".$this->db->dbprefix('bil_items')." AS BI
                    JOIN ". $this->db->dbprefix('recipe') ." AS R ON R.id = BI.recipe_id
                    WHERE BI.bil_id='".$id."' GROUP BY BI.recipe_name ";
            
        $q = $this->db->query($Billitems);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    } 

	/*public function getAllBillitems($id =NULL)
      {
		$this->db->select('bil_items.*, recipe.khmer_name')
		->join('recipe', 'recipe.id = bil_items.recipe_id')
		->where('bil_items.bil_id', $id);
		$q = $this->db->get('bil_items');*/
       // $q = $this->db->get_where('bil_items', array('bil_id' => $id));

        /*if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }*/
    public function get_BillData($id) {
    $this->db->select("bils.*,tax_rates.name as tax_name, tax_rates.rate as tax_rate,restaurant_tables.name table_name,orders.order_type")
    ->join('tax_rates', 'tax_rates.id = bils.tax_id','left')
    ->join('sales', 'sales.id = bils.sales_id')
    ->join('orders', 'orders.split_id = sales.sales_split_id')
    ->join('restaurant_tables', 'restaurant_tables.id = orders.table_id','left')
    ->where('bils.id', $id);
	$q = $this->db->get('bils');

       /*$q = $this->db->get_where('bils', array('id' => $id));*/
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }   

    public function getBillData($sale_id){

        $this->db->select("sales.*");     

        $q = $this->db->get_where('sales', array('sales.id' => $sale_id));

        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = $row;
            }
            
            return $data;
        }
       
        return FALSE;
    }
	
	public function getdaysummary($start,$end)
    {	
        $this->db->select('SUM(COALESCE(total, 0)) as total,SUM(COALESCE(grand_total, 0)) as total_amount1, SUM(COALESCE(total_tax, 0)) as total_tax, SUM(COALESCE(total_discount, 0)) as total_discount,SUM(total-total_discount+CASE WHEN (tax_type= 1) THEN total_tax ELSE 0 END) as total_amount, COUNT(' . $this->db->dbprefix('bils') . '.id) as totalbill,SUM(total-total_discount+CASE WHEN (tax_type= 1) THEN total_tax ELSE 0 END) as gross_amt,SUM(total-CASE WHEN (tax_type= 0) THEN total_tax ELSE 0 END) as netamt', FALSE)
		->where('payment_status', 'Completed')
		->where('DATE(date) >=', $start)
		->where('DATE(date) <=', $end);
        $q = $this->db->get('bils');
/*print_r($q);die;*/
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

	public function getCollection($start,$end)
    {
    	if($start == ''){
    		$start = date('d-m-Y');
    	}
    	if($end == ''){
    		$end = date('d-m-Y');
    	}
    	
		$default_currency = $this->Settings->default_currency;

		$billQuery = "SELECT  GROUP_CONCAT(id) as id FROM " . $this->db->dbprefix('bils') . " 
 		 WHERE payment_status ='Completed' AND DATE(date) BETWEEN '".$start."' AND '".$end."' ";
 		 
        $q = $this->db->query($billQuery);
        
        if ($q->num_rows() > 0) {
        	$bill_ids = $q->row()->id;
        	
        if($bill_ids){	
 		$myQuery = "SELECT  SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id = ".$default_currency.")) THEN SC.amount ELSE 0 END) AS Cash,SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id != ".$default_currency.")) THEN amount_exchange*currency_rate ELSE 0 END) as For_Exto_usd,SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id != ".$default_currency.")) THEN SC.amount ELSE 0 END) as For_Ex,SUM(DISTINCT CASE WHEN PM.paid_by = 'CC' THEN PM.amount ELSE 0 END) AS Credit_Card,SUM( DISTINCT P.balance) AS return_balance FROM " . $this->db->dbprefix('sale_currency') . " SC
 		JOIN " . $this->db->dbprefix('bils') . " P ON P.id = SC.bil_id
 		JOIN " . $this->db->dbprefix('payments') . " PM ON PM.bill_id = P.id
 		 WHERE P.payment_status ='Completed'AND SC.bil_id IN (".$bill_ids.")";
		/*echo $myQuery;die;*/
        $p = $this->db->query($myQuery);

         if ($p->num_rows() > 0) {
            return $p->row();
        }
    }
    return FALSE;
}
   return FALSE;     
    }    
public function getItemSaleReports($start,$end){

     $this->db->select("recipe_categories.id AS cate_id,recipe_categories.name as category,SUM(" . $this->db->dbprefix('bils') . ".grand_total) AS grand_total,SUM(" . $this->db->dbprefix('bils') . ".round_total) AS round_total, 'split_order'")
        ->join('recipe', 'recipe.category_id = recipe_categories.id')
        ->join('bil_items', 'bil_items.recipe_id = recipe.id')
        ->join('bils', 'bils.id = bil_items.bil_id')
        ->where('recipe_categories.parent_id', NULL)
        ->or_where('recipe_categories.parent_id',0);
                
        $this->db->group_by('recipe_categories.id');        
        $t = $this->db->get('recipe_categories');      
        
        if ($t->num_rows() > 0) {
            
            foreach ($t->result() as $row) {
                    $this->db->select("recipe_categories.id AS sub_id,recipe_categories.name AS sub_category,bils.total_tax, 'order'")
                    ->join('recipe', 'recipe.subcategory_id = recipe_categories.id')
                    ->join('bil_items', 'bil_items.recipe_id = recipe.id')
                    ->join('bils', 'bils.id = bil_items.bil_id')
                    ->where('recipe.category_id', $row->cate_id);
                    $this->db->group_by('recipe.subcategory_id');
                    
                    $s = $this->db->get('recipe_categories');
                if ($s->num_rows() > 0) {
                        
                        foreach ($s->result() as $sow) {
                                
                                /*$this->db->select('recipe.name,SUM(' . $this->db->dbprefix('bil_items') . '.item_discount) as item_discount,SUM(' . $this->db->dbprefix('bil_items') . '.off_discount) as off_discount,SUM(' . $this->db->dbprefix('bil_items') . '.input_discount) as input_discount,SUM(' . $this->db->dbprefix('bil_items') . '.tax) as tax,SUM(' . $this->db->dbprefix('bil_items') . '.quantity) as quantity,SUM(' . $this->db->dbprefix('bil_items') . '.subtotal) as subtotal')
                                    ->join('recipe', 'recipe.id = bil_items.recipe_id')
                                    ->join('bils', 'bils.id = bil_items.bil_id')
                                    ->where('DATE(date) >=', $start)
                                    ->where('DATE(date) <=', $end)
                                    ->where('recipe.subcategory_id', $sow->sub_id);
                                    $this->db->group_by('recipe.id');
                                   
                                    $o = $this->db->get('bil_items');  */

								$myQuery = "SELECT R.name,SUM(BI.item_discount) AS item_discount,SUM(BI.off_discount) AS off_discount,SUM(BI.input_discount) AS input_discount,SUM(CASE WHEN (BI.tax_type= 1) THEN BI.tax ELSE 0 END) as tax,SUM(BI.quantity) AS quantity,SUM(BI.subtotal) AS subtotal
								FROM " . $this->db->dbprefix('bil_items') . " BI
								JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
								JOIN " . $this->db->dbprefix('bils') . " B ON B.id = BI.bil_id
								WHERE DATE(B.date) BETWEEN '".$start."' AND '".$end."' AND
								R.subcategory_id =".$sow->sub_id." 
								GROUP BY R.id ";
								$o = $this->db->query($myQuery);

                                $split[$row->cate_id][] = $sow;
                                if ($o->num_rows() > 0) {                                    
                                    foreach($o->result() as $oow){
                                        $order[$sow->sub_id][] = $oow;
                                    }
                                }
                                $sow->order = $order[$sow->sub_id];                   
                        }                    
                    $row->split_order = $split[$row->cate_id];
                }else{
                    $row->split_order = array();
                }                
                $data[] = $row;

            }            
            return $data;
        }        
        return FALSE;   
    }

public function getRoundamount($start,$end)
    {
        $round = "SELECT SUM(P.grand_total - round_total) AS round
        FROM " . $this->db->dbprefix('bils') . " AS P
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
             P.payment_status ='Completed' ";
            
        $q = $this->db->query($round);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    } 
public function getItemSaleReports_new($start,$end){

	$this->db->select("recipe_categories.id AS cate_id,recipe_categories.name as category, 'split_order'")
		->join('recipe', 'recipe.category_id = recipe_categories.id')
		->join('bil_items', 'bil_items.recipe_id = recipe.id')
		->join('bils', 'bils.id = bil_items.bil_id')
		->where('recipe_categories.parent_id', NULL)
		->or_where('recipe_categories.parent_id',0);
		$this->db->group_by('recipe_categories.id');
		$t = $this->db->get('recipe_categories');    
        
        if ($t->num_rows() > 0) {
        	/*echo "<pre>";
        	print_r($t->result());*/
            foreach ($t->result() as $row) {
					$this->db->select("recipe_categories.id AS sub_id,recipe_categories.name AS sub_category , 'order'")
					->join('recipe', 'recipe.subcategory_id = recipe_categories.id')
					->join('bil_items', 'bil_items.recipe_id = recipe.id')
					->join('bils', 'bils.id = bil_items.bil_id')
					->where('recipe.category_id', $row->cate_id);
					$this->db->group_by('recipe.subcategory_id');
					$s = $this->db->get('recipe_categories');
                if ($s->num_rows() > 0) {
                        
                        foreach ($s->result() as $sow) {
                                
									$this->db->select('recipe.name,SUM(COALESCE(subtotal, 0)) as total,SUM(' . $this->db->dbprefix('bil_items') . '.quantity) as quantity')
									->join('recipe', 'recipe.id = bil_items.recipe_id')
									->join('bils', 'bils.id = bil_items.bil_id')
									->where('DATE(date) >=', $start)
									->where('DATE(date) <=', $end)
									->where('recipe.subcategory_id', $sow->sub_id);
									$this->db->group_by('recipe.id');
									$o = $this->db->get('bil_items');                               
                                $split[$row->cate_id][] = $sow;
                                if ($o->num_rows() > 0) {
                                    
                                    foreach($o->result() as $oow){
                                        $order[$sow->sub_id][] = $oow;
                                    }
                                }
                                $sow->order = $order[$sow->sub_id];                   
                        }
                    
                    
                    $row->split_order = $split[$row->cate_id];
                }else{
                    $row->split_order = array();
                }
                
                $data[] = $row;

            }
            
            return $data;
        }
        
        return FALSE;
	}	
	
	
  public function getCashierReport($start,$end)
    {
        $myQuery = "SELECT U.username,SUM(P.total-P.total_discount+CASE WHEN (P.tax_type= 1) THEN total_tax ELSE 0 END) AS grand_total
        FROM srampos_bils  P
            LEFT JOIN srampos_users  U
            ON P.created_by = U.id
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
             P.payment_status ='Completed' 
            GROUP BY U.username";
            
        $q = $this->db->query($myQuery);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

public function getOrderitemDetsils($id){

	$myQuery = "SELECT OI.recipe_name,O.reference_no,O.created_by,T.name,OI.id,T.id AS table_id
        FROM " . $this->db->dbprefix('order_items') . " AS OI
           JOIN " . $this->db->dbprefix('orders') . "  AS O
            ON O.id = OI.sale_id
            JOIN " . $this->db->dbprefix('restaurant_tables') . " AS T
            ON T.id = O.table_id
            WHERE OI.order_item_cancel_status = 0 AND OI.id=".$id."";
            /*echo $myQuery;die;*/
        $q = $this->db->query($myQuery);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;

	}
    function getCustomerDiscount($billid)
    {
	
	$this->db
	->select('P.id bil_id,P.tax_type,P.tax_id,P.total,P.customer_discount_id,P.customer_discount_status,P.total_discount,P.grand_total,D.*')
	->from('bils P')
	->join('diccounts_for_customer D','D.id=P.customer_discount_id','left')
	->where('P.id',$billid)
	->where('P.customer_discount_status','pending');
        $q = $this->db->get();
	//print_r($q->row_array());exit;
        if ($q->num_rows() > 0) {
	    $result = $q->row();    
            return $result;
        }
        return FALSE;
    }
    function updateCustomerDiscount($billid,$dis_id){
	if($dis_id!=0) $update_bil['customer_discount_id'] = $dis_id;
	if($dis_id!=0) $update_bil['customer_discount_status'] = 'pending';
	if($dis_id==0) $update_bil['customer_discount_status'] = 'no_discount';
	$this->db->where('id', $billid);
        $this->db->update('bils', $update_bil);
    }
    function getAllCustomerDiscount()
    {
	
	$q = $this->db->get('diccounts_for_customer');
        if ($q->num_rows() > 0) {
	    $result = $q->result();    
            return $result;
        }
        return FALSE;
    }
    public function getBillItemsRecipeID($billid){
    	$myQuery = "SELECT BI.id,BI.recipe_id,sum(BI.subtotal-BI.item_discount-BI.off_discount) AS amount,BI.tax_type,R.category_id
        FROM " . $this->db->dbprefix('bil_items') . " BI
        JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
            WHERE BI.bil_id =".$billid." GROUP BY BI.id";
            /*echo $myQuery;die;*/
        $q = $this->db->query($myQuery);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;

    }
   public function update_bill_withcustomer_discount($billid,$dis_id){	
	/*echo "sivan"; die;*/
	$update_bil['customer_discount_status'] = 'applied';
	// $this->db->where('id', $billid);
        // if ($this->db->update('bils', $update_bil)) {
	    $myQuery = "SELECT BI.id,BI.recipe_id,sum(BI.subtotal-BI.item_discount-BI.off_discount) AS amount,BI.tax_type,R.category_id
        FROM " . $this->db->dbprefix('bil_items') . " BI
        JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
            WHERE BI.bil_id =".$billid." GROUP BY BI.id";
            
        $q = $this->db->query($myQuery);
        /*
if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data = $row;
            }
            return $data;
        }*/

	    if ($q->num_rows() > 0) {
		/*$result = $q->result_array();*/
		 //print_r($q->result_array());
		foreach (($q->result_array()) as $row) {
		
			$inputDiscount = $this->get_customer_discount_bygroup($row['category_id'],$row['amount'],$dis_id);
		    /*$inputDiscount = $this->site->calculate_Discount($discount, (($row['amount']),(($row['subtotal']-$row['item_discount'])-$row['off_discount']));*/
		    $afterDis_total = $row['amount'] - $inputDiscount;
		    $tax = ($row['tax_type']==0)?$this->inclusive_tax_and_amt($afterDis_total,$row['tax_id']):$this->exclusive_tax_and_amt($afterDis_total,$row['tax_id']);
		    $updateItem['tax'] = $tax['tax'];
		    $updateItem['input_discount'] = $inputDiscount;
		    	    
		    $row['id'];		    
		    $this->db->where('id', $row['id']);
		    $this->db->update('bil_items', $updateItem);		    
		    //echo $this->db->last_query();exit;
		}

		$BillQuery = "SELECT B.tax_id,BI.bil_id,SUM(BI.item_discount+BI.off_discount+BI.input_discount) AS total_discount,SUM(BI.subtotal) AS subtotal,BI.tax_type
        FROM " . $this->db->dbprefix('bil_items') . " BI
	JOIN " . $this->db->dbprefix('bils') . " B ON B.id = BI.bil_id
            WHERE BI.bil_id =".$billid." GROUP BY BI.bil_id";
        $b = $this->db->query($BillQuery);

		if ($b->num_rows() > 0) {
			foreach (($b->result()) as $row) {
				$totalAmt_afterDiscount = $row->subtotal - $row->total_discount;
			$getTax = $this->site->getTaxRateByID($row->tax_id);
			if($row->tax_type==0){
			    $grandTotal = $totalAmt_afterDiscount/(($getTax->rate/100)+1);
				    $totalTax = $totalAmt_afterDiscount-($totalAmt_afterDiscount/(($getTax->rate/100)+1));
				    $amountPayable = $grandTotal+$totalTax;
				    
				}else{
				    $totalTax = $totalAmt_afterDiscount*($getTax->rate/100);
				    $grandTotal = $totalAmt_afterDiscount+$totalTax;
				    $amountPayable = $grandTotal;
				}
				$update_bil['grand_total'] = $this->sma->formatDecimal($grandTotal);
				$update_bil['total_tax'] = $this->sma->formatDecimal($totalTax);
				$update_bil['total_discount'] = $row->total_discount;
				$update_bil['round_total'] =  $this->sma->formatDecimal($grandTotal);
				$this->db->where('id', $row->bil_id);
				
		        $this->db->update('bils', $update_bil);

		       return $this->sma->formatDecimal($amountPayable);;
		    }		

		}
	//die;
	    }
	// }
        return false;
    }

 public  function get_customer_discount_bygroup($groupid,$finalAmt,$discountid){
    	
	$discount  = $this->getCategory_Group_Customer_Discount($groupid,$discountid);
	if($discount){
	    return $discountAmt = $finalAmt*($discount/100);
	    
	}
	return 0;
    }
  public  function getCategory_Group_Customer_Discount($groupid,$discountid){
	$q = $this->db
	    ->select('GD.discount_val')
	    ->from('diccounts_for_customer D')
	    ->join('group_discount GD','GD.cus_discount_id=D.id and GD.recipe_group_id='.$groupid)
	    ->where('D.id',$discountid)
	    ->get();
	$res = $q->row();
	return ($q->num_rows()>0)?$res->discount_val:false;
    }

    function update_bil($billid,$update_bil,$discount){
	
	$dpos = strpos($discount, '%');
	if ($dpos !== false) {
	    $pds = explode("%", $discount);
	    $disType = '%';
	    $disVal = $pds[0];
	}else{
	    $disVal = $discount;
	    $disType = 'F';//fixed
	}
	
	
	$update_bil['customer_discount_status'] = 'applied';
	$this->db->where('id', $billid);
        if ($this->db->update('bils', $update_bil)) {
	    $this->db
		->select('BI.id,B.tax_id,B.total bill_total,BI.*')
		->from('bil_items BI')
		->join('bils B','B.id=BI.bil_id')
		->where('B.id',$billid);
	    $q = $this->db->get();
	   
	    if ($q->num_rows() > 0) {
		$result = $q->result_array();
		 //print_r($q->result_array());
		foreach($result as $k => $row){
		    $inputDiscount = $this->site->calculate_Discount($discount, (($row['subtotal']-$row['item_discount'])-$row['off_discount']),(($row['subtotal']-$row['item_discount'])-$row['off_discount']));
		    $afterDis_total = $row['subtotal'] - $inputDiscount;
		    $tax = ($row['tax_type']==0)?$this->inclusive_tax_and_amt($afterDis_total,$row['tax_id']):$this->exclusive_tax_and_amt($afterDis_total,$row['tax_id']);
		    $updateItem['tax'] = $tax['tax'];
		    $updateItem['input_discount'] = $inputDiscount;
		    $row['id'];
		    
		    $this->db->where('id', $row['id']);
		    $this->db->update('bil_items', $updateItem);
		    
		    //echo $this->db->last_query();exit;
		}
	    }
	}
        return false;
    }
    function inclusive_tax_and_amt($total,$taxID){
	$getTax = $this->site->getTaxRateByID($taxID);
	$return['g_total'] = $total/(($getTax->rate/100)+1);
	$return['tax'] = $total-($total/(($getTax->rate/100)+1));
	return $return;
    }
    public function exclusive_tax_and_amt($total,$taxID){
	$getTax = $this->site->getTaxRateByID($taxID);
	$return['tax'] = $total*($getTax->rate/100);
	$return['g_total'] = $total+$return['tax'];
	return $return;
    }

public function getTableNumber($bill_id)
    {
        $table_name = "SELECT T.name AS table_name,TY.name AS order_type

                    FROM ".$this->db->dbprefix('bils')." AS P
                    JOIN ". $this->db->dbprefix('sales') ." AS S ON S.id = P.sales_id
                    JOIN ". $this->db->dbprefix('orders') ." AS O ON O.split_id = S.sales_split_id
                    JOIN ". $this->db->dbprefix('sales_type') ." AS TY ON TY.id = O.order_type
                           
                    LEFT JOIN ". $this->db->dbprefix('restaurant_tables') ." AS T ON T.id = O.table_id
                    WHERE P.id='".$bill_id."' ";
            
        $q = $this->db->query($table_name);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data = $row;
            }
            return $data;
        }
        return FALSE;
    }
    
   

    function recipe_customer_discount_calculation($itemid,$groupid,$finalAmt,$discountid){
	//echo $itemid.'-'.$groupid.'-'.$finalAmt.'-'.$discountid;
	if($this->Settings->customer_discount=="customer"){
	    $discount  = $this->getCategory_cusDiscount($groupid,$discountid);
	    if($discount){
		return $discountAmt = $finalAmt*($discount/100);
		
	    }
	}else if($this->Settings->customer_discount=="manual"){//manual
	    $discount_value = $discountid;
	    return $discountAmt = $this->site->calculateDiscount($discount_value, $finalAmt);
	}
	return 0;
    }
    function getCategory_cusDiscount($groupid,$discountid){
	$q = $this->db
	    ->select('GD.discount_val')
	    ->from('diccounts_for_customer D')
	    ->join('group_discount GD','GD.cus_discount_id=D.id and GD.recipe_group_id='.$groupid)
	    ->where('D.id',$discountid)
	    ->get();
	$res = $q->row();
	return ($q->num_rows()>0)?$res->discount_val:false;
    }

public function change_table($change_split_id, $changed_table_id){

        $order_array = array(
            'table_id' => $changed_table_id,
        );
        
        $this->db->where('split_id', $change_split_id);
        if ($this->db->update('orders',  $order_array)) {
			$orders_id = "SELECT O.id 
			FROM ".$this->db->dbprefix('orders')." AS O
			WHERE O.split_id='".$change_split_id."' ";

			$q = $this->db->query($orders_id);			
	        if ($q->num_rows() > 0) {
	            foreach (($q->result()) as $row) {
	            	$this->db->update('restaurant_table_orders', array('table_id' => $changed_table_id), array('order_id' => $row->id));
	            	$this->db->update('restaurant_table_sessions', array('table_id' => $changed_table_id), array('order_id' => $row->id));	                
	            }
	            return TRUE;
	        }		
			return true;
		}
		return false;
    }
    
function change_customer($change_split_id, $changed_customer_id){
    $order_array = array(
            'customer_id' => $changed_customer_id,
        );
    $this->db->where('split_id',$change_split_id);
    $this->db->update('orders',  $order_array);
    return TRUE;
}
    function isTableWhitelisted($tableid){
	$q = $this->db->get_where("restaurant_tables",array('id'=>$tableid,'whitelisted'=>1));
	if ($q->num_rows() > 0) {
	    return 1;
	}
	return 0;
    }
    public function getSettlementReport($start,$end)
    {
        $saletype = "SELECT st.name sale_type,SUM(P.round_total) sale_type_total
        FROM srampos_bils  P
            LEFT JOIN srampos_sales  s
            ON s.id = P.sales_id
	    LEFT JOIN srampos_sales_type  st
            ON st.id = s.sales_type_id
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
            P.payment_status ='Completed' 
            GROUP BY s.sales_type_id";
            
        $saletype_res = $this->db->query($saletype);//echo $myQuery;exit;
	
	$tenderType = "SELECT PA.paid_by tender_type,PA.cc_type,SUM(PA.amount) tender_type_total
        FROM srampos_bils  P
            LEFT JOIN srampos_payments  PA
            ON P.id = PA.bill_id
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
            P.payment_status ='Completed' 
            GROUP BY PA.paid_by";
            
        $tenderType_res = $this->db->query($tenderType);//echo $myQuery;exit;
	
	$query = "SELECT count(P.id) total_transaction,SUM(P.round_total) gross_total,SUM(P.round_total-P.total_discount) net_total
        FROM srampos_bils  P
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
            P.payment_status ='Completed' ";
	$details = $this->db->query($query);//print_R($this->db->error());exit;
	$data['payments'] = $details->result(); 
	$data['tender_type'] = $tenderType_res->result();
	$data['sale_type'] = $saletype_res->result();
        //if ($q->num_rows() > 0) { print_R($q->result());exit;
        //    foreach (($q->result()) as $row) {
        //        $data[] = $row;
        //    }
        //    return $data;
        //}
	//print_R($data);exit;
	return $data;
        return FALSE;
    }
}
