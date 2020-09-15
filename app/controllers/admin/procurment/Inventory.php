<?php defined('BASEPATH') or exit('No direct script access allowed');

class Inventory extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        if ($this->Customer || $this->Supplier) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
		

        $this->load->admin_model('procurment/inventory_model');
        $this->load->helper('text');
        $this->pos_settings = $this->inventory_model->getSetting();
        $this->pos_settings->pin_code = $this->pos_settings->pin_code ? md5($this->pos_settings->pin_code) : NULL;
        $this->data['pos_settings'] = $this->pos_settings;
        $this->session->set_userdata('last_activity', now());
        $this->lang->admin_load('procurment/inventory', $this->Settings->user_language);
        $this->load->library('form_validation');
		
		$this->Muser_id = $this->session->userdata('user_id');
		$this->Maccess_id = 8;
    }

    public function index()
    {
       
		
		
        $data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
       // $this->data['monthly_sales'] = $this->reports_model->getChartData();
       // $this->data['stock'] = $this->reports_model->getStockValue();
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('inventory')));
        $meta = array('page_title' => lang('inventory'), 'bc' => $bc);
        $this->page_construct('procurment/inventory/index', $meta, $this->data);

    }
	
	
	function accesspermission($id = NULL)
    {

		
        $this->form_validation->set_rules('user_id', lang("user_id"), 'is_natural_no_zero');
        if ($this->form_validation->run() == true) {
			
			$modules = $_POST['modules'];
			$data = array(
				'user_id' => $this->input->post('user_id'),
				'group_id' => $this->input->post('group_id'),
				'access_id' => $this->input->post('access_id'),
				'modules' => json_encode($modules),
				'is_active' => 1,
				'is_create' => date('Y-m-d H:i:s'),
			);
			$id = $this->input->post('user_id');
			
        }


        if ($this->form_validation->run() == true && $this->inventory_model->updateAccessPermissions($id, $data)) {
            $this->session->set_flashdata('message', lang("procurment_permissions_updated"));
            redirect($_SERVER["HTTP_REFERER"]);
        } else {

            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

            $this->data['id'] = $id;
			$this->data['m'] = $this->inventory_model->getProcurmentBYUser($id);
			$this->data['user'] = $this->inventory_model->getUserIDBY($id);
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('users'), 'page' => lang('user')), array('link' => '#', 'page' => lang('procurment_permissions')));
            $meta = array('page_title' => lang('procurment_permissions'), 'bc' => $bc);
            $this->page_construct('procurment/inventory/accesspermissions', $meta, $this->data);
        }
    }
	
	public function notification(){
		$response = $this->siteprocurment->getAccessNotifications($this->session->userdata('user_id'));		
		echo json_encode($response);
		exit;
	}
	
	public function stores(){
		
		$this->form_validation->set_rules('user_id', lang("user_id"), 'is_natural_no_zero');
		
        if ($this->form_validation->run() == true) {
			$notification = array(
				'user_id' => $this->session->userdata('user_id'),
				'group_id' => $this->session->userdata('group_id'),
				'title' => 'Hello',
				'message' => 'Welcome',
				'created_by' => $this->session->userdata('user_id'),
				'created_on' => date('Y-m-d H:i:s'),
			);	
			$this->siteprocurment->insertNotification($notification);
			
		}
		
		$this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('procurment/inventory'), 'page' => lang('inventory')), array('link' => '#', 'page' => lang('stores')));
        $meta = array('page_title' => lang('stores'), 'bc' => $bc);
        $this->page_construct('procurment/inventory/stores', $meta, $this->data);	
	}
	
	function getStores()
    {

        $this->load->library('datatables');
        $this->datatables
			->select("id,code,name", FALSE)
            ->from("warehouses")
			//->join("pro_access a", 'a.id=pro_stores.access_id', 'left')
     
            ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('procurment/inventory/storewise/$1') . "' class='tip' title='" . lang("store_wise") . "' ><i class=\"fa fa-eye\"></i></a> </div>", "id");
        //->unset_column('id');
        echo $this->datatables->generate();
    }
	
	public function storewise($id){
		
		$this->data['id'] = $id;
		$store = $this->siteprocurment->getStoreIDBY($id);
		$this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('procurment/inventory'), 'page' => lang('inventory')), array('link' => admin_url('procurment/inventory/stores'), 'page' => lang('stores')), array('link' => '#', 'page' => $store->name));
        $meta = array('page_title' => $store->name, 'bc' => $bc);
        $this->page_construct('procurment/inventory/storewises', $meta, $this->data);	
	}
	
	function getStorewises($id)
    {
		/*select yt.id, yt.current_quantity, yt.product_id
    from sramhms_pro_stock_master yt
    where current_quantity = 
    SUM(CASE WHEN 
			({$this->db->dbprefix('pro_stock_master')}.transacton_type='IN') THEN {$this->db->dbprefix('pro_stock_master')}.quantity
			WHEN 
			({$this->db->dbprefix('pro_stock_master')}.transacton_type='OUT') THEN {$this->db->dbprefix('pro_stock_master')}.quantity
			
			
			ELSE 0 END) iquantity +  
			
			SUM(CASE 
			WHEN ({$this->db->dbprefix('pro_stock_master')}.transacton_type='OUT') THEN {$this->db->dbprefix('pro_stock_master')}.quantity
			ELSE 0 END) oquantity
    
        (select max(current_quantity) from sramhms_pro_stock_master st where yt.product_id=st.product_id GROUP BY st.product_id ) ORDER BY yt.id DESC
		*/
		 $id = $this->uri->segment(5);
        $this->load->library('datatables');
        $this->datatables
			->select("{$this->db->dbprefix('pro_stock_master')}.id as id, p.name AS product_name, p.code AS product_code, p.type AS type, 
				SUM(CASE WHEN 
			    ({$this->db->dbprefix('pro_stock_master')}.transacton_type='IN') THEN {$this->db->dbprefix('pro_stock_master')}.quantity ELSE 0 END) -
			    SUM(CASE WHEN 
			    ({$this->db->dbprefix('pro_stock_master')}.transacton_type='OUT') THEN {$this->db->dbprefix('pro_stock_master')}.quantity ELSE 0 END) as quantity
			
			")
			->from("pro_stock_master")
			->join("products p", "p.id = {$this->db->dbprefix('pro_stock_master')}.product_id", 'left')
			->where("{$this->db->dbprefix('pro_stock_master')}.store_id", $id)
			->order_by("{$this->db->dbprefix('pro_stock_master')}.id", "ASC")
			//->order_by("{$this->db->dbprefix('pro_stock_master')}.product_id", "DESC")
			->group_by("{$this->db->dbprefix('pro_stock_master')}.product_id")
			;
			
		
        echo $this->datatables->generate();
		//echo $this->db->last_query();
		
		
    }
	
	public function restaurantstore(){
		
		$this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('procurment/inventory'), 'page' => lang('inventory')), array('link' => '#', 'page' => lang('restaurant_stock')));
        $meta = array('page_title' => lang('restaurant_stock'), 'bc' => $bc);
        $this->page_construct('procurment/inventory/restaurant', $meta, $this->data);	
	}

	/*function getRestaurants(){
		
		$purchased = "
		(
			SELECT SRP. recipe_id, SRP.product_id FROM ".$this->db->dbprefix('recipe_product')." SRP         
			group by SRP.recipe_id
			order by SRP.product_id
		) R";
		
		
		
		$this->load->library('datatables');
		$this->datatables
				->select(" P.code, P.name")
				->from("{$this->db->dbprefix("products")} P")
				->join($purchased, "P.id=R.product_id")
				->group_by("P.id");
				//print_R($this->datatables);exit;
		
		
		echo $this->datatables->generate();
		//echo '<pre>';
		//print_r($this->db);exit;
	}*/

}
