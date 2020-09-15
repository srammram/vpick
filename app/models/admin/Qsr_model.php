<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Qsr_model extends CI_Model
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
        $q = $this->db->get_where("recipe", array('recipe.id' => $code));
        /*print_r($this->db->last_query());die;*/
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
	
    /*public function getProductsByCode($code)
    {
        $this->db->like('code', $code, 'both')->order_by("code");
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getWHProduct($code, $warehouse_id)
    {
        $this->db->select('products.*, warehouses_products.quantity, categories.id as category_id, categories.name as category_name')
            ->join('warehouses_products', 'warehouses_products.product_id=products.id', 'left')
            ->join('categories', 'categories.id=products.category_id', 'left')
            ->group_by('products.id');
        $q = $this->db->get_where("products", array('products.code' => $code));
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;
    }

    public function getProductOptions($product_id, $warehouse_id, $all = NULL)
    {
        $wpv = "( SELECT option_id, warehouse_id, quantity from {$this->db->dbprefix('warehouses_products_variants')} WHERE product_id = {$product_id}) FWPV";
        $this->db->select('product_variants.id as id, product_variants.name as name, product_variants.price as price, product_variants.quantity as total_quantity, FWPV.quantity as quantity', FALSE)
            ->join($wpv, 'FWPV.option_id=product_variants.id', 'left')
            //->join('warehouses', 'warehouses.id=product_variants.warehouse_id', 'left')
            ->where('product_variants.product_id', $product_id)
            ->group_by('product_variants.id');

        if (! $this->Settings->overselling && ! $all) {
            $this->db->where('FWPV.warehouse_id', $warehouse_id);
            $this->db->where('FWPV.quantity >', 0);
        }
        $q = $this->db->get('product_variants');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getProductComboItems($pid, $warehouse_id)
    {
        $this->db->select('products.id as id, combo_items.item_code as code, combo_items.quantity as qty, products.name as name, products.type as type, warehouses_products.quantity as quantity')
            ->join('products', 'products.code=combo_items.item_code', 'left')
            ->join('warehouses_products', 'warehouses_products.product_id=products.id', 'left')
            ->where('warehouses_products.warehouse_id', $warehouse_id)
            ->group_by('combo_items.id');
        $q = $this->db->get_where('combo_items', array('combo_items.product_id' => $pid));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
        return FALSE;
    }*/

    public function updateOptionQuantity($option_id, $quantity)
    {
        if ($option = $this->getProductOptionByID($option_id)) {
            $nq = $option->quantity - $quantity;
            if ($this->db->update('product_variants', array('quantity' => $nq), array('id' => $option_id))) {
                return TRUE;
            }
        }
        return FALSE;
    }

    public function addOptionQuantity($option_id, $quantity)
    {
        if ($option = $this->getProductOptionByID($option_id)) {
            $nq = $option->quantity + $quantity;
            if ($this->db->update('product_variants', array('quantity' => $nq), array('id' => $option_id))) {
                return TRUE;
            }
        }
        return FALSE;
    }

    public function getProductOptionByID($id)
    {
        $q = $this->db->get_where('product_variants', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getProductWarehouseOptionQty($option_id, $warehouse_id)
    {
        $q = $this->db->get_where('warehouses_products_variants', array('option_id' => $option_id, 'warehouse_id' => $warehouse_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function updateProductOptionQuantity($option_id, $warehouse_id, $quantity, $product_id)
    {
        if ($option = $this->getProductWarehouseOptionQty($option_id, $warehouse_id)) {
            $nq = $option->quantity - $quantity;
            if ($this->db->update('warehouses_products_variants', array('quantity' => $nq), array('option_id' => $option_id, 'warehouse_id' => $warehouse_id))) {
                $this->site->syncVariantQty($option_id, $warehouse_id);
                return TRUE;
            }
        } else {
            $nq = 0 - $quantity;
            if ($this->db->insert('warehouses_products_variants', array('option_id' => $option_id, 'product_id' => $product_id, 'warehouse_id' => $warehouse_id, 'quantity' => $nq))) {
                $this->site->syncVariantQty($option_id, $warehouse_id);
                return TRUE;
            }
        }
        return FALSE;
    }
	
	public function addInsertbil($data, $recipes, $payment, $update_bill, $sales_bill, $multi_currency, $did,$order_data, $bill_recipes,$total,$customer_id,$loyalty_used_points,$taxation){
        
		/*echo "<pre>";
        print_r($multi_currency);die;*/
        /*var_dump($_POST['balance_final_amount']);die;*/
		/*echo "<pre>";
        print_r($bill_recipes);die;*/
		//$bill_number = sprintf("%'.05d", $bill_id);
	  // $this->db->update('bils', array('bill_number' => $bill_number), array('id' => $bill_id));
		   
		if ($this->db->insert('sales', $data)) {

            $sale_id = $this->db->insert_id();

            $this->db->insert('orders', $order_data);

            $order_id = $this->db->insert_id();			
			
			$this->db->update('sales', $sales_bill, array('id' => $sale_id));

            $this->db->update('orders', $sales_bill, array('id' => $order_id));
			
            $bil = array(
                // 'date' => $this->site->getTransactionDate(),
                'date' => date('Y-m-d H:i:s'),
                'reference_no' => 'SALES-'.$data['reference_no'],
                'sales_id' => $sale_id,
                'customer_id' => $data['customer_id'],
                'customer' => $data['customer'],
                'biller_id' => $data['biller_id'],
                'biller' => $data['biller'],
                'total' => $data['total'],
                'order_discount_id' => $data['order_discount_id'],
                'total_discount' => $data['total_discount'],
                'order_discount' => $data['order_discount'],
                'tax_id' => $data['order_tax_id'],
                'order_tax' => $data['order_tax'],
                'total_tax' => $data['total_tax'],
                'shipping' => $data['shipping'],
                'grand_total' => $data['grand_total'],
                'total_items' => $data['total_items'],
                'created_by' => $data['created_by'],
                'payment_status' => 'completed',
                'warehouse_id' => $this->session->userdata('warehouse_id'),
                'balance' => $_POST['balance_final_amount'] ? $_POST['balance_final_amount'] : 0,
            );
			
			$this->db->insert('bils', $bil);
/*print_r($this->db->error());die;*/
			$bill_id = $this->db->insert_id();

			$bill_number = $this->site->generate_bill_number($taxation);
            $this->db->update('bils', array('bill_number' => $bill_number), array('id' => $bill_id));

			/*$bill_number = sprintf("%'.05d", $bill_id);
		    $this->db->update('bils', array('bill_number' => $bill_number), array('id' => $bill_id));*/
			
			$this->db->update('bils', $update_bill, array('id' => $bill_id));
			
			
				   
		  foreach ($recipes as $item) {

                $item['sale_id'] = $sale_id;
                $item['bil_id'] = $bill_id;
                $this->db->insert('sale_items', $item);
                
            }
           foreach ($recipes as $order_item) {

                $order_item['sale_id'] = $order_id;
                /*$order_item['bil_id'] = $bill_id;*/
                $this->db->insert('order_items', $order_item);
                
            }
            /*echo "<pre>";
			print_r($bill_recipes);die;*/
			foreach ($bill_recipes as $item1) {
                $item1['bil_id'] = $bill_id;                
                // $item1['input_discount'] = $data['total_discount']/$data['total_items'];
                $this->db->insert('bil_items', $item1);
                
				
			}
			
			/*echo "<pre>";
            print_r($payment);die;*/
			foreach ($payment as $payment_item) {
				$payment_item['sale_id'] = $sale_id;
				$payment_item['bill_id'] = $bill_id;
    			$this->db->insert('payments', $payment_item);
                /*print_r($this->db->error());die;*/
    		}
			
			
								
			foreach ($multi_currency as $currency) {
				$currency['sale_id'] = $sale_id;			
				$currency['bil_id'] = $bill_id;
			
    			$this->db->insert('sale_currency', $currency);
    		}     
            if ($did) {
                $this->deleteBill($did);
            }  
            $this->site->LoyaltyinserAndUpdate($bill_id,$data['total'],$data['customer_id'],$loyalty_used_points);         
			 return array('sale_id' => $bill_id);
                      
			 return TRUE;
		}
		
		/*print_r($this->db->error());die;*/
		return FALSE;
	}
	
	 public function getAllBillitems($id =NULL)
    {
        $q = $this->db->get_where('bil_items', array('bil_id' => $id));

        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }
    public function get_BillData($id) {
    $this->db->select("bils.*,tax_rates.name as tax_name, tax_rates.rate as tax_rate")
    ->join('tax_rates', 'tax_rates.id = bils.tax_id','left')
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
                if ($data['sale_status'] == 'completed' && $this->site->getProductByID($item['product_id'])) {

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

    public function getProductByCode($code)
    {
        $q = $this->db->get_where('products', array('code' => $code), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getProductByName($name)
    {
        $q = $this->db->get_where('products', array('name' => $name), 1);
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

 

    public function getAllProducts()
    {
        $q = $this->db->query('SELECT * FROM products ORDER BY id');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }

    public function getProductByID($id)
    {

        $q = $this->db->get_where('products', array('id' => $id), 1);
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

    public function updateProductQuantity($product_id, $warehouse_id, $quantity)
    {

        if ($this->addQuantity($product_id, $warehouse_id, $quantity)) {
            return true;
        }

        return false;
    }

    public function addQuantity($product_id, $warehouse_id, $quantity)
    {
        if ($warehouse_quantity = $this->getProductQuantity($product_id, $warehouse_id)) {
            $new_quantity = $warehouse_quantity['quantity'] - $quantity;
            if ($this->updateQuantity($product_id, $warehouse_id, $new_quantity)) {
                $this->site->syncProductQty($product_id, $warehouse_id);
                return TRUE;
            }
        } else {
            if ($this->insertQuantity($product_id, $warehouse_id, -$quantity)) {
                $this->site->syncProductQty($product_id, $warehouse_id);
                return TRUE;
            }
        }
        return FALSE;
    }

    public function insertQuantity($product_id, $warehouse_id, $quantity)
    {
        if ($this->db->insert('warehouses_products', array('product_id' => $product_id, 'warehouse_id' => $warehouse_id, 'quantity' => $quantity))) {
            return true;
        }
        return false;
    }
	
	 public function getCompanyByID($id)
    {
        $q = $this->db->get_where('companies', array('id' => $id), 1);        
        if ($q->num_rows() > 0) {        	
            return $q->row();
        }
        return FALSE;
    }

    public function updateQuantity($product_id, $warehouse_id, $quantity)
    {
        if ($this->db->update('warehouses_products', array('quantity' => $quantity), array('product_id' => $product_id, 'warehouse_id' => $warehouse_id))) {
            return true;
        }
        return false;
    }

    public function getProductQuantity($product_id, $warehouse)
    {
        $q = $this->db->get_where('warehouses_products', array('product_id' => $product_id, 'warehouse_id' => $warehouse), 1);
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

    /*public function getAllInvoiceItems($sale_id)
    {
        if ($this->pos_settings->item_order == 0) {
            $this->db->select('sale_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, product_variants.name as variant, products.details as details, products.hsn_code as hsn_code')
            ->join('products', 'products.id=sale_items.product_id', 'left')
            ->join('tax_rates', 'tax_rates.id=sale_items.tax_rate_id', 'left')
            ->join('product_variants', 'product_variants.id=sale_items.option_id', 'left')
            ->group_by('sale_items.id')
            ->order_by('id', 'asc');
        } elseif ($this->pos_settings->item_order == 1) {
            $this->db->select('sale_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, product_variants.name as variant, categories.id as category_id, categories.name as category_name, products.details as details, products.hsn_code as hsn_code')
            ->join('tax_rates', 'tax_rates.id=sale_items.tax_rate_id', 'left')
            ->join('product_variants', 'product_variants.id=sale_items.option_id', 'left')
            ->join('products', 'products.id=sale_items.product_id', 'left')
            ->join('categories', 'categories.id=products.category_id', 'left')
            ->group_by('sale_items.id')
            ->order_by('categories.id', 'asc');
        }

        $q = $this->db->get_where('sale_items', array('sale_id' => $sale_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }*/


	public function getAllInvoiceItems($sale_id)
    {
        if ($this->pos_settings->item_order == 0) {
            $this->db->select('bil_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, recipe_variants.name as variant, recipe.details as details, recipe.hsn_code as hsn_code')
            ->join('recipe', 'recipe.id=bil_items.recipe_id', 'left')
            ->join('tax_rates', 'tax_rates.id=bil_items.tax_rate_id', 'left')
            ->join('recipe_variants', 'recipe_variants.id=bil_items.option_id', 'left')
            ->group_by('bil_items.id')
            ->order_by('id', 'asc');
        } elseif ($this->pos_settings->item_order == 1) {
            $this->db->select('bil_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, recipe_variants.name as variant, categories.id as category_id, categories.name as category_name, recipe.details as details, recipe.hsn_code as hsn_code')
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
	
    /*public function getInvoiceByID($id)
    {

        $q = $this->db->get_where('sales', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;
    }*/

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
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('paid_by', 'CC');
        $this->db->where('payments.created_by', $user_id);

        $q = $this->db->get('payments');
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
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('paid_by', 'cash');
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
            ->join('sales', 'sales.id=payments.return_id', 'left')
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
            ->join('sales', 'sales.id=payments.return_id', 'left')
            ->where('type', 'returned')->where('payments.date >', $date)->where('paid_by', 'cash');
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
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('paid_by', 'Cheque');
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
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('paid_by', 'gift_card');
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
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('paid_by', 'ppp');
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
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('paid_by', 'stripe');
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
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('paid_by', 'authorize');
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
                    /*print_r($this->db->error());die;*/
                    return TRUE;
                }
                /*print_r($this->db->error());die;*/
            }
/*print_r($this->db->error());die;*/
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

  /*  public function getInvoicePayments($sale_id)
    {
        $q = $this->db->get_where("payments", array('sale_id' => $sale_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }

        return FALSE;
    }*/

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
     function getSettings()
    {
        $q = $this->db->get('settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

function recipe_customer_discount_calculation($itemid,$groupid,$subgroup_id,$finalAmt,$discountid){
    //echo $itemid.'-'.$groupid.'-'.$finalAmt.'-'.$discountid;
    if($this->Settings->customer_discount=="customer"){ 
        //$discount  = $this->getCategory_GroupDiscount($groupid,$discountid);
        //echo $groupid.'-'.$subgroup_id.'-'.$itemid.'-'.$discountid;
        $discount = $this->getCategory_GroupDiscount($groupid,$subgroup_id,$itemid,$discountid);
        if(isset($discount['discount_val']) && $discount['discount_val']!=''){
        $dis_val = $discount['discount_val'];
        if($discount['discount_type']=="percentage"){
            return $discountAmt = $finalAmt*($dis_val/100);
        }else if($discount['discount_type']=="amount"){
            if($dis_val<$finalAmt){ return $dis_val;}else{return $finalAmt;}
        }
        
        
        }
    }else if($this->Settings->customer_discount=="manual"){//manual
        $discount_value = $discountid;
        return $discountAmt = $this->site->calculateDiscount($discount_value, $finalAmt);
    }
    return 0;
    }

    function getCategory_GroupDiscount($groupid,$subgroup_id,$itemid,$discountid){
        $today = date('Y-m-d');
        $curtime  = date('H:i').':00';
        $q = $this->db
            ->select('GD.discount_val,GD.discount_type,GD.recipe_id,GD.type')
            ->from('diccounts_for_customer D')
            ->join('group_discount GD','GD.cus_discount_id=D.id and GD.recipe_group_id='.$groupid)
            ->where('D.id',$discountid)
            ->where('D.status',1)
            ->where('GD.status',1)
            ->where('GD.recipe_subgroup_id',$subgroup_id)
            ->where('DATE(D.from_date) <=', $today)
            ->where('DATE(D.to_date) >=', $today)
            
            ->where('TIME(D.from_time) <=', $curtime)
            ->where('TIME(D.to_time) >=', $curtime)
            //->where('GD.type','included')
            ->get();
            // print_r($this->db->last_query());die;
        if($q->num_rows()>0) {
            $res = $q->result();
            foreach($res as $k => $row){ 
            $recipe_id_days = unserialize($row->recipe_id);
            $return['discount_val'] = $row->discount_val;
            $return['discount_type'] = $row->discount_type;
            if(isset($recipe_id_days[$itemid]) && $row->type=="included") {
                
                $today = strtolower(date('D'));
                $days = unserialize($recipe_id_days[$itemid]['days']);
               
                if(isset($days[$today])){
                
                return $return;
                }           
                return false;
            }else if(!isset($recipe_id_days[$itemid]) && $row->type=="excluded"){
                
                return $return;
            }else if(isset($recipe_id_days[$itemid]) && $row->type=="excluded"){
               
                return false;
            }       
            else{
                return false;
            }
            }
        }
        return false;
        }
    function isVarientExist($rid){
    $this->db->select('v.*,r.*,v.id variant_id');
    $this->db->from('recipe_variants_values r');
    $this->db->join('recipe_variants v','v.id=r.attr_id');
    $this->db->where(array('r.recipe_id'=>$rid));
    $q = $this->db->get();
    if($q->num_rows()>0){
        return $q->result();
    }
    return false;
    
    }      
    function getVariantData($vid,$rid){
    $this->db->select('v.*,r.*');
    $this->db->from('recipe_variants_values r');
    $this->db->join('recipe_variants v','v.id=r.attr_id');
    $this->db->where(array('r.recipe_id'=>$rid,'v.id'=>$vid));
    //echo $this->db->get_compiled_select();
    $q = $this->db->get();
    /*print_r($this->db->last_query());die;*/
    if($q->num_rows()>0){
        return $q->row();
    }
    return false;
    
    }      
    public function getAllBillingforReprint($date){
        
        $current_date = date('Y-m-d');
        $this->db->select("bils.*");
        $this->db->join("bil_items", "bil_items.bil_id = bils.id");
        /*$this->db->where('bils.payment_status', $this->session->userdata('warehouse_id'));*/
        $this->db->where('bils.payment_status', 'Completed');
        $this->db->where('DATE(date)', $date);
        $this->db->group_by("bils.id");
        $this->db->order_by("bils.id", "desc");
        $s = $this->db->get('bils');
        
         if ($s->num_rows() > 0) {
            foreach ($s->result() as $row) {
                $data[] = $row;
             }            
            return $data;
        }
        return FALSE;
    }    
    public function getSettlementReport($start,$end)
    {
        $saletype = "SELECT st.name sale_type,SUM(P.total) sale_type_total
        FROM srampos_bils  P
            LEFT JOIN srampos_sales  s
            ON s.id = P.sales_id
        LEFT JOIN srampos_sales_type  st
            ON st.id = s.sales_type_id
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
            P.payment_status ='Completed' 
            GROUP BY s.sales_type_id";
            
        $saletype_res = $this->db->query($saletype);//echo $saletype;exit;
    
    $tenderType = "SELECT PA.paid_by tender_type,PA.cc_type,SUM(PA.amount) tender_type_total
        FROM srampos_bils  P
            LEFT JOIN srampos_payments  PA
            ON P.id = PA.bill_id
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
            P.payment_status ='Completed' 
            GROUP BY PA.paid_by";
            
        $tenderType_res = $this->db->query($tenderType);//echo $tenderType;exit;
    
    $query = "SELECT count(P.id) total_transaction,SUM(P.total) gross_total,SUM(P.total-P.total_discount) net_total
        FROM srampos_bils  P
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
            P.payment_status ='Completed' ";//echo $query;exit;
    $details = $this->db->query($query);//print_R($this->db->error());exit;
     /*echo "<pre>";      
            print_r($details->result_array());die;*/
    $data['payments'] = $details->result(); 
    $data['tender_type'] = $tenderType_res->result();
    // $data['sale_type'] = $saletype_res->result();        
    return $data;
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
    public function getdaysummary($start,$end)
    {   
        $this->db->select('SUM(COALESCE(total, 0)) as total,SUM(COALESCE(grand_total, 0)) as total_amount1, SUM(COALESCE(total_tax, 0)) as total_tax, SUM(COALESCE(total_discount, 0)) as total_discount,SUM(total-total_discount+CASE WHEN (tax_type= 1) THEN total_tax ELSE 0 END) as total_amount, COUNT(' . $this->db->dbprefix('bils') . '.id) as totalbill,SUM(total-total_discount+CASE WHEN (tax_type= 1) THEN total_tax ELSE 0 END) as gross_amt,SUM(total-CASE WHEN (tax_type= 0) THEN total_tax ELSE 0 END) as netamt', FALSE)
        ->where('payment_status', 'Completed')
        ->where('DATE(date) >=', $start)
        ->where('DATE(date) <=', $end);
        $q = $this->db->get('bils');

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
        
}
