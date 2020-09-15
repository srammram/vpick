<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_returns_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

	public function addStorepurchasewise($data, $quote_id, $product_id){
		$this->db->where('purchase_order_id', $quote_id);
		$this->db->where('product_id', $product_id);
		$this->db->delete('delivery_store');
		if($this->db->insert_batch('delivery_store', $data)){
			
			return true;
		}
		return false;
	}
	
	public function productQuotesID($product_id, $quote_id){
		$this->db->where('purchase_order_id', $quote_id);
		$this->db->where('product_id', $product_id);
		$q = $this->db->get('delivery_store');
		if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
		return false;
	}
	
	public function getProductStores($product_id){
		$this->db->select('stores_products.store_id, stores.name');
		$this->db->join('pro_stores', 'stores.id = stores_products.store_id');
		$this->db->where('stores_products.product_id', $product_id);
		$q = $this->db->get('pro_stores_products');
		if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
		return false;
	}
	
    public function getProductNames($term, $limit = 10)
    {
        $this->db->where("(name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%' OR  concat(name, ' (', code, ')') LIKE '%" . $term . "%')");
        $this->db->limit($limit);
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function getReqBYID($id)
    {
        $q = $this->db->get_where('pro_purchase_orders', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function getQuoteByID($id)
    {
        $q = $this->db->get_where('pro_purchase_returns', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	
	public function getAllQuoteItems($purchase_order_id)
    {
		
        $this->db->select('pro_purchase_return_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, products.unit, products.details as details, product_variants.name as variant, products.hsn_code as hsn_code')
            ->join('products', 'products.id=pro_purchase_return_items.product_id', 'left')
            ->join('product_variants', 'product_variants.id=pro_purchase_return_items.option_id', 'left')
            ->join('tax_rates', 'tax_rates.id=pro_purchase_return_items.tax_rate_id', 'left')
            ->group_by('pro_purchase_return_items.id')
            ->order_by('id', 'asc');
        $q = $this->db->get_where('pro_purchase_return_items', array('purchase_order_id' => $purchase_order_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
    public function getAllProducts()
    {
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getProductByID($id)
    {
        $q = $this->db->get_where('products', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function getSupplierdetails($supplier_id){
		$q = $this->db->get_where('companies', array('id' => $supplier_id, 'group_id' => 4), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
	}
    public function getProductsByCode($code)
    {
        $this->db->select('*')->from('products')->like('code', $code, 'both');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
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

    public function getAllPurchase_returns()
    {
        $q = $this->db->get('pro_purchase_returns');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

	
	
	public function getAllRequestItems($purchase_returns_id)
    {
        $this->db->select('pro_purchase_order_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, products.unit, products.details as details, product_variants.name as variant, products.hsn_code as hsn_code')
            ->join('products', 'products.id=pro_purchase_order_items.product_id', 'left')
            ->join('product_variants', 'product_variants.id=pro_purchase_order_items.option_id', 'left')
            ->join('tax_rates', 'tax_rates.id=pro_purchase_order_items.tax_rate_id', 'left')
            ->group_by('pro_purchase_order_items.id')
            ->order_by('id', 'asc');
        $q = $this->db->get_where('pro_purchase_order_items', array('purchase_order_id' => $purchase_returns_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
    public function getAllPurchase_returnsItems($purchase_returns_id)
    {
        $this->db->select('pro_purchase_return_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, products.unit, products.details as details, product_variants.name as variant, products.hsn_code as hsn_code')
            ->join('products', 'products.id=pro_purchase_return_items.product_id', 'left')
            ->join('product_variants', 'product_variants.id=pro_purchase_return_items.option_id', 'left')
            ->join('tax_rates', 'tax_rates.id=pro_purchase_return_items.tax_rate_id', 'left')
            ->group_by('pro_purchase_return_items.id')
            ->order_by('id', 'asc');
        $q = $this->db->get_where('pro_purchase_return_items', array('purchase_return_id' => $purchase_returns_id));
		
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getItemByID($id)
    {
        $q = $this->db->get_where('pro_purchase_return_items', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getTaxRateByName($name)
    {
        $q = $this->db->get_where('tax_rates', array('name' => $name), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getPurchase_returnsByID($id)
    {
        $q = $this->db->get_where('pro_purchase_returns', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	 public function getRequestByID($id)
    {
        $q = $this->db->get_where('pro_request', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
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

    public function addProductOptionQuantity($option_id, $warehouse_id, $quantity, $product_id)
    {
        if ($option = $this->getProductWarehouseOptionQty($option_id, $warehouse_id)) {
            $nq = $option->quantity + $quantity;
            if ($this->db->update('warehouses_products_variants', array('quantity' => $nq), array('option_id' => $option_id, 'warehouse_id' => $warehouse_id))) {
                return TRUE;
            }
        } else {
            if ($this->db->insert('warehouses_products_variants', array('option_id' => $option_id, 'product_id' => $product_id, 'warehouse_id' => $warehouse_id, 'quantity' => $quantity))) {
                return TRUE;
            }
        }
        return FALSE;
    }

    public function resetProductOptionQuantity($option_id, $warehouse_id, $quantity, $product_id)
    {
        if ($option = $this->getProductWarehouseOptionQty($option_id, $warehouse_id)) {
            $nq = $option->quantity - $quantity;
            if ($this->db->update('warehouses_products_variants', array('quantity' => $nq), array('option_id' => $option_id, 'warehouse_id' => $warehouse_id))) {
                return TRUE;
            }
        } else {
            $nq = 0 - $quantity;
            if ($this->db->insert('warehouses_products_variants', array('option_id' => $option_id, 'product_id' => $product_id, 'warehouse_id' => $warehouse_id, 'quantity' => $nq))) {
                return TRUE;
            }
        }
        return FALSE;
    }

    public function getOverSoldCosting($product_id)
    {
        $q = $this->db->get_where('costing', array('overselling' => 1));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function addPurchase_returns($data, $items, $purchase_returns, $order_id)
    {
		
		$store_default_id = $this->siteprocurment->defaultStores();
        if ($this->db->insert('pro_purchase_returns', $data)) {
             $purchase_returns_id = $this->db->insert_id();
			
			$this->db->update('pro_purchase_orders', $purchase_returns, array('id' => $order_id));
			
            foreach ($items as $item) {
				
				$q = $this->db->select('*')->where('purchase_order_id', $order_id)->where('product_id', $item['product_id'])->get('delivery_store');
				if ($q->num_rows() > 0) {
					$this->db->update('delivery_store', array('purchase_returns_id' => $purchase_returns_id, 'quantity' => $item['quantity'], 'status' => 'approved', 'date' => date('Y-m-d'), 'created_on' => date('Y-m-d H:i:s'), 'created_by' =>  $this->session->userdata('user_id')), array('purchase_order_id' => $order_id, 'product_id' => $item['product_id']));
				}else{
					$this->db->insert('delivery_store', array('purchase_returns_id' => $purchase_returns_id, 'product_id' => $item['product_id'], 'quantity' => $item['quantity'], 'store_id' => $store_default_id, 'status' => 'approved', 'date' => date('Y-m-d'), 'created_on' => date('Y-m-d H:i:s'), 'created_by' =>  $this->session->userdata('user_id')));
				}
				
                $item['purchase_return_id'] = $purchase_returns_id;
                $this->db->insert('pro_purchase_return_items', $item);
				
				
				
				
            }
			
			
            return true;
        }
        return false;
    }

    public function updatePurchase_returns($id, $data, $items = array(), $status)
    {
		
		$store_default_id = $this->siteprocurment->defaultStores();
        if ($this->db->update('pro_purchase_returns', $data, array('id' => $id)) && $this->db->delete('pro_purchase_return_items', array('purchase_return_id' => $id))) {
            $purchase_returns_id = $id;
			
            foreach ($items as $item) {
                $item['purchase_return_id'] = $id;
				
				$q = $this->db->select('*')->where('purchase_returns_id', $id)->where('product_id', $item['product_id'])->get('delivery_store');
				if ($q->num_rows() > 0) {
					$this->db->update('delivery_store', array('purchase_returns_id' => $purchase_returns_id, 'quantity' => $item['quantity'], 'status' => 'approved', 'date' => date('Y-m-d'), 'created_on' => date('Y-m-d H:i:s'), 'created_by' =>  $this->session->userdata('user_id')), array('purchase_returns_id' => $id, 'product_id' => $item['product_id']));
				}else{
					$this->db->insert('delivery_store', array('purchase_returns_id' => $purchase_returns_id, 'product_id' => $item['product_id'], 'quantity' => $item['quantity'], 'store_id' => $store_default_id, 'status' => 'approved', 'date' => date('Y-m-d'), 'created_on' => date('Y-m-d H:i:s'), 'created_by' =>  $this->session->userdata('user_id')));
				}
				
				$this->db->insert('pro_purchase_return_items', $item);
				
				if($status == 'completed'){
					
					$stock = $this->db->select('current_quantity')->where('product_id', $item['product_id'])->where('store_id', $store_default_id)->order_by('product_id', 'DESC')->limit(1)->get('pro_stock_master');
					
					if ($stock->num_rows() > 0) {
						
						$current_quantity = $stock->row('current_quantity') +  $item['quantity']; 
						$this->db->insert('pro_stock_master', array('store_id' => $store_default_id, 'product_id' => $item['product_id'], 'current_quantity' => $current_quantity, 'quantity' => $item['quantity'], 'transacton_type' => 'IN', 'type' => 'purchase', 'purchase_return_id' => $id, 'purchase_batch_no' => $item['item_batch_no'], 'created_on' => date('Y-m-d H:i:s'), 'created_by' =>  $this->session->userdata('user_id') ));
					
					}else{
						
						$this->db->insert('pro_stock_master', array('store_id' => $store_default_id, 'product_id' => $item['product_id'], 'current_quantity' =>  $item['quantity'], 'quantity' => $item['quantity'], 'transacton_type' => 'IN', 'type' => 'purchase', 'purchase_return_id' => $id, 'purchase_batch_no' => $item['item_batch_no'], 'created_on' => date('Y-m-d H:i:s'), 'created_by' =>  $this->session->userdata('user_id') ));
						
					}
					
					
				}
				
                
				
				
            }
         
            return true;
        }

        return false;
    }

    public function updateStatus($id, $status, $note)
    {
        // $purchase = $this->getPurchase_returnsByID($id);
        $items = $this->siteprocurment->getAllPurchase_returnsItems($id);

        if ($this->db->update('pro_purchase_return_items', array('status' => $status, 'note' => $note), array('id' => $id))) {
            foreach ($items as $item) {
                $qb = $status == 'completed' ? ($item->quantity_balance + ($item->quantity - $item->quantity_received)) : $item->quantity_balance;
                $qr = $status == 'completed' ? $item->quantity : $item->quantity_received;
                $this->db->update('pro_purchase_items', array('status' => $status, 'quantity_balance' => $qb, 'quantity_received' => $qr), array('id' => $item->id));
                $this->updateAVCO(array('product_id' => $item->product_id, 'warehouse_id' => $item->warehouse_id, 'quantity' => $item->quantity, 'cost' => $item->real_unit_cost));
            }
            $this->siteprocurment->syncQuantity(NULL, NULL, $items);
            return true;
        }
        return false;
    }

    public function deletePurchase_returns($id)
    {
        $purchase = $this->getPurchase_returnsByID($id);
        $purchase_items = $this->siteprocurment->getAllPurchase_returnsItems($id);
        if ($this->db->delete('pro_purchase_return_items', array('purchase_returns_id' => $id)) && $this->db->delete('pro_purchase_returns', array('id' => $id))) {
            // $this->db->delete('payments', array('purchase_order_id' => $id));
            // if ($purchase->status == 'received' || $purchase->status == 'partial') {
            //     foreach ($purchase_items as $oitem) {
            //         $this->updateAVCO(array('product_id' => $oitem->product_id, 'warehouse_id' => $oitem->warehouse_id, 'quantity' => (0-$oitem->quantity), 'cost' => $oitem->real_unit_cost));
            //         $received = $oitem->quantity_received ? $oitem->quantity_received : $oitem->quantity;
            //         if ($oitem->quantity_balance < $received) {
            //             $clause = array('purchase_order_id' => NULL, 'transfer_id' => NULL, 'product_id' => $oitem->product_id, 'warehouse_id' => $oitem->warehouse_id, 'option_id' => $oitem->option_id);
            //             $this->siteprocurment->setPurchaseItem($clause, ($oitem->quantity_balance - $received));
            //         }
            //     }
            // }
            $this->siteprocurment->syncQuantity(NULL, NULL, $purchase_items);
            return true;
        }
        return FALSE;
    }

    public function getWarehouseProductQuantity($warehouse_id, $product_id)
    {
        $q = $this->db->get_where('warehouses_products', array('warehouse_id' => $warehouse_id, 'product_id' => $product_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getPurchasePayments($purchase_order_id)
    {
        $this->db->order_by('id', 'asc');
        $q = $this->db->get_where('payments', array('purchase_order_id' => $purchase_order_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getPaymentByID($id)
    {
        $q = $this->db->get_where('payments', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;
    }

    public function getPaymentsForPurchase($purchase_order_id)
    {
        $this->db->select('payments.date, payments.paid_by, payments.amount, payments.reference_no, users.first_name, users.last_name, type')
            ->join('users', 'users.id=payments.created_by', 'left');
        $q = $this->db->get_where('payments', array('purchase_order_id' => $purchase_order_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function addPayment($data = array())
    {
        if ($this->db->insert('payments', $data)) {
            if ($this->siteprocurment->getReference('ppay') == $data['reference_no']) {
                $this->siteprocurment->updateReference('ppay');
            }
            $this->siteprocurment->syncPurchasePayments($data['purchase_order_id']);
            return true;
        }
        return false;
    }

    public function updatePayment($id, $data = array())
    {
        if ($this->db->update('payments', $data, array('id' => $id))) {
            $this->siteprocurment->syncPurchasePayments($data['purchase_order_id']);
            return true;
        }
        return false;
    }

    public function deletePayment($id)
    {
        $opay = $this->getPaymentByID($id);
        if ($this->db->delete('payments', array('id' => $id))) {
            $this->siteprocurment->syncPurchasePayments($opay->purchase_order_id);
            return true;
        }
        return FALSE;
    }

    public function getProductOptions($product_id)
    {
        $q = $this->db->get_where('product_variants', array('product_id' => $product_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getProductVariantByName($name, $product_id)
    {
        $q = $this->db->get_where('product_variants', array('name' => $name, 'product_id' => $product_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getExpenseByID($id)
    {
        $q = $this->db->get_where('expenses', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function addExpense($data = array())
    {
        if ($this->db->insert('expenses', $data)) {
            if ($this->siteprocurment->getReference('ex') == $data['reference']) {
                $this->siteprocurment->updateReference('ex');
            }
            return true;
        }
        return false;
    }

    public function updateExpense($id, $data = array())
    {
        if ($this->db->update('expenses', $data, array('id' => $id))) {
            return true;
        }
        return false;
    }

    public function deleteExpense($id)
    {
        if ($this->db->delete('expenses', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }

   

    public function getReturnByID($id)
    {
        $q = $this->db->get_where('return_purchase_returns', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllReturnItems($return_id)
    {
        $this->db->select('return_purchase_items.*, products.details as details, product_variants.name as variant, products.hsn_code as hsn_code')
            ->join('products', 'products.id=return_purchase_items.product_id', 'left')
            ->join('product_variants', 'product_variants.id=return_purchase_items.option_id', 'left')
            ->group_by('return_purchase_items.id')
            ->order_by('id', 'asc');
        $q = $this->db->get_where('return_purchase_items', array('return_id' => $return_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getPurcahseItemByID($id)
    {
        $q = $this->db->get_where('pro_purchase_items', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function returnPurchase($data = array(), $items = array())
    {

        $purchase_items = $this->siteprocurment->getAllPurchase_returnsItems($data['purchase_returns_id']);

        if ($this->db->insert('return_purchase_returns', $data)) {
            $return_id = $this->db->insert_id();
            if ($this->siteprocurment->getReference('rep') == $data['reference_no']) {
                $this->siteprocurment->updateReference('rep');
            }
            foreach ($items as $item) {
                $item['return_id'] = $return_id;
                $this->db->insert('return_purchase_items', $item);

                if ($purchase_item = $this->getPurcahseItemByID($item['purchase_item_id'])) {
                    if ($purchase_item->quantity == $item['quantity']) {
                        $this->db->delete('pro_purchase_items', array('id' => $item['purchase_item_id']));
                    } else {
                        $nqty = $purchase_item->quantity - $item['quantity'];
                        $bqty = $purchase_item->quantity_balance - $item['quantity'];
                        $rqty = $purchase_item->quantity_received - $item['quantity'];
                        $tax = $purchase_item->unit_cost - $purchase_item->net_unit_cost;
                        $discount = $purchase_item->item_discount / $purchase_item->quantity;
                        $item_tax = $tax * $nqty;
                        $item_discount = $discount * $nqty;
                        $subtotal = $purchase_item->unit_cost * $nqty;
                        $this->db->update('pro_purchase_items', array('quantity' => $nqty, 'quantity_balance' => $bqty, 'quantity_received' => $rqty, 'item_tax' => $item_tax, 'item_discount' => $item_discount, 'subtotal' => $subtotal), array('id' => $item['purchase_item_id']));
                    }

                }
            }
            $this->calculatePurchaseTotals($data['purchase_order_id'], $return_id, $data['surcharge']);
            $this->siteprocurment->syncQuantity(NULL, NULL, $purchase_items);
            $this->siteprocurment->syncQuantity(NULL, $data['purchase_order_id']);
            return true;
        }
        return false;
    }

    public function calculatePurchaseTotals($id, $return_id, $surcharge)
    {
        $purchase = $this->getPurchase_returnsByID($id);
        $items = $this->getAllPurchase_returnsItems($id);
        if (!empty($items)) {
            $total = 0;
            $product_tax = 0;
            $order_tax = 0;
            $product_discount = 0;
            $order_discount = 0;
            foreach ($items as $item) {
                $product_tax += $item->item_tax;
                $product_discount += $item->item_discount;
                $total += $item->net_unit_cost * $item->quantity;
            }
            if ($purchase->order_discount_id) {
                $percentage = '%';
                $order_discount_id = $purchase->order_discount_id;
                $opos = strpos($order_discount_id, $percentage);
                if ($opos !== false) {
                    $ods = explode("%", $order_discount_id);
                    $order_discount = (($total + $product_tax) * (Float)($ods[0])) / 100;
                } else {
                    $order_discount = $order_discount_id;
                }
            }
            if ($purchase->order_tax_id) {
                $order_tax_id = $purchase->order_tax_id;
                if ($order_tax_details = $this->siteprocurment->getTaxRateByID($order_tax_id)) {
                    if ($order_tax_details->type == 2) {
                        $order_tax = $order_tax_details->rate;
                    }
                    if ($order_tax_details->type == 1) {
                        $order_tax = (($total + $product_tax - $order_discount) * $order_tax_details->rate) / 100;
                    }
                }
            }
            $total_discount = $order_discount + $product_discount;
            $total_tax = $product_tax + $order_tax;
            $grand_total = $total + $total_tax + $purchase->shipping - $order_discount + $surcharge;
            $data = array(
                'total' => $total,
                'product_discount' => $product_discount,
                'order_discount' => $order_discount,
                'total_discount' => $total_discount,
                'product_tax' => $product_tax,
                'order_tax' => $order_tax,
                'total_tax' => $total_tax,
                'grand_total' => $grand_total,
                'return_id' => $return_id,
                'surcharge' => $surcharge
            );

            if ($this->db->update('pro_purchase_returns', $data, array('id' => $id))) {
                return true;
            }
        } else {
            $this->db->delete('pro_purchase_returns', array('id' => $id));
        }
        return FALSE;
    }

    public function getExpenseCategories()
    {
        $q = $this->db->get('expense_categories');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getExpenseCategoryByID($id)
    {
        $q = $this->db->get_where("expense_categories", array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function updateAVCO($data)
    {
        if ($wp_details = $this->getWarehouseProductQuantity($data['warehouse_id'], $data['product_id'])) {
            $total_cost = (($wp_details->quantity * $wp_details->avg_cost) + ($data['quantity'] * $data['cost']));
            $total_quantity = $wp_details->quantity + $data['quantity'];
            if (!empty($total_quantity)) {
                $avg_cost = ($total_cost / $total_quantity);
                $this->db->update('warehouses_products', array('avg_cost' => $avg_cost), array('product_id' => $data['product_id'], 'warehouse_id' => $data['warehouse_id']));
            }
        } else {
            $this->db->insert('warehouses_products', array('product_id' => $data['product_id'], 'warehouse_id' => $data['warehouse_id'], 'avg_cost' => $data['cost'], 'quantity' => 0));
        }
    }

}
