<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Supplier_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }
    public function addSupplier($data = array())
    {
        if ($this->db->insert('companies', $data)) {
            $cid = $this->db->insert_id(); //file_put_contents('supplier.txt',json_encode($this->db->error()),FILE_APPEND);   
            return $cid;
        }
        //print_R($this->db->error());//exit;
        return false;
    }
}