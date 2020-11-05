<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Tables_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

	
	/*Tables*/
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

    public function addKitchen($data, $is_default)
    {
		if($is_default == 1){
			//$this->db->where('warehouse_id', $warehouses_id);
			$this->db->where('is_default', $is_default);
			$q = $this->db->update('restaurant_kitchens', array('is_default' => 0));
		}
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

    public function updateKitchen($id, $data = array(),  $is_default)
    {
		if($is_default == 1){
			//$this->db->where('warehouse_id', $warehouses_id);
			$this->db->where('is_default', $is_default);
			$q = $this->db->update('restaurant_kitchens', array('is_default' => 0));
		}
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
