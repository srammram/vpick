<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Masters_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }
	function getSettingscountry($countryCode){
		$this->db->select('*');
		$this->db->where('is_country', $countryCode);
		$q = $this->db->get('settings');
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
	}
	public function updateSetting($data, $countryCode)
    {
		
        //$this->db->where('setting_id', '1');
		$check = $this->db->select('*')->where('is_country', $countryCode)->get('settings');
		
		if($check->num_rows()>0){
			$this->db->where('is_country', $countryCode);
			if ($this->db->update('settings', $data)) {
				
				
				return true;
			}
		}else{
			$data['is_country'] = $countryCode;
			$this->db->insert('settings', $data);
			
			return true;
		}
        return false;
    }
	
	/*### Tax*/
	function countryChecking($country_id, $countryCode){
		$q = $this->db->select('country_id')->where('country_id', $country_id)->get('countrywisesetting');
		if($q->num_rows()>0){
			return true;
		}
		return false;
	}
	function getCountryname($country_id, $countryCode){
		$q = $this->db->select('name')->where('id', $country_id)->get('countries');
		if($q->num_rows()>0){
			return $q->row('name');
		}
		return false;
	}
    function add_countrywisesetting($data, $is_default, $countryCode){
		
		$data['is_country'] = $countryCode;
		$this->db->insert('countrywisesetting', $data);
		
        return $this->db->insert_id();	
    }
    function update_countrywisesetting($id,$data, $is_default, $countryCode){

		$this->db->where('id',$id);
		$this->db->where('is_country', $countryCode);
		if($this->db->update('countrywisesetting',$data)){
	    	return true;
		}
		return false;
    }
    function getCountrywisesettingby_ID($id, $countryCode){
		$this->db->select('*');
		$this->db->from('countrywisesetting');
		$this->db->where('id',$id);
		$q = $this->db->get();
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
    }
	function getALLCountrywisesetting($countryCode){
		$q = $this->db->get('countrywisesetting');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
    function update_countrywisesetting_status($data,$id, $countryCode){
		$this->db->where('id',$id);
		$this->db->where('is_country', $countryCode);
		if($this->db->update('countrywisesetting',$data)){
			return true;
		}
		return false;
    }
	
	/*### User Department*/
	function add_user_department($data, $countryCode){
		//$data['is_country'] = $countryCode;
		$this->db->insert('user_department', $data);
        if($id = $this->db->insert_id()){
	    	return true;
		}
		return false;
    }
	function update_user_department($id,$data, $countryCode){
		$this->db->where('id',$id);
		//$this->db->where('is_country', $countryCode);
		if($this->db->update('user_department',$data)){
	    	return true;
		}
		return false;
    }
    function getUser_departmentby_ID($id, $countryCode){
		$this->db->select('*');
		$this->db->from('user_department');
		$this->db->where('id',$id);
		$q = $this->db->get();
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
    }
	function getALLUser_department(){
		$q = $this->db->get('user_department');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
	
	function getTypeImage(){
		$q = $this->db->get('taxi_image');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
	
	
    function update_user_department_status($data,$id){
		$this->db->where('id',$id);
		//$this->db->where('is_country', $countryCode);
		if($this->db->update('user_department',$data)){
			return true;
		}
		return false;
    }
	
	/*### Taxi_category*/
	function add_taxi_category($data, $countryCode){
		$data['is_country'] = $countryCode;
		$this->db->insert('taxi_category', $data);
        if($id = $this->db->insert_id()){
	    	return true;
		}
		return false;
    }
	function update_taxi_category($id,$data, $countryCode){
		$this->db->where('id',$id);
		$this->db->where('is_country', $countryCode);
		if($this->db->update('taxi_category',$data)){
	    	return true;
		}
		return false;
    }
    function getTaxi_categoryby_ID($id){
		$this->db->select('*');
		$this->db->from('taxi_category');
		$this->db->where('id',$id);

		$q = $this->db->get();
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
    }
	function getALLTaxi_category($countryCode){
			
		//if($countryCode != ''){
		$this->db->where('is_country', $countryCode);
		//}
		$q = $this->db->get('taxi_category');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
    function update_taxi_category_status($data,$id, $countryCode){
		$this->db->where('id',$id);
		$this->db->where('is_country', $countryCode);
		if($this->db->update('taxi_category',$data)){
			return true;
		}
		return false;
    }
	
	
	/*### Shifting*/
	function add_shifting($data, $countryCode){
		$data['is_country'] = $countryCode;
		$this->db->insert('shifting', $data);
        if($id = $this->db->insert_id()){
	    	return true;
		}
		return false;
    }
	function update_shifting($id,$data, $countryCode){
		$this->db->where('id',$id);
		$this->db->where('is_country', $countryCode);
		if($this->db->update('shifting',$data)){
	    	return true;
		}
		return false;
    }
    function getShiftingby_ID($id){
		$this->db->select('*');
		$this->db->from('shifting');
		$this->db->where('id',$id);

		$q = $this->db->get();
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
    }
	function getALLShifting($countryCode){
			
		//if($countryCode != ''){
		$this->db->where('is_country', $countryCode);
		//}
		$q = $this->db->get('shifting');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
    function update_shifting_status($data,$id, $countryCode){
		$this->db->where('id',$id);
		$this->db->where('is_country', $countryCode);
		if($this->db->update('shifting',$data)){
			return true;
		}
		return false;
    }
	
	/*### Taxi Fuel*/
	function add_taxi_fuel($data, $countryCode){
		$data['is_country'] = $countryCode;
		$this->db->insert('taxi_fuel', $data);
        if($id = $this->db->insert_id()){
	    	return true;
		}
		return false;
    }
	function update_taxi_fuel($id,$data, $countryCode){
		$this->db->where('id',$id);
		$this->db->where('is_country', $countryCode);
		if($this->db->update('taxi_fuel',$data)){
	    	return true;
		}
		return false;
    }
    function getTaxi_fuelby_ID($id){
		$this->db->select('*');
		$this->db->from('taxi_fuel');
		$this->db->where('id',$id);

		$q = $this->db->get();
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
    }
	function getALLTaxi_fuel($countryCode){
			
		//if($countryCode != ''){
		$this->db->where('is_country', $countryCode);
		//}
		$q = $this->db->get('taxi_fuel');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
    function update_taxi_fuel_status($data,$id, $countryCode){
		$this->db->where('id',$id);
		$this->db->where('is_country', $countryCode);
		if($this->db->update('taxi_fuel',$data)){
			return true;
		}
		return false;
    }
	/*### Taxi Type*/
	function add_taxi_type($data, $countryCode){
		$data['is_country'] = $countryCode;
		$this->db->insert('taxi_type', $data);
        if($id = $this->db->insert_id()){
	    	return true;
		}
		return false;
    }
	function update_taxi_type($id,$data, $countryCode){
		$this->db->where('id',$id);
		$this->db->where('is_country', $countryCode);
		if($this->db->update('taxi_type',$data)){
	    	return true;
		}
		return false;
    }
    function getTaxi_typeby_ID($id){
		$this->db->select('*');
		$this->db->from('taxi_type');
		$this->db->where('id',$id);

		$q = $this->db->get();
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
    }
	
	function getcategoryALLTaxi_type($category_id, $countryCode){
		$this->db->where('category_id', $category_id);
		//if($countryCode != ''){
		$this->db->where('is_country', $countryCode);
		//}
		$q = $this->db->get('taxi_type');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
	
	function getALLTaxi_type($countryCode){
			
		//if($countryCode != ''){
		$this->db->where('is_country', $countryCode);
		//}
		$q = $this->db->get('taxi_type');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
	
	
	function checkTaxi_type($name, $category_id, $countryCode){
		$q = $this->db->get_where('taxi_type',array('category_id'=>$category_id, 'name' => $name, 'is_country' => $countryCode));
       	if($q->num_rows()>0){
			return TRUE;
		}
		return FALSE;
	}
    function update_taxi_type_status($data,$id, $countryCode){
		$this->db->where('id',$id);
		$this->db->where('is_country', $countryCode);
		if($this->db->update('taxi_type',$data)){
			return true;
		}
		return false;
    }
	
	
	/*### Taxi Make*/
	function add_taxi_make($data, $data_model, $countryCode){
		$data['is_country'] = $countryCode;
		$this->db->insert('taxi_make', $data);
        if($id = $this->db->insert_id()){
			if(!empty($data_model)){
			$data_model['make_id'] = $id;
			$data_model['is_country'] = $countryCode;
			$this->db->insert('taxi_model', $data_model);
			}
	    	return true;
		}
		return false;
    }
	function update_taxi_make($id,$data, $countryCode){
		$this->db->where('id',$id);
		$this->db->where('is_country', $countryCode);
		if($this->db->update('taxi_make',$data)){
	    	return true;
		}
		return false;
    }
    function getTaxi_makeby_ID($id){
		$this->db->select('*');
		$this->db->from('taxi_make');
		$this->db->where('id',$id);
		$q = $this->db->get();
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
    }
	function getALLTaxi_make($countryCode){
		//if($countryCode != ''){
		$this->db->where('is_country', $countryCode);
		//}
		$q = $this->db->get('taxi_make');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
	
    function update_taxi_make_status($data,$id, $countryCode){
		$this->db->where('id',$id);
		$this->db->where('is_country', $countryCode);
		if($this->db->update('taxi_make',$data)){
			return true;
		}
		return false;
    }
	
	/*### Taxi Model*/
	function add_taxi_model($data, $countryCode){
		$data['is_country'] = $countryCode;
		$this->db->insert('taxi_model', $data);
        if($id = $this->db->insert_id()){
	    	return true;
		}
		return false;
    }
	function update_taxi_model($id,$data, $countryCode){
		$this->db->where('id',$id);
		$this->db->where('is_country', $countryCode);
		if($this->db->update('taxi_model',$data)){
	    	return true;
		}
		return false;
    }
    function getTaxi_modelby_ID($id){
		$this->db->select('*');
		$this->db->from('taxi_model');
		$this->db->where('id',$id);
		$q = $this->db->get();
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
    }
	function getALLTaxi_model($countryCode){
		//if($countryCode != ''){
		$this->db->where('is_country', $countryCode);
		//}
		$q = $this->db->get('taxi_model');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
	
    function update_taxi_model_status($data,$id, $countryCode){
		$this->db->where('id',$id);
		$this->db->where('is_country', $countryCode);
		if($this->db->update('taxi_model',$data)){
			return true;
		}
		return false;
    }
	/*### License Type*/
	function add_license_type($data, $countryCode){
		$data['is_country'] = $countryCode;
		$this->db->insert('license_type', $data);
        if($id = $this->db->insert_id()){
	    	return true;
		}
		return false;
    }
	
	function checkLicense($name, $country_id, $type, $license_name, $countryCode){
		
		if($type == 1){
			$q = $this->db->select('*')->where('name', $name)->where('country_id', $country_id)->where('is_country', $countryCode)->get('license_type');
			if($q->num_rows()>0){
				return 1;
			}else{
				return 0;
			}
		}elseif($type == 2){
			if($name != $license_name){
				$q = $this->db->select('*')->where('name', $name)->where('country_id', $country_id)->where('is_country', $countryCode)->get('license_type');
				
				if($q->num_rows()>0){
					return 1;
				}else{
					return 0;
				}
			}else{
				return 0;	
			}
		}
		return 0;
	}
	function update_license_type($id,$data, $countryCode){
		$this->db->where('id',$id);
		$this->db->where('is_country', $countryCode);
		if($this->db->update('license_type',$data)){
	    	return true;
		}
		return false;
    }
    function getLicense_typeby_ID($id){
		$this->db->select('*');
		$this->db->from('license_type');
		$this->db->where('id',$id);
		$q = $this->db->get();
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
    }
	function getALLLicense_type($countryCode){
		//if($countryCode != ''){
		$this->db->where('is_country', $countryCode);
		//}
		$q = $this->db->get('license_type');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
    function update_license_type_status($data,$id, $countryCode){
		$this->db->where('id',$id);
		$this->db->where('is_country', $countryCode);
		if($this->db->update('license_type',$data)){
			return true;
		}
		return false;
    }
	/*### Bank*/
    function add_bank($data, $is_default, $countryCode){
		if($is_default == 1){
			$this->db->where('is_default', $is_default);
			$this->db->where('is_country', $countryCode);
			$d = $this->db->update('admin_bank', array('is_default' =>  0));
		}
		$data['is_country'] = $countryCode;
		$this->db->insert('admin_bank', $data);
        return $this->db->insert_id();	
    }
	
	
    function update_bank($id,$data, $is_default, $countryCode){
		if($is_default == 1){
			$this->db->where('is_default', $is_default);
			$this->db->where('is_country', $countryCode);
			$d = $this->db->update('admin_bank', array('is_default' => 0));
			
		}
		
		$this->db->where('id',$id);
		$this->db->where('is_country', $countryCode);
		if($this->db->update('admin_bank',$data)){
	    	return true;
		}
		return false;
    }
	function getBankByAccountNO($account_no){
		$this->db->select('*');
		$this->db->from('admin_bank');
		$this->db->where('account_no',$account_no);
		$q = $this->db->get();
		if($q->num_rows()>0){
			return true;
		}
		return false;
    }
	
    function getBankby_ID($id){
		$this->db->select('*');
		$this->db->from('admin_bank');
		$this->db->where('id',$id);
		$q = $this->db->get();
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
    }
	function getALLBank($countryCode){
		//if($countryCode != ''){
		$this->db->where('is_country', $countryCode);
		//}
		$q = $this->db->get('admin_bank');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
    function update_bank_status($data,$id, $countryCode){
		$this->db->where('id',$id);
		$this->db->where('is_country', $countryCode);
		if($this->db->update('admin_bank',$data)){
			return true;
		}
		return false;
    }
	
	/*### Tax*/
    function add_tax($data, $is_default, $countryCode){
		
		if($is_default == 1){
			$this->db->where('is_default', $is_default);
			$this->db->where('is_country', $countryCode);
			$d = $this->db->update('tax', array('is_default' =>  0));
		}
		$data['is_country'] = $countryCode;
		$this->db->insert('tax', $data);
		//print_r($this->db->last_query());
        return $this->db->insert_id();	
    }
    function update_tax($id,$data, $is_default, $countryCode){
		
		if($is_default == 1){
			$this->db->where('is_default', $is_default);
			$this->db->where('is_country', $countryCode);
			$d = $this->db->update('tax', array('is_default' => 0));
			
			
		}
		
		$this->db->where('id',$id);
		$this->db->where('is_country', $countryCode);
		if($this->db->update('tax',$data)){
	    	return true;
		}
		return false;
    }
    function getTaxby_ID($id){
		$this->db->select('*');
		$this->db->from('tax');
		$this->db->where('id',$id);
		$q = $this->db->get();
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
    }
	function getALLTax($countryCode){
		//if($countryCode != ''){
		$this->db->where('is_country', $countryCode);
		//}
		$q = $this->db->get('tax');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
    function update_tax_status($data,$id, $countryCode){
		$this->db->where('id',$id);
		$this->db->where('is_country', $countryCode);
		if($this->db->update('tax',$data)){
			return true;
		}
		return false;
    }
	/*### Wallet Offer*/
    function add_walletoffer($data, $type, $is_default, $countryCode){
		
		if($is_default == 1){
			$this->db->where('is_default', $is_default);
			$this->db->where('type', $type);
			$this->db->where('is_country', $countryCode);
			$d = $this->db->update('walletoffer', array('is_default' =>  0));
		}
		
		$this->db->insert('walletoffer', $data);
        return $this->db->insert_id();	
    }
    function update_walletoffer($id,$data, $type, $is_default, $countryCode){
		
		if($is_default == 1){
			$this->db->where('is_default', $is_default);
			$this->db->where('type', $type);
			$this->db->where('is_country', $countryCode);
			$d = $this->db->update('walletoffer', array('is_default' => 0));
			
		}
		
		$this->db->where('id',$id);
		$this->db->where('is_country', $countryCode);
		if($this->db->update('walletoffer',$data)){
	    	return true;
		}
		return false;
    }
    function getWalletofferby_ID($id){
		$this->db->select('*');
		$this->db->from('walletoffer');
		$this->db->where('id',$id);
		$q = $this->db->get();
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
    }
	function getALLWalletoffer($countryCode){
		//if($countryCode != ''){
		$this->db->where('is_country', $countryCode);
		//}
		$q = $this->db->get('walletoffer');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
    function update_walletoffer_status($data,$id, $countryCode){
		$this->db->where('id',$id);
		$this->db->where('is_country', $countryCode);
		if($this->db->update('walletoffer',$data)){
			return true;
		}
		return false;
    }
	
	/*### Currency*/
    function add_currency($data, $is_default, $countryCode){
		
		if($is_default == 1){
			$this->db->where('is_default', $is_default);
			$this->db->where('is_country', $countryCode);
			$d = $this->db->update('currencies', array('is_default' =>  0));
		}
		$data['is_country'] = $countryCode;
		$this->db->insert('currencies', $data);
        return $this->db->insert_id();	
    }
    function update_currency($id,$data, $is_default, $countryCode){
		
		if($is_default == 1){
			$this->db->where('is_default', $is_default);
			$this->db->where('is_country', $countryCode);
			$d = $this->db->update('currencies', array('is_default' => 0));
			
		}
		
		$this->db->where('id',$id);
		$this->db->where('is_country', $countryCode);
		if($this->db->update('currencies',$data)){
	    	return true;
		}
		return false;
    }
    function getCurrencyby_ID($id){
		$this->db->select('*');
		$this->db->from('currencies');
		$this->db->where('id',$id);
		$q = $this->db->get();
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
    }
	function getALLCurrency($countryCode){
		//if($countryCode != ''){
		$this->db->where('is_country', $countryCode);
		//}
		$q = $this->db->get('currencies');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
    function update_currency_status($data,$id, $countryCode){
		$this->db->where('id',$id);
		$this->db->where('is_country', $countryCode);
		if($this->db->update('currencies',$data)){
			return true;
		}
		return false;
    }
	/*### Continent*/
	function add_continent($data, $countryCode){
		
		$this->db->insert('continents', $data);
        if($id = $this->db->insert_id()){
	    	return true;
		}
		return false;
    }
	function update_continent($id,$data, $countryCode){
		$this->db->where('id',$id);
		
		if($this->db->update('continents',$data)){
	    	return true;
		}
		return false;
    }
    function getContinentby_ID($id){
		$this->db->select('*');
		$this->db->from('continents');
		$this->db->where('id',$id);
		$q = $this->db->get();
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
    }
	function getALLContinents($countryCode){
		
		$q = $this->db->get('continents');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
    function update_continent_status($data,$id, $countryCode){
		$this->db->where('id',$id);
		
		if($this->db->update('continents',$data)){
			return true;
		}
		return false;
    }
	
	/*### Country*/
	function add_country($data, $countryCode){
		
		$this->db->insert('countries', $data);
        if($id = $this->db->insert_id()){
	    	return true;
		}
		return false;
    }
	function update_country($id,$data, $countryCode){
		$this->db->where('id',$id);
		
		if($this->db->update('countries',$data)){
	    	return true;
		}
		return false;
    }
    function getCountryby_ID($id){
		$this->db->select('*');
		$this->db->from('countries');
		$this->db->where('id',$id);
		$q = $this->db->get();
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
    }
	function getALLCountry($countryCode){
		
		$q = $this->db->get('countries');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
    function update_country_status($data,$id, $countryCode){
		$this->db->where('id',$id);
		
		if($this->db->update('countries',$data)){
			return true;
		}
		return false;
    }
	
	/*### Zone*/
	function add_zone($data, $countryCode){
		
		$this->db->insert('zones', $data);
        if($id = $this->db->insert_id()){
	    	return true;
		}
		return false;
    }
	function update_zone($id,$data, $countryCode){
		$this->db->where('id',$id);
		
		if($this->db->update('zones',$data)){
	    	return true;
		}
		return false;
    }
    function getZoneby_ID($id){
		$this->db->select('*');
		$this->db->from('zones');
		$this->db->where('id',$id);
		$q = $this->db->get();
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
    }
	function getALLZone($countryCode){
		
		$q = $this->db->get('zones');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
    function update_zone_status($data,$id, $countryCode){
		$this->db->where('id',$id);
		
		if($this->db->update('zones',$data)){
			return true;
		}
		return false;
    }
   
   /*### State*/
	function add_state($data, $countryCode){
		
		$this->db->insert('states', $data);
        if($id = $this->db->insert_id()){
	    	return true;
		}
		return false;
    }
	function update_state($id,$data, $countryCode){
		$this->db->where('id',$id);
		
		if($this->db->update('states',$data)){
	    	return true;
		}
		return false;
    }
    function getStateby_ID($id){
		$this->db->select('*');
		$this->db->from('states');
		$this->db->where('id',$id);
		$q = $this->db->get();
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
    }
	function getALLState($countryCode){
		
		$q = $this->db->get('states');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
    function update_state_status($data,$id, $countryCode){
		$this->db->where('id',$id);
		
		if($this->db->update('states',$data)){
			return true;
		}
		return false;
    }
	
	/*### City*/
	function add_city($data, $countryCode){
		
		$this->db->insert('cities', $data);
        if($id = $this->db->insert_id()){
	    	return true;
		}
		return false;
    }
	function update_city($id,$data, $countryCode){
		$this->db->where('id',$id);
		
		if($this->db->update('cities',$data)){
	    	return true;
		}
		return false;
    }
    function getCityby_ID($id){
		$this->db->select('*');
		$this->db->from('cities');
		$this->db->where('id',$id);
		$q = $this->db->get();
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
    }
	function getALLCity($countryCode){
		
		$q = $this->db->get('cities');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
    function update_city_status($data,$id, $countryCode){
		$this->db->where('id',$id);
		
		if($this->db->update('cities',$data)){
			return true;
		}
		return false;
    }
	
	/*### Area*/
	function add_area($data, $countryCode){
		
		$this->db->insert('areas', $data);
        if($id = $this->db->insert_id()){
	    	return true;
		}
		return false;
    }
	function update_area($id,$data, $countryCode){
		$this->db->where('id',$id);
		
		if($this->db->update('areas',$data)){
	    	return true;
		}
		return false;
    }
    function getAreaby_ID($id){
		$this->db->select('*');
		$this->db->from('areas');
		$this->db->where('id',$id);
		$q = $this->db->get();
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
    }
	function getALLArea($countryCode){
		
		$q = $this->db->get('areas');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
    function update_area_status($data,$id, $countryCode){
		$this->db->where('id',$id);
		
		if($this->db->update('areas',$data)){
			return true;
		}
		return false;
    }
	
	/*### Pincode*/
	function add_pincode($data, $countryCode){
		
		$this->db->insert('pincode', $data);
        if($id = $this->db->insert_id()){
	    	return true;
		}
		return false;
    }
	function update_pincode($id,$data, $countryCode){
		$this->db->where('id',$id);
		
		if($this->db->update('pincode',$data)){
	    	return true;
		}
		return false;
    }
    function getPincodeby_ID($id){
		$this->db->select('*');
		$this->db->from('pincode');
		$this->db->where('id',$id);
		$q = $this->db->get();
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
    }
	function getALLPincode($countryCode){
		
		$q = $this->db->get('pincode');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
    function update_pincode_status($data,$id, $countryCode){
		$this->db->where('id',$id);
		
		if($this->db->update('pincode',$data)){
			return true;
		}
		return false;
    }
	
	/*#### Check Continent country zone state city*/
	function checkHelp_main($name, $parent_id, $countryCode){
		$q = $this->db->get_where('help_main',array('parent_id'=>$parent_id, 'name' => $name, 'is_country' => $countryCode));
       	if($q->num_rows()>0){
			return TRUE;
		}
		return FALSE;
	}
	function checkHelp_sub($name, $parent_id, $countryCode){
		$q = $this->db->get_where('help_sub',array('parent_id'=>$parent_id, 'name' => $name, 'is_country' => $countryCode));
       	if($q->num_rows()>0){
			return TRUE;
		}
		return FALSE;
	}
	function checkHelp_form($name, $parent_id, $countryCode){
		$q = $this->db->get_where('help_form',array('parent_id'=>$parent_id, 'name' => $name, 'is_country' => $countryCode));
       	if($q->num_rows()>0){
			return TRUE;
		}
		return FALSE;
	}
	
	function checkCountry($name, $continent_id, $countryCode){
		$q = $this->db->get_where('countries',array('continent_id'=>$continent_id, 'name' => $name));
       	if($q->num_rows()>0){
			return TRUE;
		}
		return FALSE;
	}
	
	function checkZone($name, $country_id, $countryCode){
		$q = $this->db->get_where('zones',array('country_id'=>$country_id, 'name' => $name));
       	if($q->num_rows()>0){
			return TRUE;
		}
		return FALSE;
	}
	
	function checkState($name, $zone_id, $countryCode){
		$q = $this->db->get_where('states',array('zone_id'=>$zone_id, 'name' => $name));
       	if($q->num_rows()>0){
			return TRUE;
		}
		return FALSE;
	}
	
	function checkCity($name, $state_id, $countryCode){
		$q = $this->db->get_where('cities',array('state_id'=>$state_id, 'name' => $name));
       	if($q->num_rows()>0){
			return TRUE;
		}
		return FALSE;
	}
	
	/*#### Json Country Zone State city area*/
	
	function getALLLicenseCountry($countryCode){
		//$this->db->where('is_country', $countryCode);
		$q = $this->db->get('countries');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
	
	
	function getCountry_bylicensetype($country_id){
		$q = $this->db->get_where('license_type',array('country_id'=>$country_id));
       	if($q->num_rows()>0){
			return $q->result();
		}
		
		
		return false;
    }
	
	function getTaxicategory_byCountry($countryCode){
		$val = array();
		$t = $this->db->get_where('taxi_category',array('is_country' => $countryCode));
       	if($t->num_rows()>0){
			$val['category'] =  $t->result();
		}
		
		if($val){
			return $val;
		}
		return false;
    }
	
	function getTaxitype_byCountry($countryCode){
		$val = array();
		$t = $this->db->get_where('taxi_type',array('is_country' => $countryCode));
       	if($t->num_rows()>0){
			$val['type'] =  $t->result();
		}
		$m = $this->db->get_where('taxi_make',array('is_country' => $countryCode));
       	if($m->num_rows()>0){
			$val['make'] = $m->result();
		}
		if($val){
			return $val;
		}
		return false;
    }
	
	function getHelp_main_byhelp($parent_id, $countryCode){
		$q = $this->db->get_where('help_main',array('parent_id'=>$parent_id));
		
       	if($q->num_rows()>0){
			return $q->result();
		}
		return false;
    }
	
	
	
	function getHelp_sub_byhelp_main($parent_id, $countryCode){
		$q = $this->db->get_where('help_sub',array('parent_id'=>$parent_id));
       	if($q->num_rows()>0){
			return $q->result();
		}
		return false;
    }
	
	
	function getCountry_bycontinent($continent_id, $countryCode){
		$q = $this->db->get_where('countries',array('continent_id'=>$continent_id));
       	if($q->num_rows()>0){
			return $q->result();
		}
		return false;
    }
	
	function getZone_bycountry($country_id, $countryCode){
		$q = $this->db->get_where('zones',array('country_id'=>$country_id));
       	if($q->num_rows()>0){
			return $q->result();
		}
		return false;
    }
	
	function getState_byzone($zone_id, $countryCode){
		$q = $this->db->get_where('states',array('zone_id'=>$zone_id));
       	if($q->num_rows()>0){
			return $q->result();
		}
		return false;
    }
	
	function getCity_bystate($state_id, $countryCode){
		$q = $this->db->get_where('cities',array('state_id'=>$state_id));
       	if($q->num_rows()>0){
			return $q->result();
		}
		return false;
    }
	
	
	function AllgetCity($countryCode){
		
		$q = $this->db->get('cities');
       	if($q->num_rows()>0){
			return $q->result();
		}
		return false;
    }
	
	function getArea_bycity($city_id, $countryCode){
		
		$q = $this->db->get_where('areas',array('city_id'=>$city_id));
       	if($q->num_rows()>0){
			return $q->result();
		}
		return false;
    }
	
	/*#### User Group*/
    function add_user_group($data){
		
		$this->db->insert('groups', $data);
        if($id = $this->db->insert_id()){
	    	return true;
		}
		return false;
    }
    function getUserGroupby_ID($id){
		$this->db->select('*');
		$this->db->from('groups');
		$this->db->where('id',$id);
		$q = $this->db->get();
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
    }
    function update_user_group($id,$data){
		$this->db->where('id',$id);
		
		if($this->db->update('groups',$data)){
			return true;
		}
		return false;
    }
	
	/*### Help*/
	function add_help($data, $countryCode){
		
		$this->db->insert('help', $data);
        if($id = $this->db->insert_id()){
	    	return true;
		}
		return false;
    }
	function update_help($id,$data, $countryCode){
		$this->db->where('id',$id);
		
		if($this->db->update('help',$data)){
	    	return true;
		}
		return false;
    }
    function getHelpby_ID($id){
		$this->db->select('*');
		$this->db->from('help');
		$this->db->where('id',$id);
		$q = $this->db->get();
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
    }
	function getALLHelp($countryCode){
		
		$q = $this->db->get('help');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
    function update_help_status($data,$id, $countryCode){
		$this->db->where('id',$id);
		
		if($this->db->update('help',$data)){
			return true;
		}
		return false;
    }
	
	/*### Help Main*/
	function add_help_main($data, $countryCode){
		$data['is_country'] = $countryCode;
		$this->db->insert('help_main', $data);
        if($id = $this->db->insert_id()){
	    	return true;
		}
		return false;
    }
	function update_help_main($id,$data, $countryCode){
		$this->db->where('id',$id);
		$this->db->where('is_country', $countryCode);
		if($this->db->update('help_main',$data)){
	    	return true;
		}
		return false;
    }
    function getHelp_mainby_ID($id){
		$this->db->select('*');
		$this->db->from('help_main');
		$this->db->where('id',$id);
		$q = $this->db->get();
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
    }
	function getALLHelp_main($countryCode){
		//if($countryCode != ''){
		$this->db->where('is_country', $countryCode);
		//}
		$q = $this->db->get('help_main');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
    function update_help_main_status($data,$id, $countryCode){
		$this->db->where('id',$id);
		$this->db->where('is_country', $countryCode);
		if($this->db->update('help_main',$data)){
			return true;
		}
		return false;
    }
	
	/*### Help Sub*/
	function add_help_sub($data, $countryCode){
		$data['is_country'] = $countryCode;
		$this->db->insert('help_sub', $data);
        if($id = $this->db->insert_id()){
	    	return true;
		}
		return false;
    }
	function update_help_sub($id,$data, $countryCode){
		$this->db->where('id',$id);
		$this->db->where('is_country', $countryCode);
		if($this->db->update('help_sub',$data)){
	    	return true;
		}
		return false;
    }
    function getHelp_subby_ID($id){
		$this->db->select('*');
		$this->db->from('help_sub');
		$this->db->where('id',$id);
		$q = $this->db->get();
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
    }
	function getALLHelp_sub($countryCode){
		//if($countryCode != ''){
		$this->db->where('is_country', $countryCode);
		//}
		$q = $this->db->get('help_sub');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
    function update_help_sub_status($data,$id, $countryCode){
		$this->db->where('id',$id);
		$this->db->where('is_country', $countryCode);
		if($this->db->update('help_sub',$data)){
			return true;
		}
		return false;
    }
	
	/*### Help Form*/
	function add_help_form($data, $details, $parent_id, $countryCode){
		//print_r($data);
		
		$this->db->update('help_sub', array('details' => $details), array('id' => $parent_id, 'is_country' => $countryCode));
		
		foreach($data as $row){
			$row['is_country'] = $countryCode;
			$this->db->insert('help_form', $row);
		}
		
		
		//print_r($this->db->last_query());die;
        if($id = $this->db->insert_id()){
	    	return true;
		}
		return false;
    }
	function update_help_form($id,$data, $countryCode){
		$this->db->where('id',$id);
		$this->db->where('is_country', $countryCode);
		if($this->db->update('help_form',$data)){
	    	return true;
		}
		return false;
    }
    function getHelp_formby_ID($id){
		$this->db->select('*');
		$this->db->from('help_form');
		$this->db->where('id',$id);
		$q = $this->db->get();
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
    }
	function getALLHelp_form($countryCode){
		//if($countryCode != ''){
		$this->db->where('is_country', $countryCode);
		//}
		$q = $this->db->get('help_form');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
    function update_help_form_status($data,$id, $countryCode){
		$this->db->where('id',$id);
		$this->db->where('is_country', $countryCode);
		if($this->db->update('help_form',$data)){
			return true;
		}
		return false;
    }
	
	/*### Payment Gateway*/
	function add_payment_gateway($data, $countryCode){
		$data['is_country'] = $countryCode;
		$this->db->insert('payment_gateway', $data);
        if($id = $this->db->insert_id()){
	    	return true;
		}
		return false;
    }
	function update_payment_gateway($id,$data, $countryCode){
		$this->db->where('id',$id);
		$this->db->where('is_country', $countryCode);
		if($this->db->update('payment_gateway',$data)){
	    	return true;
		}
		return false;
    }
    function getPayment_gatewayby_ID($id){
		$this->db->select('*');
		$this->db->from('payment_gateway');
		$this->db->where('id',$id);
		$q = $this->db->get();
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
    }
	function getALLPayment_gateway($countryCode){
		//if($countryCode != ''){
		$this->db->where('is_country', $countryCode);
		//}
		$q = $this->db->get('payment_gateway');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
	
    function update_payment_gateway_status($data,$id, $countryCode){
		$this->db->where('id',$id);
		$this->db->where('is_country', $countryCode);
		if($this->db->update('payment_gateway',$data)){
			return true;
		}
		return false;
    }
	
	function import_bank($items){
		if($this->db->insert_batch('admin_bank', $items)){
			
			return TRUE;	
		}
		return false;
	}
	
	function getCabtypeByName($cab_type_name, $is_country){
		$this->db->select('id');
		$this->db->where('name', $cab_type_name);
		$this->db->where('is_country', $is_country);
		$q = $this->db->get('taxi_type');
		if($q->num_rows()>0){
			return $q->row('id');
		}
		return 0;	
	}
	
	function getCabMakeByName($cab_make, $is_country){
		$this->db->select('id');
		$this->db->where('name', $cab_make);
		$this->db->where('is_country', $is_country);
		$q = $this->db->get('taxi_make');
		if($q->num_rows()>0){
			return $q->row('id');
		}
		return 0;	
	}
	
	function getCabModelByName($cab_type_id, $cab_make_id, $cab_model, $is_country){
		if($cab_type_id != 0 && $cab_make_id != 0){
			$this->db->select('id');
			$this->db->where('name', $cab_model);
			$this->db->where('type_id', $cab_type_id);
			$this->db->where('make_id', $cab_make_id);
			$this->db->where('is_country', $is_country);
			$q = $this->db->get('taxi_model');
			if($q->num_rows()>0){
				return true;
			}
		}
		return false;	
	}
	
	function getCabtypeImageByName($cab_image){
		$this->db->select('id');
		$this->db->where('name', $cab_image);
		$q = $this->db->get('taxi_image');
		//print_r($this->db->last_query());
		//die;
		if($q->num_rows()>0){
			return $q->row('id');
		}
		return 0;	
	}
	
	function import_cab($items){
		if(!empty($items)){
			foreach($items as $val){
				
			if($val['cab_type_id'] == 0){
				
				$check_cab_type = $this->getCabtypeByName($val['cab_type'], $val['is_country']);
				if($check_cab_type == 0){
				$this->db->insert('taxi_type', array(
					'category_id' => 1, 
					'name' => $val['cab_type'],
					'taxi_image_id' => $val['cab_image_id'],
					'status' => 1,
					'is_country' => $val['is_country']
				));
				$val['cab_type_id'] = $this->db->insert_id();
				
				
				if($val['base_price_type'] == 1){
					$base_per_distance_price = $val['base_min_distance_price'] * ($val['base_price_value'] / 100);
				}else{
					$base_per_distance_price = $val['base_price_value'];
				}
				
				$daily = array(
					'taxi_type' => $val['cab_type_id'],
					'base_min_distance' => $val['base_min_distance'],
					'base_min_distance_price' => $val['base_min_distance_price'],
					'base_price_type' => $val['base_price_type'],
					'base_price_value' => $val['base_price_value'],
					'base_per_distance' => 1,
					'base_per_distance_price' => $base_per_distance_price,
					'base_waiting_minute' => "00:".$val["base_waiting_minute"],
					'base_waiting_price' => $val['base_waiting_price'],
					
					'is_base' => 1,
					'status' => 1,
					'is_default' => 1,
					'is_country' => $val['is_country'],
					'created_on' => date('Y-m-d H:i:s')
				);
				
				$this->db->insert('daily_fare', $daily);
				
				$rental = array(
					'taxi_type' => $val['cab_type_id'],
					'package_name' => $val['package_name'],
					'package_price' => $val['package_price'],
					'package_distance' => $val['package_distance'],
					'package_time' => $val['package_time'],
					'option_type' => $val['option_type'],
					'option_price' => $val['option_price'],
					'time_type' => $val['time_type'],
					'per_distance' => $val['per_distance'],
					'per_distance_price' => $val['per_distance_price'],
					'per_time' => $val['per_time'],
					'per_time_price' => $val['per_time_price'],
					'day_allowance' => $val['day_allowance'],
					'overnight_allowance' => $val['overnight_allowance'],
					'status' => 1,
					'is_default' => 1,
					'is_country' => $val['is_country'],
					'created_on' => date('Y-m-d H:i:s')
				);
				$this->db->insert('rental_fare', $rental);
				
				$outstation = array(
				
					'taxi_type' => $val['cab_type_id'],
					'package_name' => $val['outstation_package_name'] ? $val['outstation_package_name'] : '',
					'oneway_package_price' => $val['oneway_package_price'] ? $val['oneway_package_price'] : '',
					'twoway_package_price' => $val['twoway_package_price'] ? $val['twoway_package_price'] : '',
					'is_oneway' => $val['is_oneway'] ? $val['is_oneway'] : 0,
					'is_twoway' => $val['is_twoway'] ? $val['is_twoway'] : 0,
					
					'per_distance' => $val['outstation_per_distance'] ? $val['outstation_per_distance'] : '',
					'per_distance_price' => $val['outstation_per_distance_price'] ? $val['outstation_per_distance_price'] : '',
					'driver_allowance_per_day' => $val['driver_allowance_per_day'] ? $val['driver_allowance_per_day'] : '',
					'driver_night_per_day' => $val['driver_night_per_day'] ? $val['driver_night_per_day'] : '',
					'status' => 1,
					'oneway_distance' => $val['oneway_distance'] ? $val['oneway_distance'] : '',
					'twoway_distance' => $val['twoway_distance'] ? $val['twoway_distance'] : '',
					'is_default' => 1,
					'is_country' => $val['is_country'],
					'created_on' => date('Y-m-d H:i:s')
				);
				
				$this->db->insert('outstation_fare', $outstation);
				}else{
					$val['cab_type_id'] = $check_cab_type;	
				}
			}
			
			if($val['cab_make_id'] == 0){
				$check_cab_make = $this->getCabMakeByName($val['cab_make'], $val['is_country']);
				if($check_cab_make == 0)
				{
					$this->db->insert('taxi_make', array(
						'name' => $val['cab_make'],
						'status' => 1,
						'is_country' => $val['is_country']
					));
					$val['cab_make_id'] = $this->db->insert_id();
				}else{
					$val['cab_make_id'] = $check_cab_make;	
				}
			}
			
			if($val['cab_model'] != ''){
				
				$this->db->insert('taxi_model', array(
					'name' => $val['cab_model'],
					'type_id' => $val['cab_type_id'],
					'make_id' => $val['cab_make_id'],
					'status' => 1,
					'is_country' => $val['is_country']
				));
			}
			
			
			
		}
			return true;
		}
		return false;
	}
	
	function import_location($items){
		if(!empty($items)){
			foreach($items as $val){
				
			if($val['continent_id'] == 0){
				$continent_id = $this->site->masterCheck1('continents', array('name' => trim($csv_pr['continents_name'])));
				if($continent_id == 0){
				$this->db->insert('continents', array(					
					'name' => $val['continents_name'],
					'code' => $val['code'],
					
				));
				//print_r($this->db->last_query());
				$val['continent_id'] = $this->db->insert_id();
				}else{
					$val['continent_id'] = $continent_id;
				}
			}
			
			if($val['country_id'] == 0){
				$country_id = $this->site->masterCheck1('countries', array('name' => trim($csv_pr['country_name'])));
				if($country_id == 0){
				$this->db->insert('countries', array(
					'iso' => $val['country_iso'],
					'phonecode' => $val['country_phonecode'],
					'name' => $val['country_name'],
					'continent_id' => $val['continent_id']
					
				));
				$val['country_id'] = $this->db->insert_id();
				}else{
					$val['country_id'] = $country_id;
				}
			}
			
			if($val['zone_id'] == 0){
				$zone_id = $this->site->masterCheck1('zones', array('name' => trim($csv_pr['zone_name'])));
				if($zone_id == 0){
				$this->db->insert('zones', array(					
					'name' => $val['zone_name'],
					'country_id' => $val['country_id'],
					
				));
				$val['zone_id'] = $this->db->insert_id();
				}else{
					$val['zone_id'] = $zone_id;
				}
				
			}
			
			if($val['state_id'] == 0){
				$state_id = $this->site->masterCheck1('states', array('name' => trim($csv_pr['state_name'])));
				if($state_id == 0){
				$this->db->insert('states', array(					
					'name' => $val['state_name'],
					'zone_id' => $val['zone_id'],
					
				));
				$val['state_id'] = $this->db->insert_id();
				}else{
					$val['state_id'] = $state_id;
				}
				
			}
			
			if($val['city_id'] == 0){
				$city_id = $this->site->masterCheck1('cities', array('name' => trim($csv_pr['city_name'])));
				if($city_id == 0){
				$this->db->insert('cities', array(					
					'name' => $val['city_name'],
					'state_id' => $val['state_id'],
					
				));
				$val['city_id'] = $this->db->insert_id();
				}else{
					$val['city_id'] = $city_id;
				}
			}
			
			if($val['area_id'] == 0){
				$area_id = $this->site->masterCheck1('areas', array('name' => trim($csv_pr['area_name'])));
				if($area_id == 0){
				$this->db->insert('areas', array(					
					'name' => $val['area_name'],
					'city_id' => $val['city_id'],
					
				));
				$val['area_id'] = $this->db->insert_id();
				}else{
					$val['area_id'] = $area_id;
				}
				
			}
			
			if($val['pincode'] != 0){
				$this->db->insert('pincode', array(					
					'name' => $val['post_office'],
					'pincode' => $val['pincode'],
					'area_id' => $val['area_id'],
					
				));
				
				
			}
			
		//die;	
			
			
		}
			return true;
		}

		return false;
	}
	
    
	
	function import_walletoffer($items){
		if($this->db->insert_batch('walletoffer', $items)){
			
			return TRUE;	
		}
		return false;
	}
	
	function import_payment_gateway($items){
		if($this->db->insert_batch('payment_gateway', $items)){
			
			return TRUE;	
		}
		return false;
	}
	
	function import_tax($items){		
		if($this->db->insert_batch('tax', $items)){
			
			return TRUE;	
		}
		return false;
	}
	
	function import_taxi_type($items){		
		if($this->db->insert_batch('taxi_type', $items)){
			
			return TRUE;	
		}
		return false;
	}
	
	function import_taxi_make($items){		
		if($this->db->insert_batch('taxi_make', $items)){
			
			return TRUE;	
		}
		return false;
	}
	
	function import_taxi_model($items){
		if(!empty($items)){
			foreach($items as $val){
				
				if($val['cab_type_id'] == 0){
					$this->db->insert('taxi_type', array(
						'category_id' => 1, 
						'name' => $val['cab_type'],
						'taxi_image_id' => $val['cab_image_id'],
						'status' => 1,
						'is_country' => $val['is_country']
					));
					$val['cab_type_id'] = $this->db->insert_id();
					
					
				}
				
				if($val['cab_make_id'] == 0){
					$this->db->insert('taxi_make', array(
						'name' => $val['cab_make'],
						'status' => 1,
						'is_country' => $val['is_country']
					));
					$val['cab_make_id'] = $this->db->insert_id();
				}
				
				if($val['cab_model'] != ''){
					$this->db->insert('taxi_model', array(
						'name' => $val['cab_model'],
						'type_id' => $val['cab_type_id'],
						'make_id' => $val['cab_make_id'],
						'status' => 1,
						'is_country' => $val['is_country']
					));
				}
				
				
				
			}
			return true;
		}

		return false;
	}
	
	function import_taxi_category($items){		
		if($this->db->insert_batch('taxi_category', $items)){
			
			return TRUE;	
		}
		return false;
	}
	
	function import_shifting($items){		
		if($this->db->insert_batch('shifting', $items)){
			
			return TRUE;	
		}
		return false;
	}
	
	function import_taxi_fuel($items){		
		if($this->db->insert_batch('taxi_fuel', $items)){
			
			return TRUE;	
		}
		return false;
	}
	
	function import_continents($items){		
		if($this->db->insert_batch('continents', $items)){
			
			return TRUE;	
		}
		return false;
	}
	
	function import_country($items){		
		if($this->db->insert_batch('countries', $items)){
			
			return TRUE;	
		}
		return false;
	}
	
	function import_zone($items){		
		if($this->db->insert_batch('zones', $items)){
			
			return TRUE;	
		}
		return false;
	}
	
	function import_state($items){		
		if($this->db->insert_batch('states', $items)){
			
			return TRUE;	
		}
		return false;
	}
	
	function import_city($items){		
		if($this->db->insert_batch('cities', $items)){
			
			return TRUE;	
		}
		return false;
	}
	
	function import_area($items){		
		if($this->db->insert_batch('areas', $items)){
			
			return TRUE;	
		}
		return false;
	}
	
	function import_pincode($items){		
		if($this->db->insert_batch('pincode', $items)){
			
			return TRUE;	
		}
		return false;
	}
	
	
	
}
