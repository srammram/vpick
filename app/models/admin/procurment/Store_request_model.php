<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Store_request_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    


    public function getProductNames($term, $warehouse_id, $limit = 10)
    {
        $this->db->select('recipe.*, warehouses_recipe.quantity')
            ->join('warehouses_recipe', 'warehouses_recipe.recipe_id=recipe.id', 'left')
            ->group_by('recipe.id');

            $this->db->where("(name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%' OR  concat(name, ' (', code, ')') LIKE '%" . $term . "%')");

        $this->db->limit($limit);
        $q = $this->db->get('recipe');        
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getProductByCode($code)
    {
        $q = $this->db->get_where('recipe', array('code' => $code), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getWHProduct($id)
    {
        $this->db->select('recipe.id, code, name, warehouses_recipe.quantity, cost, tax_rate')
            ->join('warehouses_recipe', 'warehouses_recipe.product_id=recipe.id', 'left')
            ->group_by('recipe.id');
        $q = $this->db->get_where('recipe', array('warehouses_recipe.product_id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getItemByID($id)
    {
        $q = $this->db->get_where('pro_store_request_items', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllStore_requestItemsWithDetails($store_request_id)
    {
        $this->db->select('pro_store_request_items.id, pro_store_request_items.product_name, pro_store_request_items.product_code, pro_store_request_items.quantity, pro_store_request_items.serial_no, pro_store_request_items.tax, pro_store_request_items.unit_price, pro_store_request_items.val_tax, pro_store_request_items.discount_val, pro_store_request_items.gross_total, recipe.details, recipe.hsn_code as hsn_code, recipe.name as second_name');
        $this->db->join('recipe', 'recipe.id=pro_store_request_items.product_id', 'left');
        $this->db->order_by('id', 'asc');
        $q = $this->db->get_where('pro_store_request_items', array('store_request_id' => $store_request_id));

        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getStore_requestByID($id)
    {
        $q = $this->db->get_where('pro_store_request', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllStore_requestItems($store_request_id)
    {
        $this->db->select('pro_store_request_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, recipe.unit, recipe.image, recipe.details as details, recipe_variants.name as variant, recipe.hsn_code as hsn_code, recipe.name as second_name')
            ->join('recipe', 'recipe.id=pro_store_request_items.product_id', 'left')
            ->join('recipe_variants', 'recipe_variants.id=pro_store_request_items.option_id', 'left')
            ->join('tax_rates', 'tax_rates.id=pro_store_request_items.tax_rate_id', 'left')
            ->group_by('pro_store_request_items.id')
            ->order_by('id', 'asc');
        $q = $this->db->get_where('pro_store_request_items', array('store_request_id' => $store_request_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function addStore_request($data = array(), $items = array())
    {
		
        if ($this->db->insert('pro_store_request', $data)) {
			
            $store_request_id = $this->db->insert_id();
            
            foreach ($items as $item) {
                $item['store_request_id'] = $store_request_id;
                $this->db->insert('pro_store_request_items', $item);
            }
            return true;
        }
        return false;
    }


    public function updateStore_request($id, $data, $items = array())
    {        
        if ($this->db->update('pro_store_request', $data, array('id' => $id)) && $this->db->delete('pro_store_request_items', array('store_request_id' => $id))) {
            foreach ($items as $item) {
                $item['store_request_id'] = $id;
                $this->db->insert('pro_store_request_items', $item);
            }            
            return true;
        }        
        return false;
    }

    public function updateStatus($id, $status, $note)
    {
        if ($this->db->update('pro_store_request', array('status' => $status, 'note' => $note), array('id' => $id))) {
            return true;
        }
        return false;
    }


    public function deleteStore_request($id)
    {
        if ($this->db->delete('pro_quote_items', array('store_request_id' => $id)) && $this->db->delete('pro_store_request', array('id' => $id))) {
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
