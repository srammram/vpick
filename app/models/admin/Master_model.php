<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Master_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }
    function getApproxFare_cityrides($lat,$lng,$taxi_type,$km,$mi){
	$this->db->select('c.*,f.*,( 3959 * acos( cos( radians('.$lat.') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$lng.') ) + sin( radians('.$lat.') ) * sin( radians( latitude ) ) ) ) AS distance ');//print_R($this->db->error());
	$this->db->from('cities c');
	$this->db->join('location_fare f','f.location_id=c.id');
	$this->db->where('taxi_type',$taxi_type);
	$this->db->order_by('distance','ASC');
	$this->db->limit(1);
	//$this->db->having('distance < 25',false);
        $q =  $this->db->get();
	if($q->num_rows()>0){
	    $data = $q->row();//print_R($data);
	    $d_unit = $data->distance_unit;
	    $d_fare = $data->base_ride_charge;
	    $d_base_dis = $data->base_ride_distance;
	    $d_base_charge = $data->base_ride_charge;
	    $price_per_unit = $data->price_per_unit; //extra
	    $currency = $data->currency;
	    if($d_unit=='km'){
		$km = round($km,2);
		$base_fare = $d_base_charge;
		$extra_fare = ($km-$d_base_dis)*$price_per_unit;
		$fare = $base_fare+$extra_fare;
	    }else if($d_unit=="mi"){
		$mi = round($mi,2);
		$base_fare =  $d_base_charge;
		$extra_fare = ($mi-$d_base_dis)*$price_per_unit;
		$fare = $base_fare+$extra_fare;
	    }
	    //echo $fare;
	    /************** peaktime ********************/
	    $p_from = strtotime($data->peaktime_from);
	    $p_to = strtotime($data->peaktime_to);
	    $p_status = $data->peaktime_status;
	    $p_surcharge = $data->peaktime_surcharge;
	    /************** nighttime ********************/
	    $n_from = strtotime($data->nighttime_from);
	    $n_to = strtotime($data->nighttime_to);
	    $n_status = $data->nighttime_status;
	    $n_surcharge = $data->nighttime_surcharge;
	    /************** demandtime ********************/
	    $d_from = strtotime($data->demandtime_from);
	    $d_to = strtotime($data->demandtime_to);
	    $d_status = $data->demandtime_status;
	    $d_surcharge = $data->demandtime_surcharge;
	    
	    $currentTime = strtotime(date('h:i a'));//strtotime( $current_time ) - strtotime( $passed_time );
	    
	    if($p_status && $currentTime>=$p_from && $currentTime<=$p_to){
		$p_charge = $fare * ($p_surcharge/100);
		$fare = $fare+$p_charge;
	    }else if($n_status && $currentTime>=$n_from && $currentTime<=$n_to){
		$n_charge = $fare * ($n_surcharge/100);
		$fare = $fare+$n_charge;
	    }else if($d_status && $currentTime>=$d_from && $currentTime<=$d_to){
		$d_charge = $fare * ($d_surcharge/100);
		$fare = $fare+$d_charge;
	    }
	    
	    $final_fare = round($fare,2);
	    
	    
	    return array('alert_status'=>'success','fare'=>$currency.$final_fare);
	}
	return false;
    }
    function getApproxFare_rental($lat,$lng,$taxi_type,$km,$mi){
	$this->db->select('c.*,f.*,( 3959 * acos( cos( radians('.$lat.') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$lng.') ) + sin( radians('.$lat.') ) * sin( radians( latitude ) ) ) ) AS distance ');//print_R($this->db->error());
	$this->db->from('cities c');
	$this->db->join('location_rental_fare f','f.location_id=c.id');
	$this->db->where('taxi_type',$taxi_type);
	$this->db->order_by('distance','ASC');
	$this->db->limit(1);
	//$this->db->having('distance < 25',false);
        $q =  $this->db->get();
	if($q->num_rows()>0){
	    
	    
	    return true;
	}
	return false;
    }
     function getApproxFare_outstation($lat,$lng,$taxi_type,$km,$mi,$trip_type,$dest_addr){
	$this->db->select('c.*,f.*,( 3959 * acos( cos( radians('.$lat.') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$lng.') ) + sin( radians('.$lat.') ) * sin( radians( latitude ) ) ) ) AS distance ');//print_R($this->db->error());
	$this->db->from('cities c');
	$this->db->join('location_outstation_fare f','f.location_id=c.id');
	$this->db->where('taxi_type',$taxi_type);
	$this->db->order_by('distance','ASC');
	$this->db->limit(1);
	//$this->db->having('distance < 25',false);
        $q =  $this->db->get();
	if($q->num_rows()>0){
	    
	    $data = $q->row();//print_R($data);//print_R(unserialize($data->excluded_cities));exit;
	    if(!empty($data->excluded_cities)){
		$this->db->select('city_name');
		$this->db->from('cities');
		$this->db->where_in('id',unserialize($data->excluded_cities));
		$c =  $this->db->get();
		
		$ex_cities = $c->result();//print_R($ex_cities);
		foreach($ex_cities as $key => $c_val){//print_R($c_val);
		    if(in_array($c_val->city_name,$dest_addr)){
			return array('alert_status'=>'error','msg'=>'destination excluded');
		    }
		}
	    }
	    $currency = $data->currency;
	    if($trip_type=="oneway"){
		/// if oneway trip ///////
		$oneway_fare = unserialize($data->one_way);
		
		if(!empty($oneway_fare)){
		    $radius = array_keys($oneway_fare);
		    $nearest_radius = $this->getNearest($radius,$km);
		    $fareDetails = $oneway_fare[$nearest_radius];
		    $km = round($km,2);
		    $fare =$fareDetails['price'];
		}
	    }else{
		 /// if round trip ////////
		$price = $data->price_per_km;
		$fare = $km * $price;		
	    }
	    if($fare){
		return array('alert_status'=>'success','fare'=>$currency.$fare);
	    }else{
		return false;
	    }
	   
	}
	return false;
    }
    

function getNearest($radius,$km){
    asort($radius);
    foreach($radius as $k => $v){
	if($km<=$v) {
	    return $v;
	}else if($k==count($radius)-1 && $km>$v){
	    return $v;
	}
    }
}
   }
