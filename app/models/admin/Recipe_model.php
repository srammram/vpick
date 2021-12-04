<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Recipe_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getAllrecipe()
    {
        $q = $this->db->get('recipe');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getCategoryrecipe($category_id)
    {
        $q = $this->db->get_where('recipe', array('category_id' => $category_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getSubCategoryrecipe($subcategory_id)
    {
        $q = $this->db->get_where('recipe', array('subcategory_id' => $subcategory_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getrecipeOptions($pid)
    {
        $q = $this->db->get_where('recipe_variants', array('recipe_id' => $pid));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getrecipeOptionsWithWH($pid)
    {
        $this->db->select($this->db->dbprefix('recipe_variants') . '.*, ' . $this->db->dbprefix('warehouses') . '.name as wh_name, ' . $this->db->dbprefix('warehouses') . '.id as warehouse_id, ' . $this->db->dbprefix('warehouses_recipe_variants') . '.quantity as wh_qty')
            ->join('warehouses_recipe_variants', 'warehouses_recipe_variants.option_id=recipe_variants.id', 'left')
            ->join('warehouses', 'warehouses.id=warehouses_recipe_variants.warehouse_id', 'left')
            ->group_by(array('' . $this->db->dbprefix('recipe_variants') . '.id', '' . $this->db->dbprefix('warehouses_recipe_variants') . '.warehouse_id'))
            ->order_by('recipe_variants.id');
        $q = $this->db->get_where('recipe_variants', array('recipe_variants.recipe_id' => $pid, 'warehouses_recipe_variants.quantity !=' => NULL));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getrecipeComboItems($pid)
    {
		
        $this->db->select($this->db->dbprefix('recipe') . '.id as id,   ' . $this->db->dbprefix('recipe') . '.name as name,  ' . $this->db->dbprefix('recipe_combo_items') . '.unit_price as price,  ' . $this->db->dbprefix('recipe') . '.code as code')->join('recipe', 'recipe.id=recipe_combo_items.item_id', 'left')->group_by('recipe_combo_items.id');
        $q = $this->db->get_where('recipe_combo_items', array('recipe_id' => $pid));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
        return FALSE;
    }

    public function getrecipeByID($id)
    {
        $q = $this->db->get_where('recipe', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getrecipeWithCategory($id)
    {
        $this->db->select($this->db->dbprefix('recipe') . '.*, ' . $this->db->dbprefix('recipe_categories') . '.name as category')
        ->join('recipe_categories', 'recipe_categories.id=recipe.category_id', 'left');
        $q = $this->db->get_where('recipe', array('recipe.id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function has_purchase($recipe_id, $warehouse_id = NULL)
    {
        if($warehouse_id) { $this->db->where('warehouse_id', $warehouse_id); }
        $q = $this->db->get_where('purchase_items', array('recipe_id' => $recipe_id), 1);
        if ($q->num_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }

    public function getrecipeDetails($id)
    {
        $this->db->select($this->db->dbprefix('recipe') . '.code, ' . $this->db->dbprefix('recipe') . '.name, ' . $this->db->dbprefix('recipe_categories') . '.code as category_code, cost, price, quantity, alert_quantity')
            ->join('recipe_categories', 'recipe_categories.id=recipe.category_id', 'left');
        $q = $this->db->get_where('recipe', array('recipe.id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getrecipeDetail($id)
    {
        $this->db->select($this->db->dbprefix('recipe') . '.*, ' . $this->db->dbprefix('tax_rates') . '.name as tax_rate_name, '.$this->db->dbprefix('tax_rates') . '.code as tax_rate_code, c.code as category_code, sc.code as subcategory_code', FALSE)
            ->join('tax_rates', 'tax_rates.id=recipe.tax_rate', 'left')
            ->join('recipe_categories c', 'c.id=recipe.category_id', 'left')
            ->join('recipe_categories sc', 'sc.id=recipe.subcategory_id', 'left');
        $q = $this->db->get_where('recipe', array('recipe.id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getrecipeSubCategories($parent_id) {
        $this->db->select('id as id, name as text')
        ->where('parent_id', $parent_id)->order_by('name');
        $q = $this->db->get("recipe_categories");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getrecipeCategories() {
        $this->db->select('id as id, name as text')
        ->where('parent_id', 0)->order_by('name');
        $q = $this->db->get("recipe_categories");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getPurchaseCategories() {
        $this->db->select('id as id, name as text')
        ->where('parent_id', 0)->order_by('name');
        $q = $this->db->get("categories");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getPurchaseSubCategories($parent_id) {
        $this->db->select('id as id, name as text')
        ->where('parent_id', $parent_id)->order_by('name');
        $q = $this->db->get("categories");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getrecipeByCategoryID($id)
    {

        $q = $this->db->get_where('recipe', array('category_id' => $id), 1);
        if ($q->num_rows() > 0) {
            return true;
        }
        return FALSE;
    }

    public function getAllWarehousesWithPQ($recipe_id)
    {
        $this->db->select('' . $this->db->dbprefix('warehouses') . '.*, ' . $this->db->dbprefix('warehouses_recipe') . '.quantity, ' . $this->db->dbprefix('warehouses_recipe') . '.rack')
            ->join('warehouses_recipe', 'warehouses_recipe.warehouse_id=warehouses.id', 'left')
            ->where('warehouses_recipe.recipe_id', $recipe_id)
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
	
	public function getAllProductWithRecipe($recipe_id)
    {
        $this->db->select('recipe_products.*, units.name AS units_name, recipe.name AS recipe_name,recipe.code')
			->join('units', 'units.id=recipe_products.unit_id', 'left')
			->join('recipe', 'recipe.id=recipe_products.product_id', 'left')
            ->where('recipe_products.recipe_id', $recipe_id);
	    //echo $this->db->get_compiled_select();exit;
        	$q = $this->db->get('recipe_products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function getAllAddonWithRecipe($recipe_id)
    {
        $this->db->select('recipe_addon.*, recipe.name AS addon_name')
			->join('recipe', 'recipe.id = recipe_addon.addon_id')
            ->where('recipe_addon.recipe_id', $recipe_id);
        	$q = $this->db->get('recipe_addon');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function getAllWarehouseWithRecipe($recipe_id)
    {
        $this->db->select('*')
            ->where('recipe_id', $recipe_id);
        	$q = $this->db->get('warehouses_recipe');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getrecipePhotos($id)
    {
        $q = $this->db->get_where("recipe_photos", array('recipe_id' => $id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getrecipeByCode($code)
    {
        $q = $this->db->get_where('recipe', array('code' => $code), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function addrecipe($data, $items, $warehouse_qty, $recipe_pro, $recipe_attributes, $photos)
    {
        if ($this->db->insert('recipe', $data)) {
            $recipe_id = $this->db->insert_id();
			
			

            if ($items) {
                foreach ($items as $item) {
                    $item['recipe_id'] = $recipe_id;
                    $this->db->insert('combo_items', $item);
                }
            }

            $warehouses = $this->site->getAllWarehouses();
            if ($data['type'] != 'standard') {
                foreach ($warehouses as $warehouse) {
                    $this->db->insert('warehouses_recipe', array('recipe_id' => $recipe_id, 'warehouse_id' => $warehouse->id, 'quantity' => 0));
                }
            }

            $tax_rate = $this->site->getTaxRateByID($data['tax_rate']);

            if ($warehouse_qty && !empty($warehouse_qty)) {
                foreach ($warehouse_qty as $wh_qty) {
					
                   
                        $this->db->insert('warehouses_recipe', array('recipe_id' => $recipe_id, 'warehouse_id' => $wh_qty['warehouse_id']));

                    
                   
                }
            }
			
			if ($recipe_pro && !empty($recipe_pro)) {
                foreach ($recipe_pro as $re_pro) {
					
                   
                        $this->db->insert('recipe_products', array('recipe_id' => $recipe_id, 'product_id' => $re_pro['product_id'], 'min_quantity' => $re_pro['min_quantity'], 'max_quantity' => $re_pro['max_quantity'], 'units_id' => $re_pro['units_id'], 'bbq_min_quantity' => $re_pro['bbq_min_quantity'], 'bbq_max_quantity' => $re_pro['bbq_max_quantity'], 'bbq_units_id' => $re_pro['bbq_units_id']));

                    
                   
                }
            }

            if ($recipe_attributes) {
                foreach ($recipe_attributes as $pr_attr) {
                    $pr_attr_details = $this->getPrductVariantByPIDandName($recipe_id, $pr_attr['name']);

                    $pr_attr['recipe_id'] = $recipe_id;
                    $variant_warehouse_id = $pr_attr['warehouse_id'];
                    unset($pr_attr['warehouse_id']);
                    if ($pr_attr_details) {
                        $option_id = $pr_attr_details->id;
                    } else {
                        $this->db->insert('recipe_variants', $pr_attr);
                        $option_id = $this->db->insert_id();
                    }
                    if ($pr_attr['quantity'] != 0) {
                        $this->db->insert('warehouses_recipe_variants', array('option_id' => $option_id, 'recipe_id' => $recipe_id, 'warehouse_id' => $variant_warehouse_id, 'quantity' => $pr_attr['quantity']));

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
                            'recipe_id' => $recipe_id,
                            'recipe_code' => $data['code'],
                            'recipe_name' => $data['name'],
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
                        if (!$this->getWarehouserecipeVariant($warehouse->id, $recipe_id, $option_id)) {
                            $this->db->insert('warehouses_recipe_variants', array('option_id' => $option_id, 'recipe_id' => $recipe_id, 'warehouse_id' => $warehouse->id, 'quantity' => 0));
                        }
                    }

                    $this->site->syncVariantQty($option_id, $variant_warehouse_id);
                }
            }

            if ($photos) {
                foreach ($photos as $photo) {
                    $this->db->insert('recipe_photos', array('recipe_id' => $recipe_id, 'photo' => $photo));
                }
            }
            ///// add varients /////////////////
            $files = $_FILES;
            if(isset($_POST['varients'])){
            $varients = $_POST['varients'];
            $cnt = count($varients['id']);
            $add_varient = array();
            for($i=0;$i<$cnt;$i++){
                $add_varient[$i]['price'] = $varients['price'][$i];
                $add_varient[$i]['status'] = $varients['status'][$i];
                $add_varient[$i]['attr_id'] = $varients['id'][$i];
                $add_varient[$i]['recipe_id'] = $recipe_id;
            if(isset($_FILES['varients_file']['name'][$i]) && $_FILES['varients_file']['name'][$i]!=''){
                $config['upload_path'] = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                //$config['max_size'] = $this->allowed_file_size;
                //$config['max_width'] = $this->Settings->iwidth;
                //$config['max_height'] = $this->Settings->iheight;
                //$config['overwrite'] = FALSE;
                //$config['encrypt_name'] = TRUE;
                //$config['max_filename'] = 25;
               
                        $_FILES['variants']['name'] = $files['varients_file']['name'][$i];
                        $_FILES['variants']['type'] = $files['varients_file']['type'][$i];
                        $_FILES['variants']['tmp_name'] = $files['varients_file']['tmp_name'][$i];
                        $_FILES['variants']['error'] = $files['varients_file']['error'][$i];
                        $_FILES['variants']['size'] = $files['varients_file']['size'][$i];

                        $this->upload->initialize($config);

                        if (!$this->upload->do_upload('variants')) {
                            $error = $this->upload->display_errors();echo $error;
                            $this->session->set_flashdata('error', $error);
                           // admin_redirect("recipe/add");
                        } else {

                            $pho = $this->upload->file_name;

                            $add_varient[$i]['image'] = $pho;
                            //$this->image_lib->clear();
                        }
                    
                $config = NULL;
            } else {
                $add_varient[$i]['image'] = '';
            }
                
               
            }
            if(!empty($add_varient)){
                $this->AddRecipeVarient($add_varient);
            }
            
            }
            
            ////// -- add varients end --///////
            return true;
        }
        return false;

    }
	
	public function addrecipe_new($data, $warehouse_qty, $recipe_pro, $recipe_aon, $items, $photos)
    {
	$this->db->insert('recipe', $data);file_put_contents('addrecipe.txt',json_encode($this->db->error()),FILE_APPEND);
                    $recipe_id = $this->db->insert_id();

if ($recipe_id) {
            $recipe_id = $this->db->insert_id();
			
			if ($warehouse_qty && !empty($warehouse_qty)) {
					foreach ($warehouse_qty as $wh_qty) {
						$this->db->insert('warehouses_recipe', array('recipe_id' => $recipe_id, 'warehouse_id' => $wh_qty['warehouse_id']));
					}
				}
			
			if($data['type'] == 'standard' || $data['type'] == 'production' || $data['type'] == 'combo'){
				if ($recipe_aon && !empty($recipe_aon)) {
					foreach ($recipe_aon as $addon_row) {
						$this->db->insert('recipe_addon', array('recipe_id' => $id, 'addon_id' => $addon_row['recipe_addon']));
					}	
				}
			}
			
				
			if ($data['type'] == 'addon' || $data['type'] == 'production' || $data['type'] == 'semi_finished') {
				
				
				
				if ($recipe_pro && !empty($recipe_pro)) {
					foreach ($recipe_pro as $re_pro) {
					    $re_pro['recipe_id'] = $recipe_id;
					    $re_pro['create_on'] = date('Y-m-d H:i:s');
					    $this->db->insert('recipe_products', $re_pro);					   
					}
				}
				
			}
			if($data['type'] == 'combo'){
				
				if ($items && !empty($items)) {
					foreach ($items as $item) {
						$item['recipe_id'] = $recipe_id;
						$this->db->insert('recipe_combo_items', $item);
					}
				}
			}
			
           
			
            if ($photos) {
                foreach ($photos as $photo) {
                    $this->db->insert('recipe_photos', array('recipe_id' => $recipe_id, 'photo' => $photo));
                }
            }
///// add varients /////////////////
            $files = $_FILES;
            if(isset($_POST['varients'])){
            $varients = $_POST['varients'];
            $cnt = count($varients['id']);
            $add_varient = array();
            for($i=0;$i<$cnt;$i++){
                $add_varient[$i]['price'] = $varients['price'][$i];
                $add_varient[$i]['status'] = $varients['status'][$i];
                $add_varient[$i]['attr_id'] = $varients['id'][$i];
                $add_varient[$i]['recipe_id'] = $recipe_id;
            if(isset($_FILES['varients_file']['name'][$i]) && $_FILES['varients_file']['name'][$i]!=''){
                $config['upload_path'] = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                //$config['max_size'] = $this->allowed_file_size;
                //$config['max_width'] = $this->Settings->iwidth;
                //$config['max_height'] = $this->Settings->iheight;
                //$config['overwrite'] = FALSE;
                //$config['encrypt_name'] = TRUE;
                //$config['max_filename'] = 25;
               
                        $_FILES['variants']['name'] = $files['varients_file']['name'][$i];
                        $_FILES['variants']['type'] = $files['varients_file']['type'][$i];
                        $_FILES['variants']['tmp_name'] = $files['varients_file']['tmp_name'][$i];
                        $_FILES['variants']['error'] = $files['varients_file']['error'][$i];
                        $_FILES['variants']['size'] = $files['varients_file']['size'][$i];

                        $this->upload->initialize($config);

                        if (!$this->upload->do_upload('variants')) {
                            $error = $this->upload->display_errors();echo $error;
                            $this->session->set_flashdata('error', $error);
                           // admin_redirect("recipe/add");
                        } else {

                            $pho = $this->upload->file_name;

                            $add_varient[$i]['image'] = $pho;
                            //$this->image_lib->clear();
                        }
                    
                $config = NULL;
            } else {
                $add_varient[$i]['image'] = '';
            }
                
               
            }
            if(!empty($add_varient)){
                $this->AddRecipeVarient($add_varient);
            }
            
            }
            
            ////// -- add varients end --///////
            return true;
        }
        return false;

    }

    public function getPrductVariantByPIDandName($recipe_id, $name)
    {
        $q = $this->db->get_where('recipe_variants', array('recipe_id' => $recipe_id, 'name' => $name), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function addAjaxrecipe($data)
    {
        if ($this->db->insert('recipe', $data)) {
            $recipe_id = $this->db->insert_id();
            return $this->getrecipeByID($recipe_id);
        }
        return false;
    }
	
	public function getrecipeProductsalesUnits($term)
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
	
	public function getrecipeProductSuggestions($term, $limit = 10)
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
        return '';
    }
	
	public function getrecipeAddonSuggestions($term, $limit = 10)
    {
		
        $this->db->select("id, (CASE WHEN code = '-' THEN name ELSE CONCAT(code, ' - ', name, ' ') END) as text", FALSE);
        $this->db->where(" type = 'addon' AND (id LIKE '%" . $term . "%' OR name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%') ");
        $q = $this->db->get('recipe', '', $limit);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
         return '';
    }

    public function add_recipe($recipe = array())
    {
        if (!empty($recipe)) {
            foreach ($recipe as $recipe) {
				
				  $warehouse_id = explode('|', $recipe['warehouse_id']);
				  				  
				  unset($recipe['warehouse_id']);
				  
                if ($this->db->insert('recipe', $recipe)) {
                    $recipe_id = $this->db->insert_id();
                    foreach ($warehouse_id as $warehouse) {
                        if ($warehouse && trim($warehouse) != '') {
                            $vat = array('recipe_id' => $recipe_id, 'warehouse_id' => trim($warehouse));
                            $this->db->insert('warehouses_recipe', $vat);
                        }
                    }
                }
            }
            return true;
        }
        return false;
    }

    public function getrecipeNames($term, $limit = 10)
    {
        $this->db->select('*')
            ->where("type != 'combo' AND name LIKE '%" . $term . "%' ");
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
        $this->db->select('' . $this->db->dbprefix('recipe') . '.id, code, ' . $this->db->dbprefix('recipe') . '.name as name')
            ->where("type != 'combo' AND "
                . "(" . $this->db->dbprefix('recipe') . ".name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%' OR
                concat(" . $this->db->dbprefix('recipe') . ".name, ' (', code, ')') LIKE '%" . $term . "%')")
            ->limit($limit);
        $q = $this->db->get('recipe');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getrecipeForPrinting($term, $limit = 5)
    {
        $this->db->select('' . $this->db->dbprefix('recipe') . '.id, code, ' . $this->db->dbprefix('recipe') . '.name as name, ' . $this->db->dbprefix('recipe') . '.price as price')
            ->where("(" . $this->db->dbprefix('recipe') . ".name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%' OR
                concat(" . $this->db->dbprefix('recipe') . ".name, ' (', code, ')') LIKE '%" . $term . "%')")
            ->limit($limit);
        $q = $this->db->get('recipe');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function updaterecipe($id, $data, $items, $warehouse_qty, $recipe_attributes, $photos, $update_variants)
    {
        if ($this->db->update('recipe', $data, array('id' => $id))) {

            if ($items) {
                $this->db->delete('combo_items', array('recipe_id' => $id));
                foreach ($items as $item) {
                    $item['recipe_id'] = $id;
                    $this->db->insert('combo_items', $item);
                }
            }

            $tax_rate = $this->site->getTaxRateByID($data['tax_rate']);

            if ($warehouse_qty && !empty($warehouse_qty)) {
                foreach ($warehouse_qty as $wh_qty) {
                    $this->db->update('warehouses_recipe', array('rack' => $wh_qty['rack']), array('recipe_id' => $id, 'warehouse_id' => $wh_qty['warehouse_id']));
                }
            }

            if (!empty($update_variants)) {
                $this->db->update_batch('recipe_variants', $update_variants, 'id');
            }

            if ($photos) {
                foreach ($photos as $photo) {
                    $this->db->insert('recipe_photos', array('recipe_id' => $id, 'photo' => $photo));
                }
            }

            if ($recipe_attributes) {
                foreach ($recipe_attributes as $pr_attr) {

                    $pr_attr['recipe_id'] = $id;
                    $variant_warehouse_id = $pr_attr['warehouse_id'];
                    unset($pr_attr['warehouse_id']);
                    $this->db->insert('recipe_variants', $pr_attr);
                    $option_id = $this->db->insert_id();

                    if ($pr_attr['quantity'] != 0) {
                        $this->db->insert('warehouses_recipe_variants', array('option_id' => $option_id, 'recipe_id' => $id, 'warehouse_id' => $variant_warehouse_id, 'quantity' => $pr_attr['quantity']));

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
                            'recipe_id' => $id,
                            'recipe_code' => $data['code'],
                            'recipe_name' => $data['name'],
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
	
	public function updaterecipe_new($id, $data, $warehouse_qty, $recipe_pro, $recipe_aon, $items, $photos)
    {
        if ($this->db->update('recipe', $data, array('id' => $id))) {	
			
			$this->db->delete('recipe_products', array('recipe_id' => $id));
			$this->db->delete('warehouses_recipe', array('recipe_id' => $id));
			$this->db->delete('recipe_combo_items', array('recipe_id' => $id));
			$this->db->delete('recipe_addon', array('recipe_id' => $id));
			
			if ($warehouse_qty && !empty($warehouse_qty)) {
					foreach ($warehouse_qty as $wh_qty) {
						$this->db->insert('warehouses_recipe', array('recipe_id' => $id, 'warehouse_id' => $wh_qty['warehouse_id']));
					}
				}
			if($data['type'] != 'addon'){
				if ($recipe_aon && !empty($recipe_aon)) {
					foreach ($recipe_aon as $addon_row) {
						$this->db->insert('recipe_addon', array('recipe_id' => $id, 'addon_id' => $addon_row['recipe_addon']));
					}	
				}
			}
			
			if ($data['type'] == 'addon' || $data['type'] == 'production' || $data['type'] == 'semi_finished') {
				
				
				if ($recipe_pro && !empty($recipe_pro)) {
					foreach ($recipe_pro as $re_pro) {
					    $re_pro['recipe_id'] = $id;
					    $re_pro['create_on'] = date('Y-m-d H:i:s');
					    $this->db->insert('recipe_products', $re_pro);
					    				   
					}
				}
				
			}
			if($data['type'] == 'combo'){
				if ($items && !empty($items)) {
					foreach ($items as $item) {
						$item['recipe_id'] = $id;
						$this->db->insert('recipe_combo_items', $item);
					}
				}
			}
			
            if ($photos) {
                foreach ($photos as $photo) {
                    $this->db->insert('recipe_photos', array('recipe_id' => $id, 'photo' => $photo));
                }
            }

           
            return true;
        } else {
            return false;
        }
    }

    public function updaterecipeOptionQuantity($option_id, $warehouse_id, $quantity, $recipe_id)
    {
        if ($option = $this->getrecipeWarehouseOptionQty($option_id, $warehouse_id)) {
            if ($this->db->update('warehouses_recipe_variants', array('quantity' => $quantity), array('option_id' => $option_id, 'warehouse_id' => $warehouse_id))) {
                $this->site->syncVariantQty($option_id, $warehouse_id);
                return TRUE;
            }
        } else {
            if ($this->db->insert('warehouses_recipe_variants', array('option_id' => $option_id, 'recipe_id' => $recipe_id, 'warehouse_id' => $warehouse_id, 'quantity' => $quantity))) {
                $this->site->syncVariantQty($option_id, $warehouse_id);
                return TRUE;
            }
        }
        return FALSE;
    }

    public function updatePrice($data = array())
    {
        if ($this->db->update_batch('recipe', $data, 'code')) {
            return true;
        }
        return false;
    }
	
	public function checkDeleterecipe($id){
		$q = $this->db->get_where('order_items', array('recipe_id' => $id), 1);
        if ($q->num_rows() > 0) {
            return TRUE;
        }
        return FALSE;
	}
	
    public function deleterecipe($id)
    {
        if ($this->db->delete('recipe', array('id' => $id)) && $this->db->delete('warehouses_recipe', array('recipe_id' => $id))) {
            $this->db->delete('warehouses_recipe_variants', array('recipe_id' => $id));
            $this->db->delete('recipe_variants', array('recipe_id' => $id));
            $this->db->delete('recipe_photos', array('recipe_id' => $id));
            $this->db->delete('recipe_prices', array('recipe_id' => $id));
	        $this->db->delete('recipe_feedback_mapping', array('recipe_id' => $id));
            return true;
        }
        return FALSE;
    }


    public function totalCategoryrecipe($category_id)
    {
        $q = $this->db->get_where('recipe', array('category_id' => $category_id));
        return $q->num_rows();
    }

    public function getCategoryByCode($code)
    {
		
        $q = $this->db->get_where('recipe_categories', array('code' => $code), 1);
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
        $this->db->select('adjustment_items.*, recipe.code as recipe_code, recipe.name as recipe_name, recipe.image, recipe.details as details, recipe_variants.name as variant')
            ->join('recipe', 'recipe.id=adjustment_items.recipe_id', 'left')
            ->join('recipe_variants', 'recipe_variants.id=adjustment_items.option_id', 'left')
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
            $clause = array('recipe_id' => $data['recipe_id'], 'option_id' => $data['option_id'], 'warehouse_id' => $data['warehouse_id'], 'status' => 'received');
            $qty = $data['type'] == 'subtraction' ? 0 - $data['quantity'] : 0 + $data['quantity'];
            $this->site->setPurchaseItem($clause, $qty);

            $this->site->syncrecipeQty($data['recipe_id'], $data['warehouse_id']);
            if ($data['option_id']) {
                $this->site->syncVariantQty($data['option_id'], $data['warehouse_id'], $data['recipe_id']);
            }
        }
    }

    public function reverseAdjustment($id)
    {
        if ($recipe = $this->getAdjustmentItems($id)) {
            foreach ($recipe as $adjustment) {
                $clause = array('recipe_id' => $adjustment->recipe_id, 'warehouse_id' => $adjustment->warehouse_id, 'option_id' => $adjustment->option_id, 'status' => 'received');
                $qty = $adjustment->type == 'subtraction' ? (0+$adjustment->quantity) : (0-$adjustment->quantity);
                $this->site->setPurchaseItem($clause, $qty);
                $this->site->syncrecipeQty($adjustment->recipe_id, $adjustment->warehouse_id);
                if ($adjustment->option_id) {
                    $this->site->syncVariantQty($adjustment->option_id, $adjustment->warehouse_id, $adjustment->recipe_id);
                }
            }
        }
    }

    public function addAdjustment($data, $recipe)
    {
        if ($this->db->insert('adjustments', $data)) {
            $adjustment_id = $this->db->insert_id();
            foreach ($recipe as $recipe) {
                $recipe['adjustment_id'] = $adjustment_id;
                $this->db->insert('adjustment_items', $recipe);
                $this->syncAdjustment($recipe);
            }
            if ($this->site->getReference('qa') == $data['reference_no']) {
                $this->site->updateReference('qa');
            }
            return true;
        }
        return false;
    }

    public function updateAdjustment($id, $data, $recipe)
    {
        $this->reverseAdjustment($id);
        if ($this->db->update('adjustments', $data, array('id' => $id)) &&
            $this->db->delete('adjustment_items', array('adjustment_id' => $id))) {
            foreach ($recipe as $recipe) {
                $recipe['adjustment_id'] = $id;
                $this->db->insert('adjustment_items', $recipe);
                $this->syncAdjustment($recipe);
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

    public function getrecipeQuantity($recipe_id, $warehouse)
    {
        $q = $this->db->get_where('warehouses_recipe', array('recipe_id' => $recipe_id, 'warehouse_id' => $warehouse), 1);
        if ($q->num_rows() > 0) {
            return $q->row_array();
        }
        return FALSE;
    }

    public function addQuantity($recipe_id, $warehouse_id, $quantity, $rack = NULL)
    {

        if ($this->getrecipeQuantity($recipe_id, $warehouse_id)) {
            if ($this->updateQuantity($recipe_id, $warehouse_id, $quantity, $rack)) {
                return TRUE;
            }
        } else {
            if ($this->insertQuantity($recipe_id, $warehouse_id, $quantity, $rack)) {
                return TRUE;
            }
        }

        return FALSE;
    }

    public function insertQuantity($recipe_id, $warehouse_id, $quantity, $rack = NULL)
    {
        $recipe = $this->site->getrecipeByID($recipe_id);
        if ($this->db->insert('warehouses_recipe', array('recipe_id' => $recipe_id, 'warehouse_id' => $warehouse_id, 'quantity' => $quantity, 'rack' => $rack, 'avg_cost' => $recipe->cost))) {
            $this->site->syncrecipeQty($recipe_id, $warehouse_id);
            return true;
        }
        return false;
    }

    public function updateQuantity($recipe_id, $warehouse_id, $quantity, $rack = NULL)
    {
        $data = $rack ? array('quantity' => $quantity, 'rack' => $rack) : $data = array('quantity' => $quantity);
        if ($this->db->update('warehouses_recipe', $data, array('recipe_id' => $recipe_id, 'warehouse_id' => $warehouse_id))) {
            $this->site->syncrecipeQty($recipe_id, $warehouse_id);
            return true;
        }
        return false;
    }

    public function recipe_count($category_id, $subcategory_id = NULL)
    {
        if ($category_id) {
            $this->db->where('category_id', $category_id);
        }
        if ($subcategory_id) {
            $this->db->where('subcategory_id', $subcategory_id);
        }
        $this->db->from('recipe');
        return $this->db->count_all_results();
    }

    public function fetch_recipe($category_id, $limit, $start, $subcategory_id = NULL)
    {

        $this->db->limit($limit, $start);
        if ($category_id) {
            $this->db->where('category_id', $category_id);
        }
        if ($subcategory_id) {
            $this->db->where('subcategory_id', $subcategory_id);
        }
        $this->db->order_by("id", "asc");
        $query = $this->db->get("recipe");

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getrecipeWarehouseOptionQty($option_id, $warehouse_id)
    {
        $q = $this->db->get_where('warehouses_recipe_variants', array('option_id' => $option_id, 'warehouse_id' => $warehouse_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function syncVariantQty($option_id)
    {
        $wh_pr_vars = $this->getrecipeWarehouseOptions($option_id);
        $qty = 0;
        foreach ($wh_pr_vars as $row) {
            $qty += $row->quantity;
        }
        if ($this->db->update('recipe_variants', array('quantity' => $qty), array('id' => $option_id))) {
            return TRUE;
        }
        return FALSE;
    }

    public function getrecipeWarehouseOptions($option_id)
    {
        $q = $this->db->get_where('warehouses_recipe_variants', array('option_id' => $option_id));
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
        if ($this->db->update('warehouses_recipe', array('rack' => $data['rack']), array('recipe_id' => $data['recipe_id'], 'warehouse_id' => $data['warehouse_id']))) {
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
            ->where($this->db->dbprefix('sale_items') . '.recipe_id', $id)
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
            ->where($this->db->dbprefix('purchase_items') . '.recipe_id', $id)
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

    public function getWarehouserecipeVariant($warehouse_id, $recipe_id, $option_id = NULL)
    {
        $q = $this->db->get_where('warehouses_recipe_variants', array('recipe_id' => $recipe_id, 'option_id' => $option_id, 'warehouse_id' => $warehouse_id), 1);
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

    public function getStockCountrecipe($warehouse_id, $type, $categories = NULL, $brands = NULL)
    {
        $this->db->select("{$this->db->dbprefix('recipe')}.id as id, {$this->db->dbprefix('recipe')}.code as code, {$this->db->dbprefix('recipe')}.name as name, {$this->db->dbprefix('warehouses_recipe')}.quantity as quantity")
        ->join('warehouses_recipe', 'warehouses_recipe.recipe_id=recipe.id', 'left')
        ->where('warehouses_recipe.warehouse_id', $warehouse_id)
        ->where('recipe.type', 'standard')
        ->order_by('recipe.code', 'asc');
        if ($categories) {
            $r = 1;
            $this->db->group_start();
            foreach ($categories as $category) {
                if ($r == 1) {
                    $this->db->where('recipe.category_id', $category);
                } else {
                    $this->db->or_where('recipe.category_id', $category);
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
                    $this->db->where('recipe.brand', $brand);
                } else {
                    $this->db->or_where('recipe.brand', $brand);
                }
                $r++;
            }
            $this->db->group_end();
        }

        $q = $this->db->get('recipe');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getStockCountrecipeVariants($warehouse_id, $recipe_id)
    {
        $this->db->select("{$this->db->dbprefix('recipe_variants')}.name, {$this->db->dbprefix('warehouses_recipe_variants')}.quantity as quantity")
            ->join('warehouses_recipe_variants', 'warehouses_recipe_variants.option_id=recipe_variants.id', 'left');
        $q = $this->db->get_where('recipe_variants', array('recipe_variants.recipe_id' => $recipe_id, 'warehouses_recipe_variants.warehouse_id' => $warehouse_id));
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

    public function finalizeStockCount($id, $data, $recipe)
    {
        if ($this->db->update('stock_counts', $data, array('id' => $id))) {
            foreach ($recipe as $recipe) {
                $this->db->insert('stock_count_items', $recipe);
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

    public function getrecipeVariantID($recipe_id, $name)
    {
        $q = $this->db->get_where("recipe_variants", array('recipe_id' => $recipe_id, 'name' => $name), 1);
        if ($q->num_rows() > 0) {
            $variant = $q->row();
            return $variant->id;
        }
        return NULL;
    }
    
public function deactivate($id = NULL)
    {
        if (($id)) {        

            $data = array(
                'active' => 0
            );

        $return = $this->db->update('recipe', $data, array('id' => $id));

        return $return;

        }

        return FALSE;
    }   

    public function activate($id = NULL)
    {
        if (($id)) {        

            $data = array(
                'active' => 1
            );

        $return = $this->db->update('recipe', $data, array('id' => $id));

        return $return;

        }

        return FALSE;
    }  

    function getKitchen_idByName($name){
    $q = $this->db->get_where('restaurant_kitchens', array('name' => $name), 1);
        if ($q->num_rows() > 0) {
            return $q->row('id');
        }
        return FALSE;
    }
    
    function getCategoryNsubByName($category,$subcategory,$kitchenid){
    $q = $this->db->get_where('recipe_categories', array('name' => $category), 1);
    $return = array();
        if ($q->num_rows() > 0) {
        $return = array();
        $cate = $q->row();
        $category_id = $cate->id;
        
        
        $s = $this->db->get_where('recipe_categories', array('name' => $subcategory,'parent_id'=>$category_id), 1);
        if ($s->num_rows() > 0) {
        $subcate = $s->row('id');
        $subcategoryid = $subcate;
        }else{
        $insertData['code'] = $this->generateCode();
        $insertData['name'] = $subcategory;
        $insertData['parent_id'] = $category_id;
        $insertData['kitchens_id'] = $cate->kitchens_id;
        $this->db->insert('recipe_categories',$insertData);
        $subcategoryid =$this->db->insert_id();
        }
        $return['cat_id'] = $category_id;
        $return['subcat_id'] = $subcategoryid;
            return $return;
        }else{
        $insertData['code'] = $this->generateCode();
        $insertData['name'] = $category;
        $insertData['kitchens_id'] = $kitchenid;
        $this->db->insert('recipe_categories',$insertData);
        $category_id =$this->db->insert_id();
        
        $insertData['code'] = $this->generateCode();
        $insertData['name'] = $subcategory;
        $insertData['parent_id'] = $category_id;
        $insertData['kitchens_id'] = $kitchenid;
        $this->db->insert('recipe_categories',$insertData);
        $subcategoryid =$this->db->insert_id();
        
        
        $return['cat_id'] = $category_id;
        $return['subcat_id'] = $subcategoryid;
    }
        return $return;
    }
    function generateCode($x=8) {
    $chars = "1234567890";
    $no = "";
    echo '<pre>';
    for ($i=0; $i<$x; $i++) {
        //echo 'mt_rand() * strlen($chars)'.(mt_rand() / mt_getrandmax()) * strlen($chars);
      $rnum = floor((mt_rand() / mt_getrandmax()) * strlen($chars));       
      $no .= substr($chars,$rnum,1);
       }
       return $no;
    }    

    function import_recipe($items){
     foreach ($items as $item) {
        $warehouse = explode(',',$item['warehouse']);
        unset($item['warehouse']);
        $this->db->insert('recipe', $item);//print_R($this->db->error());exit;
        $id = $this->db->insert_id();
        
        foreach ($warehouse as $w_id) {
        $this->db->insert('warehouses_recipe', array('recipe_id' => $id, 'warehouse_id' => $w_id));
        }
     }
     return true;
    }      

/*variant start*/
    function add_varient($data){
    $this->db->insert('recipe_variants',$data);
    return $this->db->insert_id();
    }
    function delete_varient($id){
    $this->db->where('id',$id);
    $this->db->delete('recipe_variants');   
    return true;
    }
    function getAllvarients(){
    $q =  $this->db->get('recipe_variants')->result();
    return $q;
    }
    function getVarients($term,$existing){
    $this->db->select();
    $this->db->where(" (name LIKE '%" . $term . "%' OR native_name LIKE '%" . $term . "%') ");
    
    $this->db->where_not_in('id',$existing);
    $q = $this->db->get('recipe_variants');
    return $q->result();
    }
    function AddRecipeVarient($data){
    $this->db->insert_batch('recipe_variants_values',$data); print_r($this->db->error());
    return true;
    }
    function getRecipeVariantData($id){
    $this->db->select('v.*,r.*');
    $this->db->from('recipe_variants_values r');
    $this->db->join('recipe_variants v','v.id=r.attr_id');
    $this->db->where(array('r.recipe_id'=>$id));
    //echo $this->db->get_compiled_select();
    $q = $this->db->get();//print_R($q->result());exit;
    if($q->num_rows()>0){
        return $q->result();
    }
    return false;
    
    }
    function deleteRecipeVariant($id){
    $this->db->where('id',$id);
    $this->db->delete('recipe_variants_values');    
    return true;
    }
    function updateRecipe_variantValues($recipe_id){
    //echo '<pre>';print_R($_POST['varients']);exit;
    if(isset($_POST['varients'])){
        $varients = $_POST['varients'];
        $cnt = count($varients['id']);
        $add_varient = array();
        for($i=0;$i<$cnt;$i++){
            if(isset($varients['val_id'][$i]) && $varients['val_id'][$i]!=''){
            $id = $varients['val_id'][$i];
            $update_varient['price'] = $varients['price'][$i];
            $update_varient['status'] = $varients['status'][$i];
            $update_varient['attr_id'] = $varients['id'][$i];
            $update_varient['status'] = $varients['status_'.$id];
            $update_varient['recipe_id'] = $recipe_id;
            //echo '<pre>';print_r($update_varient);
            $this->db->where('id',$id);
            $this->db->update('recipe_variants_values',$update_varient);
            }else{
            $add_varient['price'] = $varients['price'][$i];
            $add_varient['status'] = $varients['status'][$i];
            $add_varient['attr_id'] = $varients['id'][$i];
            $add_varient['recipe_id'] = $recipe_id;
            //print_R($add_varient);exit;
            $this->db->insert('recipe_variants_values',$add_varient);  
            /*print_r($this->db->error());die;*/
            }
            
        }
        
    }
    return true;
    }
    function getVariantbyID($id){
    $this->db->select('*');
    $this->db->from('recipe_variants');
    $this->db->where(array('id'=>$id));
    //echo $this->db->get_compiled_select();
    $q = $this->db->get();//print_R($q->result());exit;
    if($q->num_rows()>0){
        return $q->row();
    }
    return false;
    }
    function update_varient($id,$data){
    $this->db->where('id',$id);
    $this->db->update('recipe_variants',$data); 
    return true;
    }
    
    function getPurchase_items($term,$existing,$type,$limit=10){

	$this->db->select("p.id, (CASE WHEN p.code = '-' THEN p.name ELSE CONCAT(p.code, ' - ', p.name, ' ') END) as name,p.cost,u.id as unit_id,u.name as unit", FALSE);
        $this->db->from('recipe p');
	$this->db->where(" (p.id LIKE '%" . $term . "%' OR p.name LIKE '%" . $term . "%' OR p.code LIKE '%" . $term . "%') ");
        $this->db->where_not_in('p.id',$existing);
	$this->db->where_in('p.type',array('raw','standard','semi_finished'));
	$this->db->join('units u','u.id=p.unit');
	//echo $this->db->get_compiled_select();exit;
	$this->db->limit($limit);
	$q = $this->db->get(); 
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
        return '';
    }
    function delete_purchase_item($id){
	$this->db->where('id',$id);
	$this->db->delete('recipe_products');
    }
    function recipe_stock($id){
	$this->db->select();
	$this->db->from('pro_stock_master');
	$this->db->where('product_id',$id);
	$q = $this->db->get();
	return $q->result();
    }
/*variant end */
}
