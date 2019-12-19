<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Frontend_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
		
    }

	public function getUsers($user_number){
		$query = $this->db->select('user_number, username, email, id, password, active, last_login, last_ip_address, avatar, gender, group_id, warehouse_id, biller_id, company_id, view_right, edit_right, allow_discount, show_cost, show_price')
            ->where('user_number', $user_number)
            ->limit(1)
            ->get('users');
		
		
		
			
		if ($query->num_rows() === 1) {
            $user = $query->row();
			return $user;	
		}
		return FALSE;
	}
		
	/*public function login($user_number, $user_group, $user_warehouse){
		
		
		
		$query = $this->db->select('user_number, username, email, id, password, active, last_login, last_ip_address, avatar, gender, group_id, warehouse_id, biller_id, company_id, view_right, edit_right, allow_discount, show_cost, show_price')
            ->where('user_number', $user_number)
			->where('group_id', $user_group)
			->where('warehouse_id', $user_warehouse)
            ->limit(1)
            ->get('users');
		
		
		
			
		if ($query->num_rows() === 1) {
            $user = $query->row();

			if ($user->active != 1) {
				$this->set_error('login_unsuccessful_not_active');
				return FALSE;
			}
			
			$this->set_session($user);
			
			$this->update_last_login($user->id);
			$this->update_last_login_ip($user->id);
			$ldata = array('user_id' => $user->id, 'ip_address' => $this->input->ip_address(), 'login' => $user->id);
			
			$ldata['session_id'] = session_id();
			$ldata['email'] = $user->email;
			$ldata['username'] = $user->username;
			$ldata['status'] = "logged_in";
			$ldata['last_activity'] = date('Y-m-d H:i:s');
			$ldata['login_type'] = 'F';
			$ldata['expiry'] = date('Y-m-d H:i:s',time() + config_item('sess_expiration'));
			$this->db->insert('user_logins', $ldata);

			

			return TRUE;
            
        }
		
		return FALSE;
	}*/
	public function login($user_number){
		
		$query = $this->db->select('users.user_number, users.username, users.email, users.id, users.password, users.active, users.last_login, users.last_ip_address, users.avatar, users.gender,users.group_id, users.warehouse_id, users.biller_id, users.company_id, users.view_right, users.edit_right, users.allow_discount, users.show_cost,users.show_price,users.first_name,users.last_name,groups.name as group_name')		
		     ->join('groups','groups.id = users.group_id') 
            ->where('user_number', $user_number)
			/*->where('group_id', $user_group)
			->where('warehouse_id', $user_warehouse)*/
            ->limit(1)
            ->get('users');
		
		
		
			
		if ($query->num_rows() === 1) {
            $user = $query->row();

			if ($user->active != 1) {
				$this->set_error('login_unsuccessful_not_active');
				return FALSE;
			}
			
			$this->set_session($user);
			
			$this->update_last_login($user->id);
			$this->update_last_login_ip($user->id);
			$ldata = array('user_id' => $user->id, 'ip_address' => $this->input->ip_address(), 'login' => $user->id);
			
			$ldata['session_id'] = session_id();
			$ldata['email'] = $user->email;
			$ldata['username'] = $user->username;
			$ldata['status'] = "logged_in";
			$ldata['last_activity'] = date('Y-m-d H:i:s');
			$ldata['login_type'] = 'F';
			$ldata['expiry'] = date('Y-m-d H:i:s',time() + config_item('sess_expiration'));
			$this->db->insert('user_logins', $ldata);

			

			return TRUE;
            
        }
		
		return FALSE;
	}
	public function set_session($user)
    {

        $session_data = array(
			'identity' => $user->user_number,
			'user_number' => $user->user_number,
            'username' => $user->username,
            'email' => $user->email,
            'user_id' => $user->id, //everyone likes to overwrite id so we'll use user_id
            'old_last_login' => $user->last_login,
            'last_ip' => $user->last_ip_address,
            'avatar' => $user->avatar,
            'gender' => $user->gender,
            'group_id' => $user->group_id,
            'warehouse_id' => $user->warehouse_id,
            'view_right' => $user->view_right,
            'edit_right' => $user->edit_right,
            'allow_discount' => $user->allow_discount,
            'biller_id' => $user->biller_id,
            'company_id' => $user->company_id,
            'show_cost' => $user->show_cost,
            'show_price' => $user->show_price,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'group_name' => $user->group_name,
        );

        $this->session->set_userdata($session_data);

        return TRUE;
    }
	
	public function update_last_login($id)
    {

        $this->load->helper('date');
        $this->db->update('users', array('last_login' => time()), array('id' => $id));
        return $this->db->affected_rows() == 1;
    }

    public function update_last_login_ip($id)
    {

        $this->db->update('users', array('last_ip_address' => $this->input->ip_address()), array('id' => $id));

        return $this->db->affected_rows() == 1;
    }
    public function set_error($error)
    {
        $this->errors[] = $error;

        return $error;
    }
}
