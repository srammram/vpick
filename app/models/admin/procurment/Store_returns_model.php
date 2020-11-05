<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Store_returns_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getProductNames($term, $warehouse_id, $store_id, $limit = 10)
    {
        $this->db->select('products.*, warehouses_products.quantity, pro_stock_master.id as stock_id, pro_stock_master.purchase_batch_no, SUM('.$this->db->dbprefix("pro_stock_master").'.quantity) as available_quantity')
			->join('pro_stock_master', 'pro_stock_master.product_id = products.id AND pro_stock_master.transacton_type = "IN" AND pro_stock_master.store_id = '.$store_id.' ')
            ->join('warehouses_products', 'warehouses_products.product_id=products.id', 'left')
            ->group_by('pro_stock_master.purchase_batch_no');

            $this->db->where("(name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%' OR  concat(name, ' (', code, ')') LIKE '%" . $term . "%')");

        $this->db->limit($limit);
		
        $q = $this->db->get('products');
		
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

    public function getWHProduct($id)
    {
        $this->db->select('products.id, code, name, warehouses_products.quantity, cost, tax_rate')
            ->join('warehouses_products', 'warehouses_products.product_id=products.id', 'left')
            ->group_by('products.id');
        $q = $this->db->get_where('products', array('warehouses_products.product_id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getItemByID($id)
    {
        $q = $this->db->get_where('pro_store_return_items', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllStore_returnsItemsWithDetails($store_return_id)
    {
        $this->db->select('pro_store_return_items.id, pro_store_return_items.product_name, pro_store_return_items.product_code, pro_store_return_items.quantity, pro_store_return_items.serial_no, pro_store_return_items.tax, pro_store_return_items.unit_price, pro_store_return_items.val_tax, pro_store_return_items.discount_val, pro_store_return_items.gross_total, products.details, products.hsn_code as hsn_code, products.name as second_name');
        $this->db->join('products', 'products.id=pro_store_return_items.product_id', 'left');
        $this->db->order_by('id', 'asc');
        $q = $this->db->get_where('pro_store_return_items', array('store_return_id' => $store_return_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getStore_returnsByID($id)
    {
        $q = $this->db->get_where('pro_store_returns', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllStore_returnsItems($store_return_id)
    {
        $this->db->select('pro_store_return_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, products.unit, products.image, products.details as details, product_variants.name as variant, products.hsn_code as hsn_code, products.name as second_name')
            ->join('products', 'products.id=pro_store_return_items.product_id', 'left')
            ->join('product_variants', 'product_variants.id=pro_store_return_items.option_id', 'left')
            ->join('tax_rates', 'tax_rates.id=pro_store_return_items.tax_rate_id', 'left')
            ->group_by('pro_store_return_items.id')
            ->order_by('id', 'asc');
        $q = $this->db->get_where('pro_store_return_items', array('store_return_id' => $store_return_id));
		
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function addStore_returns($data = array(), $items = array())
    {
		
		
        if ($this->db->insert('pro_store_returns', $data)) {
			
            $store_return_id = $this->db->insert_id();
            
            foreach ($items as $item) {
                $item['store_return_id'] = $store_return_id;
                $this->db->insert('pro_store_return_items', $item);
				
            }
            return true;
        }
        return false;
    }


    public function updateStore_returns($id, $data, $items = array())
    {
        if ($this->db->update('pro_store_returns', $data, array('id' => $id)) && $this->db->delete('pro_store_return_items', array('store_return_id' => $id))) {
            foreach ($items as $item) {
                $item['store_return_id'] = $id;
                $this->db->insert('pro_store_return_items', $item);
            }
            return true;
        }
        return false;
    }

    public function updateStatus($id, $status, $note)
    {
        if ($this->db->update('pro_store_returns', array('status' => $status, 'note' => $note), array('id' => $id))) {
            return true;
        }
        return false;
    }


    public function deleteStore_returns($id)
    {
        if ($this->db->delete('pro_quote_items', array('store_return_id' => $id)) && $this->db->delete('pro_store_returns', array('id' => $id))) {
            return true;
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

    public function getWarehouseProductQuantity($warehouse_id, $product_id)
    {
        $q = $this->db->get_where('warehouses_products', array('warehouse_id' => $warehouse_id, 'product_id' => $product_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
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
    }

    public function getProductOptions($product_id, $warehouse_id)
    {
        $this->db->select('product_variants.id as id, product_variants.name as name, product_variants.price as price, product_variants.quantity as total_quantity, warehouses_products_variants.quantity as quantity')
            ->join('warehouses_products_variants', 'warehouses_products_variants.option_id=product_variants.id', 'left')
            //->join('warehouses', 'warehouses.id=product_variants.warehouse_id', 'left')
            ->where('product_variants.product_id', $product_id)
            ->where('warehouses_products_variants.warehouse_id', $warehouse_id)
            ->where('warehouses_products_variants.quantity >', 0)
            ->group_by('product_variants.id');
        $q = $this->db->get('product_variants');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
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

}
