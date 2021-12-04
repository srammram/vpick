<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Production_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getAllproduction()
    {
        $q = $this->db->get('production');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function getproductionItems($id)
    {
		$this->db->where('production_id', $id);
        $q = $this->db->get('production_items');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	

    public function getCategoryproduction($category_id)
    {
        $q = $this->db->get_where('production', array('category_id' => $category_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getSubCategoryproduction($subcategory_id)
    {
        $q = $this->db->get_where('production', array('subcategory_id' => $subcategory_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getproductionOptions($pid)
    {
        $q = $this->db->get_where('production_variants', array('production_id' => $pid));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getproductionOptionsWithWH($pid)
    {
        $this->db->select($this->db->dbprefix('production_variants') . '.*, ' . $this->db->dbprefix('warehouses') . '.name as wh_name, ' . $this->db->dbprefix('warehouses') . '.id as warehouse_id, ' . $this->db->dbprefix('warehouses_production_variants') . '.quantity as wh_qty')
            ->join('warehouses_production_variants', 'warehouses_production_variants.option_id=production_variants.id', 'left')
            ->join('warehouses', 'warehouses.id=warehouses_production_variants.warehouse_id', 'left')
            ->group_by(array('' . $this->db->dbprefix('production_variants') . '.id', '' . $this->db->dbprefix('warehouses_production_variants') . '.warehouse_id'))
            ->order_by('production_variants.id');
        $q = $this->db->get_where('production_variants', array('production_variants.production_id' => $pid, 'warehouses_production_variants.quantity !=' => NULL));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getproductionComboItems($pid)
    {
		
        $this->db->select($this->db->dbprefix('production') . '.id as id,   ' . $this->db->dbprefix('production') . '.name as name,  ' . $this->db->dbprefix('production_combo_items') . '.unit_price as price,  ' . $this->db->dbprefix('production') . '.code as code')->join('production', 'production.id=production_combo_items.item_id', 'left')->group_by('production_combo_items.id');
        $q = $this->db->get_where('production_combo_items', array('production_id' => $pid));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
        return FALSE;
    }

    public function getproductionByID($id)
    {
        $q = $this->db->get_where('production', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getproductionWithCategory($id)
    {
        $this->db->select($this->db->dbprefix('production') . '.*, ' . $this->db->dbprefix('production_categories') . '.name as category')
        ->join('production_categories', 'production_categories.id=production.category_id', 'left');
        $q = $this->db->get_where('production', array('production.id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function has_purchase($production_id, $warehouse_id = NULL)
    {
        if($warehouse_id) { $this->db->where('warehouse_id', $warehouse_id); }
        $q = $this->db->get_where('purchase_items', array('production_id' => $production_id), 1);
        if ($q->num_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }

    public function getproductionDetails($id)
    {
        $this->db->select($this->db->dbprefix('production') . '.code, ' . $this->db->dbprefix('production') . '.name, ' . $this->db->dbprefix('production_categories') . '.code as category_code, cost, price, quantity, alert_quantity')
            ->join('production_categories', 'production_categories.id=production.category_id', 'left');
        $q = $this->db->get_where('production', array('production.id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getproductionDetail($id)
    {
        $this->db->select($this->db->dbprefix('production') . '.*, ' . $this->db->dbprefix('tax_rates') . '.name as tax_rate_name, '.$this->db->dbprefix('tax_rates') . '.code as tax_rate_code, c.code as category_code, sc.code as subcategory_code', FALSE)
            ->join('tax_rates', 'tax_rates.id=production.tax_rate', 'left')
            ->join('production_categories c', 'c.id=production.category_id', 'left')
            ->join('production_categories sc', 'sc.id=production.subcategory_id', 'left');
        $q = $this->db->get_where('production', array('production.id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getproductionSubCategories($parent_id) {
        $this->db->select('id as id, name as text')
        ->where('parent_id', $parent_id)->order_by('name');
        $q = $this->db->get("production_categories");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getproductionByCategoryID($id)
    {

        $q = $this->db->get_where('production', array('category_id' => $id), 1);
        if ($q->num_rows() > 0) {
            return true;
        }
        return FALSE;
    }

    public function getAllWarehousesWithPQ($production_id)
    {
        $this->db->select('' . $this->db->dbprefix('warehouses') . '.*, ' . $this->db->dbprefix('warehouses_production') . '.quantity, ' . $this->db->dbprefix('warehouses_production') . '.rack')
            ->join('warehouses_production', 'warehouses_production.warehouse_id=warehouses.id', 'left')
            ->where('warehouses_production.production_id', $production_id)
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
	
	public function getAllProductWithRecipe($production_id)
    {
        $this->db->select('production_products.*, units.name AS units_name, products.name AS product_name')
			->join('units', 'units.id=production_products.units_id', 'left')
			->join('products', 'products.id=production_products.product_id', 'left')
            ->where('production_products.production_id', $production_id);
        	$q = $this->db->get('production_products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function getAllAddonWithRecipe($production_id)
    {
        $this->db->select('production_addon.*')
            ->where('production_addon.production_id', $production_id);
        	$q = $this->db->get('production_addon');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function getAllWarehouseWithRecipe($production_id)
    {
        $this->db->select('*')
            ->where('production_id', $production_id);
        	$q = $this->db->get('warehouses_production');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getproductionPhotos($id)
    {
        $q = $this->db->get_where("production_photos", array('production_id' => $id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getproductionByCode($code)
    {
        $q = $this->db->get_where('production', array('code' => $code), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function addproduction($data, $items, $warehouse_qty, $production_pro, $production_attributes, $photos)
    {
        if ($this->db->insert('production', $data)) {
            $production_id = $this->db->insert_id();
			
			

            if ($items) {
                foreach ($items as $item) {
                    $item['production_id'] = $production_id;
                    $this->db->insert('combo_items', $item);
                }
            }

            $warehouses = $this->site->getAllWarehouses();
            if ($data['type'] != 'standard') {
                foreach ($warehouses as $warehouse) {
                    $this->db->insert('warehouses_production', array('production_id' => $production_id, 'warehouse_id' => $warehouse->id, 'quantity' => 0));
                }
            }

            $tax_rate = $this->site->getTaxRateByID($data['tax_rate']);

            if ($warehouse_qty && !empty($warehouse_qty)) {
                foreach ($warehouse_qty as $wh_qty) {
					
                   
                        $this->db->insert('warehouses_production', array('production_id' => $production_id, 'warehouse_id' => $wh_qty['warehouse_id']));

                    
                   
                }
            }
			
			if ($production_pro && !empty($production_pro)) {
                foreach ($production_pro as $re_pro) {
					
                   
                        $this->db->insert('production_products', array('production_id' => $production_id, 'product_id' => $re_pro['product_id[]'], 'min_quantity' => $re_pro['min_quantity[]'], 'max_quantity' => $re_pro['max_quantity'], 'units_id' => $re_pro['units_id']));

                    
                   
                }
            }

            if ($production_attributes) {
                foreach ($production_attributes as $pr_attr) {
                    $pr_attr_details = $this->getPrductVariantByPIDandName($production_id, $pr_attr['name']);

                    $pr_attr['production_id'] = $production_id;
                    $variant_warehouse_id = $pr_attr['warehouse_id'];
                    unset($pr_attr['warehouse_id']);
                    if ($pr_attr_details) {
                        $option_id = $pr_attr_details->id;
                    } else {
                        $this->db->insert('production_variants', $pr_attr);
                        $option_id = $this->db->insert_id();
                    }
                    if ($pr_attr['quantity'] != 0) {
                        $this->db->insert('warehouses_production_variants', array('option_id' => $option_id, 'production_id' => $production_id, 'warehouse_id' => $variant_warehouse_id, 'quantity' => $pr_attr['quantity']));

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
                            'production_id' => $production_id,
                            'production_code' => $data['code'],
                            'production_name' => $data['name'],
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
                        if (!$this->getWarehouseproductionVariant($warehouse->id, $production_id, $option_id)) {
                            $this->db->insert('warehouses_production_variants', array('option_id' => $option_id, 'production_id' => $production_id, 'warehouse_id' => $warehouse->id, 'quantity' => 0));
                        }
                    }

                    $this->site->syncVariantQty($option_id, $variant_warehouse_id);
                }
            }

            if ($photos) {
                foreach ($photos as $photo) {
                    $this->db->insert('production_photos', array('production_id' => $production_id, 'photo' => $photo));
                }
            }

            return true;
        }
        return false;

    }
	
	public function addproduction_new($production_array, $purchases_item)
    {
        if ($this->db->insert('production', $production_array)) {
            $production_id = $this->db->insert_id();
			if ($purchases_item && !empty($purchases_item)) {
				foreach ($purchases_item as $item) {
					$item['production_id'] = $production_id;
					$this->db->insert('production_items', $item);
				}
			}
            return true;
        }
        return false;

    }

    public function getPrductVariantByPIDandName($production_id, $name)
    {
        $q = $this->db->get_where('production_variants', array('production_id' => $production_id, 'name' => $name), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function addAjaxproduction($data)
    {
        if ($this->db->insert('production', $data)) {
            $production_id = $this->db->insert_id();
            return $this->getproductionByID($production_id);
        }
        return false;
    }
	
	public function getproductionProductsalesUnits($term)
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
	
	public function getproductionProductSuggestions($term, $limit = 10)
    {
		
        $this->db->select("id, (CASE WHEN code = '-' THEN name ELSE CONCAT(code, ' - ', name, ' ') END) as text", FALSE);
        $this->db->where(" (id LIKE '%" . $term . "%' OR name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%') ");
        $q = $this->db->get('products', '', $limit);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			print_r($data);
			die;
            return $data;
        }
    }
	

    public function add_production($production = array())
    {
        if (!empty($production)) {
            foreach ($production as $production) {
                $variants = explode('|', $production['variants']);
                unset($production['variants']);
                if ($this->db->insert('production', $production)) {
                    $production_id = $this->db->insert_id();
                    foreach ($variants as $variant) {
                        if ($variant && trim($variant) != '') {
                            $vat = array('production_id' => $production_id, 'name' => trim($variant));
                            $this->db->insert('production_variants', $vat);
                        }
                    }
                }
            }
            return true;
        }
        return false;
    }

    public function getproductionNames($term, $limit = 10)
    {
        $this->db->select('*')
            ->where("name LIKE '%" . $term . "%' ");
        $this->db->limit($limit);
        $q = $this->db->get('products');
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
        $this->db->select('' . $this->db->dbprefix('production') . '.id, code, ' . $this->db->dbprefix('production') . '.name as name')
            ->where("type != 'combo' AND "
                . "(" . $this->db->dbprefix('production') . ".name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%' OR
                concat(" . $this->db->dbprefix('production') . ".name, ' (', code, ')') LIKE '%" . $term . "%')")
            ->limit($limit);
        $q = $this->db->get('production');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getproductionForPrinting($term, $limit = 5)
    {
        $this->db->select('' . $this->db->dbprefix('production') . '.id, code, ' . $this->db->dbprefix('production') . '.name as name, ' . $this->db->dbprefix('production') . '.price as price')
            ->where("(" . $this->db->dbprefix('production') . ".name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%' OR
                concat(" . $this->db->dbprefix('production') . ".name, ' (', code, ')') LIKE '%" . $term . "%')")
            ->limit($limit);
        $q = $this->db->get('production');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function updateproduction($id, $data, $items, $warehouse_qty, $production_attributes, $photos, $update_variants)
    {
        if ($this->db->update('production', $data, array('id' => $id))) {

            if ($items) {
                $this->db->delete('combo_items', array('production_id' => $id));
                foreach ($items as $item) {
                    $item['production_id'] = $id;
                    $this->db->insert('combo_items', $item);
                }
            }

            $tax_rate = $this->site->getTaxRateByID($data['tax_rate']);

            if ($warehouse_qty && !empty($warehouse_qty)) {
                foreach ($warehouse_qty as $wh_qty) {
                    $this->db->update('warehouses_production', array('rack' => $wh_qty['rack']), array('production_id' => $id, 'warehouse_id' => $wh_qty['warehouse_id']));
                }
            }

            if (!empty($update_variants)) {
                $this->db->update_batch('production_variants', $update_variants, 'id');
            }

            if ($photos) {
                foreach ($photos as $photo) {
                    $this->db->insert('production_photos', array('production_id' => $id, 'photo' => $photo));
                }
            }

            if ($production_attributes) {
                foreach ($production_attributes as $pr_attr) {

                    $pr_attr['production_id'] = $id;
                    $variant_warehouse_id = $pr_attr['warehouse_id'];
                    unset($pr_attr['warehouse_id']);
                    $this->db->insert('production_variants', $pr_attr);
                    $option_id = $this->db->insert_id();

                    if ($pr_attr['quantity'] != 0) {
                        $this->db->insert('warehouses_production_variants', array('option_id' => $option_id, 'production_id' => $id, 'warehouse_id' => $variant_warehouse_id, 'quantity' => $pr_attr['quantity']));

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
                            'production_id' => $id,
                            'production_code' => $data['code'],
                            'production_name' => $data['name'],
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
	
	public function updateproduction_new($id, $production_array, $purchases_item)
    {
        
		if ($this->db->update('production', $production_array, array('id' => $id))) {
			$this->db->delete('production_items', array('production_id' => $id));
			if ($purchases_item && !empty($purchases_item)) {
				foreach ($purchases_item as $item) {
					$item['production_id'] = $id;
					$this->db->insert('production_items', $item);
				}
			}
            return true;
        }
        return false;
		
    }

    public function updateproductionOptionQuantity($option_id, $warehouse_id, $quantity, $production_id)
    {
        if ($option = $this->getproductionWarehouseOptionQty($option_id, $warehouse_id)) {
            if ($this->db->update('warehouses_production_variants', array('quantity' => $quantity), array('option_id' => $option_id, 'warehouse_id' => $warehouse_id))) {
                $this->site->syncVariantQty($option_id, $warehouse_id);
                return TRUE;
            }
        } else {
            if ($this->db->insert('warehouses_production_variants', array('option_id' => $option_id, 'production_id' => $production_id, 'warehouse_id' => $warehouse_id, 'quantity' => $quantity))) {
                $this->site->syncVariantQty($option_id, $warehouse_id);
                return TRUE;
            }
        }
        return FALSE;
    }

    public function updatePrice($data = array())
    {
        if ($this->db->update_batch('production', $data, 'code')) {
            return true;
        }
        return false;
    }

    public function deleteproduction($id)
    {
        if ($this->db->delete('production', array('id' => $id)) && $this->db->delete('warehouses_production', array('production_id' => $id))) {
            $this->db->delete('warehouses_production_variants', array('production_id' => $id));
            $this->db->delete('production_variants', array('production_id' => $id));
            $this->db->delete('production_photos', array('production_id' => $id));
            $this->db->delete('production_prices', array('production_id' => $id));
            return true;
        }
        return FALSE;
    }


    public function totalCategoryproduction($category_id)
    {
        $q = $this->db->get_where('production', array('category_id' => $category_id));
        return $q->num_rows();
    }

    public function getCategoryByCode($code)
    {
        $q = $this->db->get_where('production_categories', array('code' => $code), 1);
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
        $this->db->select('adjustment_items.*, production.code as production_code, production.name as production_name, production.image, production.details as details, production_variants.name as variant')
            ->join('production', 'production.id=adjustment_items.production_id', 'left')
            ->join('production_variants', 'production_variants.id=adjustment_items.option_id', 'left')
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
            $clause = array('production_id' => $data['production_id'], 'option_id' => $data['option_id'], 'warehouse_id' => $data['warehouse_id'], 'status' => 'received');
            $qty = $data['type'] == 'subtraction' ? 0 - $data['quantity'] : 0 + $data['quantity'];
            $this->site->setPurchaseItem($clause, $qty);

            $this->site->syncproductionQty($data['production_id'], $data['warehouse_id']);
            if ($data['option_id']) {
                $this->site->syncVariantQty($data['option_id'], $data['warehouse_id'], $data['production_id']);
            }
        }
    }

    public function reverseAdjustment($id)
    {
        if ($production = $this->getAdjustmentItems($id)) {
            foreach ($production as $adjustment) {
                $clause = array('production_id' => $adjustment->production_id, 'warehouse_id' => $adjustment->warehouse_id, 'option_id' => $adjustment->option_id, 'status' => 'received');
                $qty = $adjustment->type == 'subtraction' ? (0+$adjustment->quantity) : (0-$adjustment->quantity);
                $this->site->setPurchaseItem($clause, $qty);
                $this->site->syncproductionQty($adjustment->production_id, $adjustment->warehouse_id);
                if ($adjustment->option_id) {
                    $this->site->syncVariantQty($adjustment->option_id, $adjustment->warehouse_id, $adjustment->production_id);
                }
            }
        }
    }

    public function addAdjustment($data, $production)
    {
        if ($this->db->insert('adjustments', $data)) {
            $adjustment_id = $this->db->insert_id();
            foreach ($production as $production) {
                $production['adjustment_id'] = $adjustment_id;
                $this->db->insert('adjustment_items', $production);
                $this->syncAdjustment($production);
            }
            if ($this->site->getReference('qa') == $data['reference_no']) {
                $this->site->updateReference('qa');
            }
            return true;
        }
        return false;
    }

    public function updateAdjustment($id, $data, $production)
    {
        $this->reverseAdjustment($id);
        if ($this->db->update('adjustments', $data, array('id' => $id)) &&
            $this->db->delete('adjustment_items', array('adjustment_id' => $id))) {
            foreach ($production as $production) {
                $production['adjustment_id'] = $id;
                $this->db->insert('adjustment_items', $production);
                $this->syncAdjustment($production);
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

    public function getproductionQuantity($production_id, $warehouse)
    {
        $q = $this->db->get_where('warehouses_production', array('production_id' => $production_id, 'warehouse_id' => $warehouse), 1);
        if ($q->num_rows() > 0) {
            return $q->row_array();
        }
        return FALSE;
    }

    public function addQuantity($production_id, $warehouse_id, $quantity, $rack = NULL)
    {

        if ($this->getproductionQuantity($production_id, $warehouse_id)) {
            if ($this->updateQuantity($production_id, $warehouse_id, $quantity, $rack)) {
                return TRUE;
            }
        } else {
            if ($this->insertQuantity($production_id, $warehouse_id, $quantity, $rack)) {
                return TRUE;
            }
        }

        return FALSE;
    }

    public function insertQuantity($production_id, $warehouse_id, $quantity, $rack = NULL)
    {
        $production = $this->site->getproductionByID($production_id);
        if ($this->db->insert('warehouses_production', array('production_id' => $production_id, 'warehouse_id' => $warehouse_id, 'quantity' => $quantity, 'rack' => $rack, 'avg_cost' => $production->cost))) {
            $this->site->syncproductionQty($production_id, $warehouse_id);
            return true;
        }
        return false;
    }

    public function updateQuantity($production_id, $warehouse_id, $quantity, $rack = NULL)
    {
        $data = $rack ? array('quantity' => $quantity, 'rack' => $rack) : $data = array('quantity' => $quantity);
        if ($this->db->update('warehouses_production', $data, array('production_id' => $production_id, 'warehouse_id' => $warehouse_id))) {
            $this->site->syncproductionQty($production_id, $warehouse_id);
            return true;
        }
        return false;
    }

    public function production_count($category_id, $subcategory_id = NULL)
    {
        if ($category_id) {
            $this->db->where('category_id', $category_id);
        }
        if ($subcategory_id) {
            $this->db->where('subcategory_id', $subcategory_id);
        }
        $this->db->from('production');
        return $this->db->count_all_results();
    }

    public function fetch_production($category_id, $limit, $start, $subcategory_id = NULL)
    {

        $this->db->limit($limit, $start);
        if ($category_id) {
            $this->db->where('category_id', $category_id);
        }
        if ($subcategory_id) {
            $this->db->where('subcategory_id', $subcategory_id);
        }
        $this->db->order_by("id", "asc");
        $query = $this->db->get("production");

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getproductionWarehouseOptionQty($option_id, $warehouse_id)
    {
        $q = $this->db->get_where('warehouses_production_variants', array('option_id' => $option_id, 'warehouse_id' => $warehouse_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function syncVariantQty($option_id)
    {
        $wh_pr_vars = $this->getproductionWarehouseOptions($option_id);
        $qty = 0;
        foreach ($wh_pr_vars as $row) {
            $qty += $row->quantity;
        }
        if ($this->db->update('production_variants', array('quantity' => $qty), array('id' => $option_id))) {
            return TRUE;
        }
        return FALSE;
    }

    public function getproductionWarehouseOptions($option_id)
    {
        $q = $this->db->get_where('warehouses_production_variants', array('option_id' => $option_id));
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
        if ($this->db->update('warehouses_production', array('rack' => $data['rack']), array('production_id' => $data['production_id'], 'warehouse_id' => $data['warehouse_id']))) {
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
            ->where($this->db->dbprefix('sale_items') . '.production_id', $id)
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
            ->where($this->db->dbprefix('purchase_items') . '.production_id', $id)
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

    public function getWarehouseproductionVariant($warehouse_id, $production_id, $option_id = NULL)
    {
        $q = $this->db->get_where('warehouses_production_variants', array('production_id' => $production_id, 'option_id' => $option_id, 'warehouse_id' => $warehouse_id), 1);
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

    public function getStockCountproduction($warehouse_id, $type, $categories = NULL, $brands = NULL)
    {
        $this->db->select("{$this->db->dbprefix('production')}.id as id, {$this->db->dbprefix('production')}.code as code, {$this->db->dbprefix('production')}.name as name, {$this->db->dbprefix('warehouses_production')}.quantity as quantity")
        ->join('warehouses_production', 'warehouses_production.production_id=production.id', 'left')
        ->where('warehouses_production.warehouse_id', $warehouse_id)
        ->where('production.type', 'standard')
        ->order_by('production.code', 'asc');
        if ($categories) {
            $r = 1;
            $this->db->group_start();
            foreach ($categories as $category) {
                if ($r == 1) {
                    $this->db->where('production.category_id', $category);
                } else {
                    $this->db->or_where('production.category_id', $category);
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
                    $this->db->where('production.brand', $brand);
                } else {
                    $this->db->or_where('production.brand', $brand);
                }
                $r++;
            }
            $this->db->group_end();
        }

        $q = $this->db->get('production');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getStockCountproductionVariants($warehouse_id, $production_id)
    {
        $this->db->select("{$this->db->dbprefix('production_variants')}.name, {$this->db->dbprefix('warehouses_production_variants')}.quantity as quantity")
            ->join('warehouses_production_variants', 'warehouses_production_variants.option_id=production_variants.id', 'left');
        $q = $this->db->get_where('production_variants', array('production_variants.production_id' => $production_id, 'warehouses_production_variants.warehouse_id' => $warehouse_id));
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

    public function finalizeStockCount($id, $data, $production)
    {
        if ($this->db->update('stock_counts', $data, array('id' => $id))) {
            foreach ($production as $production) {
                $this->db->insert('stock_count_items', $production);
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

    public function getproductionVariantID($production_id, $name)
    {
        $q = $this->db->get_where("production_variants", array('production_id' => $production_id, 'name' => $name), 1);
        if ($q->num_rows() > 0) {
            $variant = $q->row();
            return $variant->id;
        }
        return NULL;
    }

}
