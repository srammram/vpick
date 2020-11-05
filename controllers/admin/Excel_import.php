<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Excel_import extends MY_Controller
{

    function __construct() {
        parent::__construct();
        
		$this->load->library('form_validation');
		
		include (APPPATH.'third_party'.'/'.'PhpExcel'.'/'.'PHPExcel'.'/'.'IOFactory.php');

    }

	
	public function index()
    {
		if($_POST['submit']){
			$CI =& get_instance();
			$CI->load->database();
			// prepend file path with project directory
		    $tmp = explode(".", $_FILES['import']['name']); // For getting Extension of selected file
            $extension = end($tmp);
            $allowed_extension = array("xls", "xlsx", "csv"); //allowed extension
            $file = $_FILES["import"]["tmp_name"]; // getting temporary 
	        $excel = PHPExcel_IOFactory::load($file);
			//print_r($excel);
			foreach ($excel->getWorksheetIterator() as $worksheet)
			{
				$highestRow = $worksheet->getHighestRow();
				for($row=1; $row<=$highestRow; $row++)
				{
					$statename       = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
					$cityname       = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
					$areaname       = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
					$pincode       = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
					$postofficename       = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
					
					if(isset($statename)){
						
					  $ifstate	=$this->db->select('*')->get_where('states',array('name'=>$statename))->row();
					  if(!empty($ifstate)){
							$state_id=	$ifstate->id;
						  
					  }else{
						  $this->db->insert('states',array('name'=>$statename,'zone_id' => 1));
						  $state_id=$this->db->insert_id();
					  }
						
					}
					
					
					if(isset($cityname)){
					  $ifcity	=$this->db->select('*')->get_where('cities',array('name'=>$cityname))->row();
					  if(!empty($ifcity)){
							$city_id=	 $ifcity->id;
						  
					  }else{
						  $this->db->insert('cities',array('name'=>$cityname, 'state_id' => $state_id));
						  $city_id=$this->db->insert_id();
					  }
						
					}
					
					if(isset($areaname)){
					  $ifarea	=$this->db->get_where('areas',array('name'=>$areaname))->row();
					  if(!empty($ifarea)){
							$area_id=	 $ifarea->id;
						  
					  }else{
						  $this->db->insert('areas',array('name'=>$areaname, 'city_id' => $city_id));
						  $area_id=$this->db->insert_id();
					  }
						
					}
					
					
					if(isset($pincode)){
					  $ifpincode	=$this->db->select('*')->get_where('pincode',array('name'=>$postofficename, 'pincode' => $pincode))->row();
					  if(!empty($ifpincode)){
							$pincode_id=	 $ifpincode->id;
						  
					  }else{
						  $this->db->insert('pincode',array('name'=>$postofficename, 'pincode' => $pincode, 'area_id' => $area_id));
						  $pincode_id=$this->db->insert_id();
					  }
						
					}
				   
				} 
			}
       
		}
		
		$this->page_construct('excel_import/index', $meta, $this->data);
		
    
	}

}
