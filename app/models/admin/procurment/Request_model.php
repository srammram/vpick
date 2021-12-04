<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Request_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getProductNames($term, $limit = 10)
    {
        $type = array('standard','raw');
        $this->db->select('r.*,t.rate as purchase_tax_rate');
        $this->db->from('recipe r');
        $this->db->join('tax_rates t','r.purchase_tax=t.id','left');
            $this->db->where("(r.name LIKE '%" . $term . "%' OR r.code LIKE '%" . $term . "%' OR  concat(r.name, ' (', r.code, ')') LIKE '%" . $term . "%')");
            $this->db->where_in('r.type',$type);
        $this->db->limit($limit);
        
            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
                return $data;
            }
            return FALSE;
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
        $q = $this->db->get_where('pro_request_items', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllRequestItemsWithDetails($request_id)
    {
        $this->db->select('p.*');
        $this->db->from('pro_request_items as p');
	$this->db->join('recipe as r', 'r.id=p.product_id', 'left');
        $this->db->order_by('id', 'asc');
        $this->db->where(array('request_id' => $request_id));
	//echo $this->db->get_compiled_select();
	$q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getRequestByID($id)
    {
        $q = $this->db->get_where('pro_request', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllRequestItems($request_id)
    {
        $this->db->select('pro_store_request.from_store_id as store_id,pro_store_request_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, recipe.unit, recipe.image, recipe.details as details, recipe_variants.name as variant, recipe.hsn_code as hsn_code, ')
            ->join('recipe', 'recipe.id=pro_store_request_items.product_id', 'left')
            ->join('recipe_variants', 'recipe_variants.id=pro_store_request_items.option_id', 'left')
            ->join('tax_rates', 'tax_rates.id=pro_store_request_items.tax_rate_id', 'left')
	    ->join('pro_store_request', 'pro_store_request.id=pro_store_request_items.store_request_id')
            ->where_in('pro_store_request_items.store_request_id',($request_id))   
            ->group_by('pro_store_request_items.id')
            ->order_by('id', 'asc');
        $q = $this->db->get('pro_store_request_items');
         // print_r($this->db->last_query())        ;die;
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function addRequest($data = array(), $items = array(), $reference, $date, $status, $un,$store_request_id)
    {
/*        echo "<pre>";
        print_r($data);die;
        */
            foreach ($data as $key =>  $request_data) {
            $this->db->insert('pro_request', $request_data);            
            $request_id = $this->db->insert_id();     
            
			/*if($status == 'process'){				
				foreach($un as $un_row){
					$notification = array(
						'user_id' => $un_row->id,
						'group_id' => $un_row->group_id,
						'title' => 'Purchases Request',
						'links' => admin_url('procurment/request/edit/'.$request_id.''),
						'message' => 'The new purchase request has been created. REF No:'.$reference.', Date:'.$date,
						'created_by' => $this->session->userdata('user_id'),
						'created_on' => date('Y-m-d H:i:s'),
					);	
					
					$this->siteprocurment->insertNotification($notification);
				}
			}*/
            foreach ($items[$key]  as $item) {
                    $item['request_id'] = $request_id;
                    $this->db->insert('pro_request_items', $item);
                }
        }  
        // print_r($this->db->error());die;
        if($store_request_id != ''){
            $store_array = array(
                'status' => 'completed',
            );
            $this->db->where_in('id', $store_request_id);
            $this->db->update('pro_store_request',  $store_array);                                    
        }   
        // print_r($this->db->error());die;
        return true;
    }


    public function updateRequest($id, $data, $items = array())
    {
        if ($this->db->update('pro_request', $data, array('id' => $id)) && $this->db->delete('pro_request_items', array('request_id' => $id))) {
            foreach ($items as $item) {
                $item['request_id'] = $id;
                $this->db->insert('pro_request_items', $item);
            }
            return true;
        }
        return false;
    }

    public function updateStatus($id, $status, $note)
    {
        if ($this->db->update('pro_request', array('status' => $status, 'note' => $note), array('id' => $id))) {
            return true;
        }
        return false;
    }


    public function deleteRequest($id)
    {
        
        if ($this->db->delete('pro_request_items', array('request_id' => $id)) && $this->db->delete('pro_request', array('id' => $id))) {
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
    public function getProductOptions($product_id)
    {
        $q = $this->db->get_where('recipe_variants', array('recipe_id' => $product_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

 /*   public function getProductOptions($product_id, $warehouse_id)
    {
        $this->db->select('product_variants.id as id, product_variants.name as name, product_variants.price as price, product_variants.quantity as total_quantity, warehouses_products_variants.quantity as quantity')
            ->join('warehouses_products_variants', 'warehouses_products_variants.option_id=product_variants.id', 'left')            
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
    }*/

    public function getProductOptionByID($id)
    {
        $q = $this->db->get_where('product_variants', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getStoreRequestByID($id)
    {
        $this->db->select()
        ->from('pro_store_request')        
        ->where_in('id',($id));        
        $q = $this->db->get();
        // $q = $this->db->get_where('pro_store_request', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
        public function getStoreRequestItems($store_requestid)
    {
        $this->db->select('pro_store_request_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, recipe.unit, recipe.details as details, recipe_variants.name as variant, recipe.hsn_code as hsn_code')
            ->join('recipe', 'recipe.id=pro_store_request_items.product_id', 'left')
            ->join('recipe_variants', 'recipe_variants.id=pro_store_request_items.option_id', 'left')
            ->join('tax_rates', 'tax_rates.id=pro_store_request_items.tax_rate_id', 'left')
            ->group_by('pro_store_request_items.id')
            ->order_by('id', 'asc');
        $q = $this->db->get_where('pro_store_request_items', array('request_id' => $store_requestid));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getAllSTOREREQUEST(){

        $this->db->select('pro_store_request.*, warehouses.name as store_name')
            ->join('warehouses', 'warehouses.id=pro_store_request.from_store_id', 'left');            
            $this->db->where('status', 'approved');
            $this->db->or_where('status', 'partial_complete');            
        $q = $this->db->get('pro_store_request');        
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
        return FALSE;

        $this->db->where('status', 'approved');
        $this->db->or_where('status', 'partial_complete');
        $q = $this->db->get('pro_store_request');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
}
