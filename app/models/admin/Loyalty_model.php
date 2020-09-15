<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Loyalty_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }


  public function addLoyalty($data, $accumulation, $reedemption)
    {        
        $this->db->insert('loyalty_settings', $data);        
         $loyalty_id = $this->db->insert_id();
        if ($loyalty_id) {            
            foreach ($accumulation as $item) {     
                    $item['loyalty_id'] = $loyalty_id;            
                $this->db->insert('loyalty_accumalation', $item);                                
            }
            foreach ($reedemption as $reedem) {     
                $reedem['loyalty_id'] = $loyalty_id;              
                $this->db->insert('loyalty_redemption', $reedem);               
            }
            return true;
        }        
        return false;
    }

    public function updateLoyalty($id, $data, $accumulation, $reedemption)
    {  
        
        $this->db->update('loyalty_settings', $data, array('id' => $id));        
        $this->db->delete('loyalty_accumalation', array('loyalty_id' => $id));
        $this->db->delete('loyalty_redemption', array('loyalty_id' => $id));        
        if (!empty($accumulation)) {
            foreach ($accumulation as $item) {
                 $item['loyalty_id'] = $id;               
                $this->db->insert('loyalty_accumalation', $item); 
             }                       
            foreach ($reedemption as $reedem) {     
                $reedem['loyalty_id'] = $id;              
                $this->db->insert('loyalty_redemption', $reedem);               
            }               
            return true;
        }            
        return false;
    }

 public function addLoyaltyCards($card_array)
    {        
        if (!empty($card_array)) {
            foreach ($card_array as $card) {                        
                $this->db->insert('loyalty_cards', $card); 
             }                      
                  
            return true;
        } 
        return false;
    }

    public function getLoyalty()
    {
		$this->db->select('ls.id as id, ls.name, ls.from_date, ls.end_date, ls.eligibity_point, ls.prefix, ls.status as status, ls.created_on, ls.created_id, lc.id as card_id');
		$this->db->from('loyalty_settings ls');
		$this->db->join('loyalty_cards lc', 'lc.loyalty_id = ls.id', 'left');
		$this->db->order_by('lc.id', 'DESC');
		$this->db->limit(1);
        $q = $this->db->get();
		
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
				if(!empty($row->card_id)){
					$row->serial_number = sprintf('%04d',$row->card_id+1);
				}else{
					$row->serial_number = sprintf('%04d',1);
				}
				
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getLoyaltyByID($id)
    {
        $q = $this->db->get_where('loyalty_settings', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getLoyaltyCardByID($id)
    {
        $q = $this->db->get_where('loyalty_cards', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }    

    public function getAccumulation($id)
    {
        $q = $this->db->get_where('loyalty_accumalation', array('loyalty_id' => $id));
        if ($q->num_rows() > 0) {
            return $q->result();
        }
        return FALSE;
    }
    public function getReedemption($id)
    {
        $q = $this->db->get_where('loyalty_redemption', array('loyalty_id' => $id));
        if ($q->num_rows() > 0) {
            return $q->result();
        }
        return FALSE;
    }

    public function getSettings()
    {
        $q = $this->db->get('settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function checkLoyalused($id){
        $q = $this->db->get_where('loyalty_points', array('loyalty_id' => $id), 1);        
        if ($q->num_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }    
 public function deleteLoyal($id)
    {
        if ($this->db->delete('loyalty_settings', array('id' => $id))) {
            $this->db->delete('loyalty_redemption', array('loyalty_id' => $id));
            $this->db->delete('loyalty_accumalation', array('loyalty_id' => $id));
            return true;
        }
        return FALSE;
    }
public function deactivate($id = NULL)
    {
        if (($id)) {        
            $data = array(
                'status' => 0
            );
        $return = $this->db->update('loyalty_settings', $data, array('id' => $id));
        return $return;
        }
        return FALSE;
    }   

    public function activate($id = NULL)
    {
        if (($id)) {   
            $data = array(
                'status' => 1
            );

        $return = $this->db->update('loyalty_settings', $data, array('id' => $id));
        return $return;
        }
        return FALSE;
    }       
public function card_deactivate($id = NULL)
    {
        if (($id)) {        
            $data = array(
                'status' => 0
            );
        $return = $this->db->update('loyalty_cards', $data, array('id' => $id));
        return $return;
        }
        return FALSE;
    }   

    public function card_activate($id = NULL)
    {
        if (($id)) {   
            $data = array(
                'status' => 1
            );

        $return = $this->db->update('loyalty_cards', $data, array('id' => $id));
        return $return;
        }
        return FALSE;
    }      
}
