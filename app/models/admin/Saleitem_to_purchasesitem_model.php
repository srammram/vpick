<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Saleitem_to_purchasesitem_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

	
	/*Tables*/
	public function getAllrecipe(){
		
		$sql = "SELECT 'sno',w.name branch,recipe.id, recipe.name, recipe.code, GROUP_CONCAT( DISTINCT CONCAT(products.name,' - ',recipe_products.max_quantity,'(',units.code,')') ORDER BY recipe.id SEPARATOR '<br> ') AS product_details
		FROM ".$this->db->dbprefix('recipe')." AS recipe
		LEFT JOIN ".$this->db->dbprefix('recipe_products')."  AS recipe_products ON recipe_products.recipe_id = recipe.id
		LEFT JOIN ".$this->db->dbprefix('products')."  AS products ON products.id IN (recipe_products.product_id)
		LEFT JOIN ".$this->db->dbprefix('units')."  AS units ON units.id IN (recipe_products.units_id)
		LEFT JOIN ".$this->db->dbprefix('warehouses_recipe')."  AS wp ON wp.recipe_id = recipe.id
		     JOIN ".$this->db->dbprefix('warehouses')."  AS w ON w.id = wp.warehouse_id
		WHERE recipe.type = 'standard' GROUP BY recipe.id ";
		$q = $this->db->query($sql);
		if ($q->num_rows() > 0) {
            foreach($q->result() as $row){
				$data[] = $row;
			}
			return $data;
        }
        return FALSE;
	}
    public function getTableByName($name)
    {
        $q = $this->db->get_where('restaurant_tables', array('name' => $name), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function addTable($data)
    {
        if ($this->db->insert("restaurant_tables", $data)) {
            return true;
        }
        return false;
    }

    public function addTables($data)
    {
        if ($this->db->insert_batch('restaurant_tables', $data)) {
            return true;
        }
        return false;
    }

    public function updateTable($id, $data = array())
    {
        if ($this->db->update("restaurant_tables", $data, array('id' => $id))) {
            return true;
        }
        return false;
    }

   

    public function deleteTable($id)
    {
        if ($this->db->delete("restaurant_tables", array('id' => $id))) {
            return true;
        }
        return FALSE;
    }
	
	/*Areas*/
   
    public function getAreaByName($name)
    {
        $q = $this->db->get_where('restaurant_areas', array('name' => $name), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function addArea($data)
    {
        if ($this->db->insert("restaurant_areas", $data)) {
            return true;
        }
        return false;
    }

    public function addAreas($data)
    {
        if ($this->db->insert_batch('restaurant_areas', $data)) {
            return true;
        }
        return false;
    }

    public function updateArea($id, $data = array())
    {
        if ($this->db->update("restaurant_areas", $data, array('id' => $id))) {
            return true;
        }
        return false;
    }

   

    public function deleteArea($id)
    {
        if ($this->db->delete("restaurant_areas", array('id' => $id))) {
            return true;
        }
        return FALSE;
    }
	
	/*Kitchens*/
    public function getKitchenByName($name)
    {
        $q = $this->db->get_where('restaurant_kitchens', array('name' => $name), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function addKitchen($data)
    {
        if ($this->db->insert("restaurant_kitchens", $data)) {
            return true;
        }
        return false;
    }

    public function addKitchens($data)
    {
        if ($this->db->insert_batch('restaurant_kitchens', $data)) {
            return true;
        }
        return false;
    }

    public function updateKitchen($id, $data = array())
    {
        if ($this->db->update("restaurant_kitchens", $data, array('id' => $id))) {
            return true;
        }
        return false;
    }

   

    public function deleteKitchen($id)
    {
        if ($this->db->delete("restaurant_kitchens", array('id' => $id))) {
            return true;
        }
        return FALSE;
    }

}
