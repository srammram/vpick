<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Nightaudit_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

	
	public function getDataviewSales($dates = NULL, $warehouses_id = NULL){
		
		
		if(!empty($dates)){
			$current_date = $dates;
		}else{
			$current_date = date('Y-m-d');
		}
		
		if(!empty($warehouses_id)){
			$warehouses = $warehouses_id;
		}else{
			$warehouses = 1;
		}
		
		$this->db->select("grand_total, sale_status");
		$this->db->where('DATE(date)', $current_date);
		$this->db->where('warehouse_id', $warehouses);
		$q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }	
	}
	
	public function checkbeforedate($dates = NULL, $warehouses_id = NULL){
		if(!empty($dates)){
			$current_date = $dates;
		}else{
			$current_date = date('Y-m-d');
		}
		
		if(!empty($warehouses_id)){
			$warehouses = $warehouses_id;
		}else{
			$warehouses = 1;
		}
		$this->db->where('DATE(nightaudit_date)', $current_date);
		$this->db->where('warehouse_id', $warehouses);
		$q = $this->db->get('nightaudit');
        if ($q->num_rows() > 0) {
			return $data = 'yes';
		}
		return $data = 'no';
	}

	public function checkNightaudit($dates = NULL, $warehouses_id = NULL){
		if(!empty($dates)){
			$current_date = $dates;
		}else{
			$current_date = date('Y-m-d');
		}
		
		if(!empty($warehouses_id)){
			$warehouses = $warehouses_id;
		}else{
			$warehouses = 1;
		}
		$this->db->where('DATE(nightaudit_date)', $current_date);
		$this->db->where('warehouse_id', $warehouses);
		$q = $this->db->get('nightaudit');
        if ($q->num_rows() > 0) {
			return $data = 'yes';
		}
		return $data = 'no';
	}
	
	function addNightaudit($data = array()){
		if ($this->db->insert('nightaudit', $data)){
			return true;	
		}
        return false;
	}
    function Check_Not_Closed_Nightaudit(){

    	$Max_Date  = "SELECT DISTINCT  max(nightaudit_date)  AS lastdate 
		FROM " . $this->db->dbprefix('nightaudit') . " ";

		$MaxDate = $this->db->query($Max_Date);	

		 if ($MaxDate->num_rows() > 0) {		 	 
            foreach (($MaxDate->result()) as $row) {
                $lastdate = $row->lastdate;   
            }            
        }

        
        if(isset($lastdate))
        {
			$Miss_dates = "SELECT * FROM
				(
				SELECT DATE_ADD('".$lastdate."', INTERVAL t4+t16+t64+t256+t1024 DAY) missingDates 
				FROM 
				(SELECT 0 t4    UNION ALL SELECT 1   UNION ALL SELECT 2   UNION ALL SELECT 3  ) t4,
				(SELECT 0 t16   UNION ALL SELECT 4   UNION ALL SELECT 8   UNION ALL SELECT 12 ) t16,   
				(SELECT 0 t64   UNION ALL SELECT 16  UNION ALL SELECT 32  UNION ALL SELECT 48 ) t64,      
				(SELECT 0 t256  UNION ALL SELECT 64  UNION ALL SELECT 128 UNION ALL SELECT 192) t256,     
				(SELECT 0 t1024 UNION ALL SELECT 256 UNION ALL SELECT 512 UNION ALL SELECT 768) t1024     
				) b 
				WHERE
				missingDates NOT IN (SELECT DATE_FORMAT(nightaudit_date,'%Y-%m-%d')
				FROM " . $this->db->dbprefix('nightaudit') . "  GROUP BY nightaudit_date)
				AND
				missingDates <= DATE(NOW())";
				
			    $missdate = $this->db->query($Miss_dates);
			    if ($missdate->num_rows() > 0) {
			        foreach (($missdate->result()) as $row) {			        	
			            $misdate[] = $row->missingDates;
			        }
			        return $misdate;
			    }
			    return FALSE;
        }
        else{
	        	$date_format = 'Y-m-d';
				$yesterday = strtotime('-1 day');
				$previous_date = date($date_format, $yesterday);
			
				$lastdate = $previous_date;
				$Miss_dates = "SELECT * FROM
				(
				SELECT DATE_ADD('".$lastdate."', INTERVAL t4+t16+t64+t256+t1024 DAY) missingDates 
				FROM 
				(SELECT 0 t4    UNION ALL SELECT 1   UNION ALL SELECT 2   UNION ALL SELECT 3  ) t4,
				(SELECT 0 t16   UNION ALL SELECT 4   UNION ALL SELECT 8   UNION ALL SELECT 12 ) t16,   
				(SELECT 0 t64   UNION ALL SELECT 16  UNION ALL SELECT 32  UNION ALL SELECT 48 ) t64,      
				(SELECT 0 t256  UNION ALL SELECT 64  UNION ALL SELECT 128 UNION ALL SELECT 192) t256,     
				(SELECT 0 t1024 UNION ALL SELECT 256 UNION ALL SELECT 512 UNION ALL SELECT 768) t1024     
				) b 
				WHERE
				missingDates NOT IN (SELECT DATE_FORMAT(nightaudit_date,'%Y-%m-%d')
				FROM
				" . $this->db->dbprefix('nightaudit') . " GROUP BY nightaudit_date)
				AND
				missingDates <= DATE(NOW())";
				
			    $missdate = $this->db->query($Miss_dates);
			    if ($missdate->num_rows() > 0) {
			        foreach (($missdate->result()) as $row) {			        	
			            $misdate[] = $row->missingDates;
			        }
			        return $misdate;
			    }
			    return FALSE;
        }

    }
    function Last_Nightaudit(){

    	$Max_Date  = "SELECT DISTINCT  max(nightaudit_date)  AS lastdate 
		FROM " . $this->db->dbprefix('nightaudit') . " ";

		$MaxDate = $this->db->query($Max_Date);	

		 if ($MaxDate->num_rows() > 0) {		 	 
            foreach (($MaxDate->result()) as $row) {
                $lastdate = $row->lastdate;   
            }   
            return $lastdate;         
        }
        return FALSE;        
    }    
    public function getUserGroupid($user_id)
    {

        $this->db->select('group_id')
            ->where('id', $user_id);
        $q = $this->db->get('users');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getGroupPermissions($id)
    {	
        $q = $this->db->get_where('permissions', array('group_id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
}
