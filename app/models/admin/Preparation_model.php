<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Preparation_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getAllpreparation()
    {
        $q = $this->db->get('preparation');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function getpreparationItems($id)
    {
		$this->db->where('preparation_id', $id);
        $q = $this->db->get('preparation_items');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	

    public function getCategorypreparation($category_id)
    {
        $q = $this->db->get_where('preparation', array('category_id' => $category_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function checkDate($date){
		$q = $this->db->get_where('preparation', array('preparation_date' => $date));
		if ($q->num_rows() > 0) {
			return TRUE;	
		}
		return FALSE;
	}
	
    public function getSubCategorypreparation($subcategory_id)
    {
        $q = $this->db->get_where('preparation', array('subcategory_id' => $subcategory_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getpreparationOptions($pid)
    {
        $q = $this->db->get_where('preparation_variants', array('preparation_id' => $pid));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getpreparationOptionsWithWH($pid)
    {
        $this->db->select($this->db->dbprefix('preparation_variants') . '.*, ' . $this->db->dbprefix('warehouses') . '.name as wh_name, ' . $this->db->dbprefix('warehouses') . '.id as warehouse_id, ' . $this->db->dbprefix('warehouses_preparation_variants') . '.quantity as wh_qty')
            ->join('warehouses_preparation_variants', 'warehouses_preparation_variants.option_id=preparation_variants.id', 'left')
            ->join('warehouses', 'warehouses.id=warehouses_preparation_variants.warehouse_id', 'left')
            ->group_by(array('' . $this->db->dbprefix('preparation_variants') . '.id', '' . $this->db->dbprefix('warehouses_preparation_variants') . '.warehouse_id'))
            ->order_by('preparation_variants.id');
        $q = $this->db->get_where('preparation_variants', array('preparation_variants.preparation_id' => $pid, 'warehouses_preparation_variants.quantity !=' => NULL));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getpreparationComboItems($pid)
    {
		
        $this->db->select($this->db->dbprefix('preparation') . '.id as id,   ' . $this->db->dbprefix('preparation') . '.name as name,  ' . $this->db->dbprefix('preparation_combo_items') . '.unit_price as price,  ' . $this->db->dbprefix('preparation') . '.code as code')->join('preparation', 'preparation.id=preparation_combo_items.item_id', 'left')->group_by('preparation_combo_items.id');
        $q = $this->db->get_where('preparation_combo_items', array('preparation_id' => $pid));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
        return FALSE;
    }

    public function getpreparationByID($id)
    {
        $q = $this->db->get_where('preparation', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getpreparationWithCategory($id)
    {
        $this->db->select($this->db->dbprefix('preparation') . '.*, ' . $this->db->dbprefix('preparation_categories') . '.name as category')
        ->join('preparation_categories', 'preparation_categories.id=preparation.category_id', 'left');
        $q = $this->db->get_where('preparation', array('preparation.id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function has_purchase($preparation_id, $warehouse_id = NULL)
    {
        if($warehouse_id) { $this->db->where('warehouse_id', $warehouse_id); }
        $q = $this->db->get_where('purchase_items', array('preparation_id' => $preparation_id), 1);
        if ($q->num_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }

    public function getpreparationDetails($id)
    {
        $this->db->select($this->db->dbprefix('preparation') . '.code, ' . $this->db->dbprefix('preparation') . '.name, ' . $this->db->dbprefix('preparation_categories') . '.code as category_code, cost, price, quantity, alert_quantity')
            ->join('preparation_categories', 'preparation_categories.id=preparation.category_id', 'left');
        $q = $this->db->get_where('preparation', array('preparation.id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getpreparationDetail($id)
    {
        $this->db->select($this->db->dbprefix('preparation') . '.*, ' . $this->db->dbprefix('tax_rates') . '.name as tax_rate_name, '.$this->db->dbprefix('tax_rates') . '.code as tax_rate_code, c.code as category_code, sc.code as subcategory_code', FALSE)
            ->join('tax_rates', 'tax_rates.id=preparation.tax_rate', 'left')
            ->join('preparation_categories c', 'c.id=preparation.category_id', 'left')
            ->join('preparation_categories sc', 'sc.id=preparation.subcategory_id', 'left');
        $q = $this->db->get_where('preparation', array('preparation.id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getpreparationSubCategories($parent_id) {
        $this->db->select('id as id, name as text')
        ->where('parent_id', $parent_id)->order_by('name');
        $q = $this->db->get("preparation_categories");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getpreparationByCategoryID($id)
    {

        $q = $this->db->get_where('preparation', array('category_id' => $id), 1);
        if ($q->num_rows() > 0) {
            return true;
        }
        return FALSE;
    }

    public function getAllWarehousesWithPQ($preparation_id)
    {
        $this->db->select('' . $this->db->dbprefix('warehouses') . '.*, ' . $this->db->dbprefix('warehouses_preparation') . '.quantity, ' . $this->db->dbprefix('warehouses_preparation') . '.rack')
            ->join('warehouses_preparation', 'warehouses_preparation.warehouse_id=warehouses.id', 'left')
            ->where('warehouses_preparation.preparation_id', $preparation_id)
            ->group_by('warehouses.id');
        $q = $this->db->get('warehouses');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function getAllProductWithRecipe($preparation_id)
    {
        $this->db->select('preparation_products.*, units.name AS units_name, products.name AS product_name')
			->join('units', 'units.id=preparation_products.units_id', 'left')
			->join('products', 'products.id=preparation_products.product_id', 'left')
            ->where('preparation_products.preparation_id', $preparation_id);
        	$q = $this->db->get('preparation_products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function getAllAddonWithRecipe($preparation_id)
    {
        $this->db->select('preparation_addon.*')
            ->where('preparation_addon.preparation_id', $preparation_id);
        	$q = $this->db->get('preparation_addon');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function getAllWarehouseWithRecipe($preparation_id)
    {
        $this->db->select('*')
            ->where('preparation_id', $preparation_id);
        	$q = $this->db->get('warehouses_preparation');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getpreparationPhotos($id)
    {
        $q = $this->db->get_where("preparation_photos", array('preparation_id' => $id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getpreparationByCode($code)
    {
        $q = $this->db->get_where('preparation', array('code' => $code), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function addpreparation($data, $items, $warehouse_qty, $preparation_pro, $preparation_attributes, $photos)
    {
        if ($this->db->insert('preparation', $data)) {
            $preparation_id = $this->db->insert_id();
			
			

            if ($items) {
                foreach ($items as $item) {
                    $item['preparation_id'] = $preparation_id;
                    $this->db->insert('combo_items', $item);
                }
            }

            $warehouses = $this->site->getAllWarehouses();
            if ($data['type'] != 'standard') {
                foreach ($warehouses as $warehouse) {
                    $this->db->insert('warehouses_preparation', array('preparation_id' => $preparation_id, 'warehouse_id' => $warehouse->id, 'quantity' => 0));
                }
            }

            $tax_rate = $this->site->getTaxRateByID($data['tax_rate']);

            if ($warehouse_qty && !empty($warehouse_qty)) {
                foreach ($warehouse_qty as $wh_qty) {
					
                   
                        $this->db->insert('warehouses_preparation', array('preparation_id' => $preparation_id, 'warehouse_id' => $wh_qty['warehouse_id']));

                    
                   
                }
            }
			
			if ($preparation_pro && !empty($preparation_pro)) {
                foreach ($preparation_pro as $re_pro) {
					
                   
                        $this->db->insert('preparation_products', array('preparation_id' => $preparation_id, 'product_id' => $re_pro['product_id[]'], 'min_quantity' => $re_pro['min_quantity[]'], 'max_quantity' => $re_pro['max_quantity'], 'units_id' => $re_pro['units_id']));

                    
                   
                }
            }

            if ($preparation_attributes) {
                foreach ($preparation_attributes as $pr_attr) {
                    $pr_attr_details = $this->getPrductVariantByPIDandName($preparation_id, $pr_attr['name']);

                    $pr_attr['preparation_id'] = $preparation_id;
                    $variant_warehouse_id = $pr_attr['warehouse_id'];
                    unset($pr_attr['warehouse_id']);
                    if ($pr_attr_details) {
                        $option_id = $pr_attr_details->id;
                    } else {
                        $this->db->insert('preparation_variants', $pr_attr);
                        $option_id = $this->db->insert_id();
                    }
                    if ($pr_attr['quantity'] != 0) {
                        $this->db->insert('warehouses_preparation_variants', array('option_id' => $option_id, 'preparation_id' => $preparation_id, 'warehouse_id' => $variant_warehouse_id, 'quantity' => $pr_attr['quantity']));

                        $tax_rate_id = $tax_rate ? $tax_rate->id : NULL;
                        $tax = $tax_rate ? (($tax_rate->type == 1) ? $tax_rate->rate . "%" : $tax_rate->rate) : NULL;
                        $unit_cost = $data['cost'];
                        if ($tax_rate) {
                            if ($tax_rate->type == 1 && $tax_rate->rate != 0) {
                                if ($data['tax_method'] == '0') {
                                    $pr_tax_val = ($data['cost'] * $tax_rate->rate) / (100 + $tax_rate->rate);
                                    $net_item_cost = $data['cost'] - $pr_tax_val;
                                    $item_tax = $pr_tax_val * $pr_attr['quantity'];
                                } else {
                                    $net_item_cost = $data['cost'];
                                    $pr_tax_val = ($data['cost'] * $tax_rate->rate) / 100;
                                    $unit_cost = $data['cost'] + $pr_tax_val;
                                    $item_tax = $pr_tax_val * $pr_attr['quantity'];
                                }
                            } else {
                                $net_item_cost = $data['cost'];
                                $item_tax = $tax_rate->rate;
                            }
                        } else {
                            $net_item_cost = $data['cost'];
                            $item_tax = 0;
                        }

                        $subtotal = (($net_item_cost * $pr_attr['quantity']) + $item_tax);
                        $item = array(
                            'preparation_id' => $preparation_id,
                            'preparation_code' => $data['code'],
                            'preparation_name' => $data['name'],
                            'net_unit_cost' => $net_item_cost,
                            'unit_cost' => $unit_cost,
                            'quantity' => $pr_attr['quantity'],
                            'option_id' => $option_id,
                            'quantity_balance' => $pr_attr['quantity'],
                            'quantity_received' => $pr_attr['quantity'],
                            'item_tax' => $item_tax,
                            'tax_rate_id' => $tax_rate_id,
                            'tax' => $tax,
                            'subtotal' => $subtotal,
                            'warehouse_id' => $variant_warehouse_id,
                            'date' => date('Y-m-d'),
                            'status' => 'received',
                        );
                        $item['option_id'] = !empty($item['option_id']) && is_numeric($item['option_id']) ? $item['option_id'] : NULL;
                        $this->db->insert('purchase_items', $item);

                    }

                    foreach ($warehouses as $warehouse) {
                        if (!$this->getWarehousepreparationVariant($warehouse->id, $preparation_id, $option_id)) {
                            $this->db->insert('warehouses_preparation_variants', array('option_id' => $option_id, 'preparation_id' => $preparation_id, 'warehouse_id' => $warehouse->id, 'quantity' => 0));
                        }
                    }

                    $this->site->syncVariantQty($option_id, $variant_warehouse_id);
                }
            }

            if ($photos) {
                foreach ($photos as $photo) {
                    $this->db->insert('preparation_photos', array('preparation_id' => $preparation_id, 'photo' => $photo));
                }
            }

            return true;
        }
        return false;

    }
	
	public function addpreparation_new($preparation_array, $purchases_item)
    {
		
        if ($this->db->insert('preparation', $preparation_array)) {
			
            $preparation_id = $this->db->insert_id();
			if ($purchases_item && !empty($purchases_item)) {
				foreach ($purchases_item as $item) {
					$item['preparation_id'] = $preparation_id;
					$this->db->insert('preparation_items', $item);
				}
			}
            return true;
        }
        return false;

    }

    public function getPrductVariantByPIDandName($preparation_id, $name)
    {
        $q = $this->db->get_where('preparation_variants', array('preparation_id' => $preparation_id, 'name' => $name), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function addAjaxpreparation($data)
    {
        if ($this->db->insert('preparation', $data)) {
            $preparation_id = $this->db->insert_id();
            return $this->getpreparationByID($preparation_id);
        }
        return false;
    }
	
	public function getpreparationProductsalesUnits($term)
    {
        $this->db->select("units.name, units.id", FALSE);
	   $this->db->join('units', 'units.id=products.sale_unit', 'left');
	    $this->db->where("products.id = ".$term." ");
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }
	
	public function getpreparationProductSuggestions($term, $limit = 10)
    {
		
        $this->db->select("id, (CASE WHEN code = '-' THEN name ELSE CONCAT(code, ' - ', name, ' ') END) as text", FALSE);
        $this->db->where(" (id LIKE '%" . $term . "%' OR name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%') ");
        $q = $this->db->get('products', '', $limit);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }
	

    public function add_preparation($preparation = array())
    {
        if (!empty($preparation)) {
            foreach ($preparation as $preparation) {
                $variants = explode('|', $preparation['variants']);
                unset($preparation['variants']);
                if ($this->db->insert('preparation', $preparation)) {
                    $preparation_id = $this->db->insert_id();
                    foreach ($variants as $variant) {
                        if ($variant && trim($variant) != '') {
                            $vat = array('preparation_id' => $preparation_id, 'name' => trim($variant));
                            $this->db->insert('preparation_variants', $vat);
                        }
                    }
                }
            }
            return true;
        }
        return false;
    }

    public function getpreparationNames($term, $limit = 10)
    {
        $this->db->select('*')
			->where('type', 'standard')
            ->where("name LIKE '%" . $term . "%' ");
        $this->db->limit($limit);
        $q = $this->db->get('recipe');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getQASuggestions($term, $limit = 5)
    {
        $this->db->select('' . $this->db->dbprefix('preparation') . '.id, code, ' . $this->db->dbprefix('preparation') . '.name as name')
            ->where("type != 'combo' AND "
                . "(" . $this->db->dbprefix('preparation') . ".name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%' OR
                concat(" . $this->db->dbprefix('preparation') . ".name, ' (', code, ')') LIKE '%" . $term . "%')")
            ->limit($limit);
        $q = $this->db->get('preparation');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getpreparationForPrinting($term, $limit = 5)
    {
        $this->db->select('' . $this->db->dbprefix('preparation') . '.id, code, ' . $this->db->dbprefix('preparation') . '.name as name, ' . $this->db->dbprefix('preparation') . '.price as price')
            ->where("(" . $this->db->dbprefix('preparation') . ".name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%' OR
                concat(" . $this->db->dbprefix('preparation') . ".name, ' (', code, ')') LIKE '%" . $term . "%')")
            ->limit($limit);
        $q = $this->db->get('preparation');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function updatepreparation($id, $data, $items, $warehouse_qty, $preparation_attributes, $photos, $update_variants)
    {
        if ($this->db->update('preparation', $data, array('id' => $id))) {

            if ($items) {
                $this->db->delete('combo_items', array('preparation_id' => $id));
                foreach ($items as $item) {
                    $item['preparation_id'] = $id;
                    $this->db->insert('combo_items', $item);
                }
            }

            $tax_rate = $this->site->getTaxRateByID($data['tax_rate']);

            if ($warehouse_qty && !empty($warehouse_qty)) {
                foreach ($warehouse_qty as $wh_qty) {
                    $this->db->update('warehouses_preparation', array('rack' => $wh_qty['rack']), array('preparation_id' => $id, 'warehouse_id' => $wh_qty['warehouse_id']));
                }
            }

            if (!empty($update_variants)) {
                $this->db->update_batch('preparation_variants', $update_variants, 'id');
            }

            if ($photos) {
                foreach ($photos as $photo) {
                    $this->db->insert('preparation_photos', array('preparation_id' => $id, 'photo' => $photo));
                }
            }

            if ($preparation_attributes) {
                foreach ($preparation_attributes as $pr_attr) {

                    $pr_attr['preparation_id'] = $id;
                    $variant_warehouse_id = $pr_attr['warehouse_id'];
                    unset($pr_attr['warehouse_id']);
                    $this->db->insert('preparation_variants', $pr_attr);
                    $option_id = $this->db->insert_id();

                    if ($pr_attr['quantity'] != 0) {
                        $this->db->insert('warehouses_preparation_variants', array('option_id' => $option_id, 'preparation_id' => $id, 'warehouse_id' => $variant_warehouse_id, 'quantity' => $pr_attr['quantity']));

                        $tax_rate_id = $tax_rate ? $tax_rate->id : NULL;
                        $tax = $tax_rate ? (($tax_rate->type == 1) ? $tax_rate->rate . "%" : $tax_rate->rate) : NULL;
                        $unit_cost = $data['cost'];
                        if ($tax_rate) {
                            if ($tax_rate->type == 1 && $tax_rate->rate != 0) {
                                if ($data['tax_method'] == '0') {
                                    $pr_tax_val = ($data['cost'] * $tax_rate->rate) / (100 + $tax_rate->rate);
                                    $net_item_cost = $data['cost'] - $pr_tax_val;
                                    $item_tax = $pr_tax_val * $pr_attr['quantity'];
                                } else {
                                    $net_item_cost = $data['cost'];
                                    $pr_tax_val = ($data['cost'] * $tax_rate->rate) / 100;
                                    $unit_cost = $data['cost'] + $pr_tax_val;
                                    $item_tax = $pr_tax_val * $pr_attr['quantity'];
                                }
                            } else {
                                $net_item_cost = $data['cost'];
                                $item_tax = $tax_rate->rate;
                            }
                        } else {
                            $net_item_cost = $data['cost'];
                            $item_tax = 0;
                        }

                        $subtotal = (($net_item_cost * $pr_attr['quantity']) + $item_tax);
                        $item = array(
                            'preparation_id' => $id,
                            'preparation_code' => $data['code'],
                            'preparation_name' => $data['name'],
                            'net_unit_cost' => $net_item_cost,
                            'unit_cost' => $unit_cost,
                            'quantity' => $pr_attr['quantity'],
                            'option_id' => $option_id,
                            'quantity_balance' => $pr_attr['quantity'],
                            'quantity_received' => $pr_attr['quantity'],
                            'item_tax' => $item_tax,
                            'tax_rate_id' => $tax_rate_id,
                            'tax' => $tax,
                            'subtotal' => $subtotal,
                            'warehouse_id' => $variant_warehouse_id,
                            'date' => date('Y-m-d'),
                            'status' => 'received',
                        );
                        $item['option_id'] = !empty($item['option_id']) && is_numeric($item['option_id']) ? $item['option_id'] : NULL;
                        $this->db->insert('purchase_items', $item);

                    }
                }
            }

            $this->site->syncQuantity(NULL, NULL, NULL, $id);
            return true;
        } else {
            return false;
        }
    }
	
	public function updatepreparation_new($id, $preparation_array, $purchases_item)
    {
        
		if ($this->db->update('preparation', $preparation_array, array('id' => $id))) {
			$this->db->delete('preparation_items', array('preparation_id' => $id));
			if ($purchases_item && !empty($purchases_item)) {
				foreach ($purchases_item as $item) {
					$item['preparation_id'] = $id;
					$this->db->insert('preparation_items', $item);
				}
			}
            return true;
        }
        return false;
		
    }

    public function updatepreparationOptionQuantity($option_id, $warehouse_id, $quantity, $preparation_id)
    {
        if ($option = $this->getpreparationWarehouseOptionQty($option_id, $warehouse_id)) {
            if ($this->db->update('warehouses_preparation_variants', array('quantity' => $quantity), array('option_id' => $option_id, 'warehouse_id' => $warehouse_id))) {
                $this->site->syncVariantQty($option_id, $warehouse_id);
                return TRUE;
            }
        } else {
            if ($this->db->insert('warehouses_preparation_variants', array('option_id' => $option_id, 'preparation_id' => $preparation_id, 'warehouse_id' => $warehouse_id, 'quantity' => $quantity))) {
                $this->site->syncVariantQty($option_id, $warehouse_id);
                return TRUE;
            }
        }
        return FALSE;
    }

    public function updatePrice($data = array())
    {
        if ($this->db->update_batch('preparation', $data, 'code')) {
            return true;
        }
        return false;
    }

    public function deletepreparation($id)
    {
        if ($this->db->delete('preparation', array('id' => $id)) && $this->db->delete('warehouses_preparation', array('preparation_id' => $id))) {
            $this->db->delete('warehouses_preparation_variants', array('preparation_id' => $id));
            $this->db->delete('preparation_variants', array('preparation_id' => $id));
            $this->db->delete('preparation_photos', array('preparation_id' => $id));
            $this->db->delete('preparation_prices', array('preparation_id' => $id));
            return true;
        }
        return FALSE;
    }


    public function totalCategorypreparation($category_id)
    {
        $q = $this->db->get_where('preparation', array('category_id' => $category_id));
        return $q->num_rows();
    }

    public function getCategoryByCode($code)
    {
        $q = $this->db->get_where('preparation_categories', array('code' => $code), 1);
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

    public function getAdjustmentByID($id)
    {
        $q = $this->db->get_where('adjustments', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAdjustmentItems($adjustment_id)
    {
        $this->db->select('adjustment_items.*, preparation.code as preparation_code, preparation.name as preparation_name, preparation.image, preparation.details as details, preparation_variants.name as variant')
            ->join('preparation', 'preparation.id=adjustment_items.preparation_id', 'left')
            ->join('preparation_variants', 'preparation_variants.id=adjustment_items.option_id', 'left')
            ->group_by('adjustment_items.id')
            ->order_by('id', 'asc');

        $this->db->where('adjustment_id', $adjustment_id);

        $q = $this->db->get('adjustment_items');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function syncAdjustment($data = array())
    {
        if(! empty($data)) {
            $clause = array('preparation_id' => $data['preparation_id'], 'option_id' => $data['option_id'], 'warehouse_id' => $data['warehouse_id'], 'status' => 'received');
            $qty = $data['type'] == 'subtraction' ? 0 - $data['quantity'] : 0 + $data['quantity'];
            $this->site->setPurchaseItem($clause, $qty);

            $this->site->syncpreparationQty($data['preparation_id'], $data['warehouse_id']);
            if ($data['option_id']) {
                $this->site->syncVariantQty($data['option_id'], $data['warehouse_id'], $data['preparation_id']);
            }
        }
    }

    public function reverseAdjustment($id)
    {
        if ($preparation = $this->getAdjustmentItems($id)) {
            foreach ($preparation as $adjustment) {
                $clause = array('preparation_id' => $adjustment->preparation_id, 'warehouse_id' => $adjustment->warehouse_id, 'option_id' => $adjustment->option_id, 'status' => 'received');
                $qty = $adjustment->type == 'subtraction' ? (0+$adjustment->quantity) : (0-$adjustment->quantity);
                $this->site->setPurchaseItem($clause, $qty);
                $this->site->syncpreparationQty($adjustment->preparation_id, $adjustment->warehouse_id);
                if ($adjustment->option_id) {
                    $this->site->syncVariantQty($adjustment->option_id, $adjustment->warehouse_id, $adjustment->preparation_id);
                }
            }
        }
    }

    public function addAdjustment($data, $preparation)
    {
        if ($this->db->insert('adjustments', $data)) {
            $adjustment_id = $this->db->insert_id();
            foreach ($preparation as $preparation) {
                $preparation['adjustment_id'] = $adjustment_id;
                $this->db->insert('adjustment_items', $preparation);
                $this->syncAdjustment($preparation);
            }
            if ($this->site->getReference('qa') == $data['reference_no']) {
                $this->site->updateReference('qa');
            }
            return true;
        }
        return false;
    }

    public function updateAdjustment($id, $data, $preparation)
    {
        $this->reverseAdjustment($id);
        if ($this->db->update('adjustments', $data, array('id' => $id)) &&
            $this->db->delete('adjustment_items', array('adjustment_id' => $id))) {
            foreach ($preparation as $preparation) {
                $preparation['adjustment_id'] = $id;
                $this->db->insert('adjustment_items', $preparation);
                $this->syncAdjustment($preparation);
            }
            return true;
        }
        return false;
    }

    public function deleteAdjustment($id)
    {
        $this->reverseAdjustment($id);
        if ( $this->db->delete('adjustments', array('id' => $id)) &&
            $this->db->delete('adjustment_items', array('adjustment_id' => $id))) {
            return true;
        }
        return false;
    }

    public function getpreparationQuantity($preparation_id, $warehouse)
    {
        $q = $this->db->get_where('warehouses_preparation', array('preparation_id' => $preparation_id, 'warehouse_id' => $warehouse), 1);
        if ($q->num_rows() > 0) {
            return $q->row_array();
        }
        return FALSE;
    }

    public function addQuantity($preparation_id, $warehouse_id, $quantity, $rack = NULL)
    {

        if ($this->getpreparationQuantity($preparation_id, $warehouse_id)) {
            if ($this->updateQuantity($preparation_id, $warehouse_id, $quantity, $rack)) {
                return TRUE;
            }
        } else {
            if ($this->insertQuantity($preparation_id, $warehouse_id, $quantity, $rack)) {
                return TRUE;
            }
        }

        return FALSE;
    }

    public function insertQuantity($preparation_id, $warehouse_id, $quantity, $rack = NULL)
    {
        $preparation = $this->site->getpreparationByID($preparation_id);
        if ($this->db->insert('warehouses_preparation', array('preparation_id' => $preparation_id, 'warehouse_id' => $warehouse_id, 'quantity' => $quantity, 'rack' => $rack, 'avg_cost' => $preparation->cost))) {
            $this->site->syncpreparationQty($preparation_id, $warehouse_id);
            return true;
        }
        return false;
    }

    public function updateQuantity($preparation_id, $warehouse_id, $quantity, $rack = NULL)
    {
        $data = $rack ? array('quantity' => $quantity, 'rack' => $rack) : $data = array('quantity' => $quantity);
        if ($this->db->update('warehouses_preparation', $data, array('preparation_id' => $preparation_id, 'warehouse_id' => $warehouse_id))) {
            $this->site->syncpreparationQty($preparation_id, $warehouse_id);
            return true;
        }
        return false;
    }

    public function preparation_count($category_id, $subcategory_id = NULL)
    {
        if ($category_id) {
            $this->db->where('category_id', $category_id);
        }
        if ($subcategory_id) {
            $this->db->where('subcategory_id', $subcategory_id);
        }
        $this->db->from('preparation');
        return $this->db->count_all_results();
    }

    public function fetch_preparation($category_id, $limit, $start, $subcategory_id = NULL)
    {

        $this->db->limit($limit, $start);
        if ($category_id) {
            $this->db->where('category_id', $category_id);
        }
        if ($subcategory_id) {
            $this->db->where('subcategory_id', $subcategory_id);
        }
        $this->db->order_by("id", "asc");
        $query = $this->db->get("preparation");

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getpreparationWarehouseOptionQty($option_id, $warehouse_id)
    {
        $q = $this->db->get_where('warehouses_preparation_variants', array('option_id' => $option_id, 'warehouse_id' => $warehouse_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function syncVariantQty($option_id)
    {
        $wh_pr_vars = $this->getpreparationWarehouseOptions($option_id);
        $qty = 0;
        foreach ($wh_pr_vars as $row) {
            $qty += $row->quantity;
        }
        if ($this->db->update('preparation_variants', array('quantity' => $qty), array('id' => $option_id))) {
            return TRUE;
        }
        return FALSE;
    }

    public function getpreparationWarehouseOptions($option_id)
    {
        $q = $this->db->get_where('warehouses_preparation_variants', array('option_id' => $option_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function setRack($data)
    {
        if ($this->db->update('warehouses_preparation', array('rack' => $data['rack']), array('preparation_id' => $data['preparation_id'], 'warehouse_id' => $data['warehouse_id']))) {
            return TRUE;
        }
        return FALSE;
    }

    public function getSoldQty($id)
    {
        $this->db->select("date_format(" . $this->db->dbprefix('sales') . ".date, '%Y-%M') month, SUM( " . $this->db->dbprefix('sale_items') . ".quantity ) as sold, SUM( " . $this->db->dbprefix('sale_items') . ".subtotal ) as amount")
            ->from('sales')
            ->join('sale_items', 'sales.id=sale_items.sale_id', 'left')
            ->group_by("date_format(" . $this->db->dbprefix('sales') . ".date, '%Y-%m')")
            ->where($this->db->dbprefix('sale_items') . '.preparation_id', $id)
            //->where('DATE(NOW()) - INTERVAL 1 MONTH')
            ->where('DATE_ADD(curdate(), INTERVAL 1 MONTH)')
            ->order_by("date_format(" . $this->db->dbprefix('sales') . ".date, '%Y-%m') desc")->limit(3);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getPurchasedQty($id)
    {
        $this->db->select("date_format(" . $this->db->dbprefix('purchases') . ".date, '%Y-%M') month, SUM( " . $this->db->dbprefix('purchase_items') . ".quantity ) as purchased, SUM( " . $this->db->dbprefix('purchase_items') . ".subtotal ) as amount")
            ->from('purchases')
            ->join('purchase_items', 'purchases.id=purchase_items.purchase_id', 'left')
            ->group_by("date_format(" . $this->db->dbprefix('purchases') . ".date, '%Y-%m')")
            ->where($this->db->dbprefix('purchase_items') . '.preparation_id', $id)
            //->where('DATE(NOW()) - INTERVAL 1 MONTH')
            ->where('DATE_ADD(curdate(), INTERVAL 1 MONTH)')
            ->order_by("date_format(" . $this->db->dbprefix('purchases') . ".date, '%Y-%m') desc")->limit(3);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getAllVariants()
    {
        $q = $this->db->get('variants');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getWarehousepreparationVariant($warehouse_id, $preparation_id, $option_id = NULL)
    {
        $q = $this->db->get_where('warehouses_preparation_variants', array('preparation_id' => $preparation_id, 'option_id' => $option_id, 'warehouse_id' => $warehouse_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getPurchaseItems($purchase_id)
    {
        $q = $this->db->get_where('purchase_items', array('purchase_id' => $purchase_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getTransferItems($transfer_id)
    {
        $q = $this->db->get_where('purchase_items', array('transfer_id' => $transfer_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getUnitByCode($code)
    {
        $q = $this->db->get_where("units", array('code' => $code), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getBrandByName($name)
    {
        $q = $this->db->get_where('brands', array('name' => $name), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getStockCountpreparation($warehouse_id, $type, $categories = NULL, $brands = NULL)
    {
        $this->db->select("{$this->db->dbprefix('preparation')}.id as id, {$this->db->dbprefix('preparation')}.code as code, {$this->db->dbprefix('preparation')}.name as name, {$this->db->dbprefix('warehouses_preparation')}.quantity as quantity")
        ->join('warehouses_preparation', 'warehouses_preparation.preparation_id=preparation.id', 'left')
        ->where('warehouses_preparation.warehouse_id', $warehouse_id)
        ->where('preparation.type', 'standard')
        ->order_by('preparation.code', 'asc');
        if ($categories) {
            $r = 1;
            $this->db->group_start();
            foreach ($categories as $category) {
                if ($r == 1) {
                    $this->db->where('preparation.category_id', $category);
                } else {
                    $this->db->or_where('preparation.category_id', $category);
                }
                $r++;
            }
            $this->db->group_end();
        }
        if ($brands) {
            $r = 1;
            $this->db->group_start();
            foreach ($brands as $brand) {
                if ($r == 1) {
                    $this->db->where('preparation.brand', $brand);
                } else {
                    $this->db->or_where('preparation.brand', $brand);
                }
                $r++;
            }
            $this->db->group_end();
        }

        $q = $this->db->get('preparation');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getStockCountpreparationVariants($warehouse_id, $preparation_id)
    {
        $this->db->select("{$this->db->dbprefix('preparation_variants')}.name, {$this->db->dbprefix('warehouses_preparation_variants')}.quantity as quantity")
            ->join('warehouses_preparation_variants', 'warehouses_preparation_variants.option_id=preparation_variants.id', 'left');
        $q = $this->db->get_where('preparation_variants', array('preparation_variants.preparation_id' => $preparation_id, 'warehouses_preparation_variants.warehouse_id' => $warehouse_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function addStockCount($data)
    {
        if ($this->db->insert('stock_counts', $data)) {
            return TRUE;
        }
        return FALSE;
    }

    public function finalizeStockCount($id, $data, $preparation)
    {
        if ($this->db->update('stock_counts', $data, array('id' => $id))) {
            foreach ($preparation as $preparation) {
                $this->db->insert('stock_count_items', $preparation);
            }
            return TRUE;
        }
        return FALSE;
    }

    public function getStouckCountByID($id)
    {
        $q = $this->db->get_where("stock_counts", array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getStockCountItems($stock_count_id)
    {
        $q = $this->db->get_where("stock_count_items", array('stock_count_id' => $stock_count_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return NULL;
    }

    public function getAdjustmentByCountID($count_id)
    {
        $q = $this->db->get_where('adjustments', array('count_id' => $count_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getpreparationVariantID($preparation_id, $name)
    {
        $q = $this->db->get_where("preparation_variants", array('preparation_id' => $preparation_id, 'name' => $name), 1);
        if ($q->num_rows() > 0) {
            $variant = $q->row();
            return $variant->id;
        }
        return NULL;
    }

}
