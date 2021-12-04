<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Production extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        $this->lang->admin_load('production', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('production_model');
        $this->digital_upload_path = 'files/';
        $this->upload_path = 'assets/uploads/';
        $this->thumbs_path = 'assets/uploads/thumbs/';
        $this->upload_path = 'assets/uploads/customers/';
        $this->thumbs_path = 'assets/uploads/customers/thumbs/';
       $this->image_types = 'gif|jpg|png|jpeg|pdf';
		//$this->photo_types = 'jpg|jpeg';
		//$this->pdf_types = 'pdf';
		$this->photo_types = 'gif|jpg|png|jpeg|pdf';
		$this->pdf_types = 'gif|jpg|png|jpeg|pdf';
        $this->digital_file_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
        $this->allowed_file_size = '1024';
        $this->popup_attributes = array('width' => '900', 'height' => '600', 'window_name' => 'sma_popup', 'menubar' => 'yes', 'scrollbars' => 'yes', 'status' => 'no', 'resizable' => 'yes', 'screenx' => '0', 'screeny' => '0');
    }

    function index($warehouse_id = NULL)
    {
        $this->sma->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['warehouse_id'] = $warehouse_id;
            $this->data['warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : NULL;
        } else {
            $this->data['warehouses'] = NULL;
            $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
            $this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : NULL;
        }

        $this->data['supplier'] = $this->input->get('supplier') ? $this->site->getCompanyByID($this->input->get('supplier')) : NULL;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('production')));
        $meta = array('page_title' => lang('production'), 'bc' => $bc);
        $this->page_construct('production/index', $meta, $this->data);
    }

    function getproduction($warehouse_id = NULL)
    {
        $this->sma->checkPermissions('index', TRUE);
        //$supplier = $this->input->get('supplier') ? $this->input->get('supplier') : NULL;

        if ((! $this->Owner || ! $this->Admin)) {
            $user = $this->site->getUser();
        }
     
        $action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
            . lang('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
          
           
            <li><a href="' . admin_url('production/edit/$1') . '"><i class="fa fa-edit"></i> ' . lang('edit_production') . '</a></li>';
        if ($warehouse_id) {
            $action .= '<li><a href="' . admin_url('production/set_rack/$1/' . $warehouse_id) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-bars"></i> '
                . lang('set_rack') . '</a></li>';
        }
        $action .= '
            
            <li class="divider"></li>
            
            </ul>
        </div></div>';
        $this->load->library('datatables');
		
        $this->datatables
                ->select("'sno',".$this->db->dbprefix('production') . ".id as productionid,     {$this->db->dbprefix('production')}.production_date as date, {$this->db->dbprefix('warehouses')}.name as bname", FALSE)
                ->from('production')
                ->join('warehouses', 'warehouses.id=production.warehouse_id', 'left')
				->where('production.status', 'Open')
                ->group_by("production.id");
       
        
        $this->datatables->add_column("Actions", $action, "productionid, name, date, bname");
        echo $this->datatables->generate();
    }

	function balance($warehouse_id = NULL)
    {
        $this->sma->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['warehouse_id'] = $warehouse_id;
            $this->data['warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : NULL;
        } else {
            $this->data['warehouses'] = NULL;
            $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
            $this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : NULL;
        }

        $this->data['supplier'] = $this->input->get('supplier') ? $this->site->getCompanyByID($this->input->get('supplier')) : NULL;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('production')));
        $meta = array('page_title' => lang('production'), 'bc' => $bc);
        $this->page_construct('production/balance', $meta, $this->data);
    }
   

	function getbalanceproduction($warehouse_id = NULL)
    {
        $this->sma->checkPermissions('balance', TRUE);
        //$supplier = $this->input->get('supplier') ? $this->input->get('supplier') : NULL;

        if ((! $this->Owner || ! $this->Admin)) {
            $user = $this->site->getUser();
        }
     
        $action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
            . lang('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
            <li>' . $detail_link . '</li>
           
            <li><a href="' . admin_url('production/balance_edit/$1') . '"><i class="fa fa-edit"></i> ' . lang('edit_production') . '</a></li>';
			
        if ($warehouse_id) {
            $action .= '<li><a href="' . admin_url('production/set_rack/$1/' . $warehouse_id) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-bars"></i> '
                . lang('set_rack') . '</a></li>';
        }
        $action .= '
            
            <li class="divider"></li>
            <li>' . $delete_link . '</li>
            </ul>
        </div></div>';
        $this->load->library('datatables');
		
        $this->datatables
                ->select($this->db->dbprefix('production') . ".id as productionid,   {$this->db->dbprefix('production')}.production_date as date, {$this->db->dbprefix('production')}.status as status, {$this->db->dbprefix('warehouses')}.name as bname", FALSE)
                ->from('production')
                ->join('warehouses', 'warehouses.id=production.warehouse_id', 'left')
                ->group_by("production.id");
       
        
        $this->datatables->add_column("Actions", $action, "productionid, name, date, bname");
        echo $this->datatables->generate();
    }
	
    /* ------------------------------------------------------- */

    function add($id = NULL)
    {
		
        $this->sma->checkPermissions();
        $this->load->helper('security');
        $warehouses = $this->site->getAllWarehouses();
        
        
        $this->form_validation->set_rules('warehouse_id', lang("warehouse_id"), 'required');
		$this->form_validation->set_rules('production_date', lang("production_date"), 'required');
		
		
        if ($this->form_validation->run() == true) {
			
			$production_array = array(
				'warehouse_id' => $this->input->post('warehouse_id'),
				'production_date' => $this->input->post('production_date'),
				'created_by' => $this->session->userdata('user_id'),
				'date' => date('Y-m-d H:i:s'),
				'status' => 'Open',
				'reference_no' => date('YmdHis')	
			);
			$k=0;
			for($j=0; $j<count($this->input->post('product_id[]')); $j++){
				if($this->input->post('given_quantity['.$j.']') != 0){
					$k++;
				}
				$purchases_item[] = array(
					'product_id' => $this->input->post('product_id['.$j.']'),
					'product_code' => $this->input->post('product_code['.$j.']'),
					'product_name' => $this->input->post('product_name['.$j.']'),
					'qoh' => $this->input->post('product_qoh['.$j.']'),
					'quantity' => $this->input->post('quantity['.$j.']'),
					'sale_unit' => $this->input->post('sale_unit['.$j.']'),
					'given_quantity' => $this->input->post('given_quantity['.$j.']'),
					'given_unit' => $this->input->post('given_unit['.$j.']'),
					'warehouse_id' => $this->input->post('warehouse_id'),
				);
			}
			
			if(empty($purchases_item) || (count($this->input->post('product_id[]')) != $k)){
				 $this->session->set_flashdata('error', lang("purchase_item_is_empty_or_quantity_value_empty"));
           		 admin_redirect('production/add');
			}
           
        }
		
		
		
        if ($this->form_validation->run() == true && $this->production_model->addproduction_new($production_array, $purchases_item)) 			{
			
            $this->session->set_flashdata('message', lang("production_added"));
            admin_redirect('production');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['warehouses'] = $warehouses;
			$this->data['units'] = $this->site->getAllUnits();
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('production'), 'page' => lang('production')), array('link' => '#', 'page' => lang('add_production')));
            $meta = array('page_title' => lang('add_production'), 'bc' => $bc);
            $this->page_construct('production/add', $meta, $this->data);
			
        }
    }
	
	function product_suggestions($term = NULL, $limit = NULL)
    {
        // $this->sma->checkPermissions('index');
        if ($this->input->get('term')) {
            $term = $this->input->get('term', TRUE);
        }
        $limit = $this->input->get('limit', TRUE);
        $rows['results'] = $this->production_model->getproductionProductSuggestions($term, $limit);
        $this->sma->send_json($rows);
    }
	
	function product_units($term = NULL)
    {
        // $this->sma->checkPermissions('index');
        if ($this->input->get('term')) {
            $term = $this->input->get('term', TRUE);
        }

        $rows['results'] = $this->production_model->getproductionProductsalesUnits($term);
        $this->sma->send_json($rows);
    }
	
    function suggestions()
    {
        $term = $this->input->get('term', TRUE);
        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('welcome') . "'; }, 10);</script>");
        }

        $rows = $this->production_model->getproductionNames($term);
		$units = $this->site->getAllUnits();
        if ($rows) {
            foreach ($rows as $row) {
                $pr[] = array('id' => $row->id, 'label' => $row->name,  'name' => $row->name, 'code' => $row->code, 'quantity' => $row->quantity, 'qoh' => $row->avail_quantity, 'sale_unit' => $row->sale_unit, 'given_quantity' => 0, 'given_units' => '',   'units' => $units);
            }
			
            $this->sma->send_json($pr);
        } else {
            $this->sma->send_json(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
        }
    }

    function get_suggestions()
    {
        $term = $this->input->get('term', TRUE);
        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('welcome') . "'; }, 10);</script>");
        }

        $rows = $this->production_model->getproductionForPrinting($term);
        if ($rows) {
            foreach ($rows as $row) {
                $variants = $this->production_model->getproductionOptions($row->id);
                $pr[] = array('id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => 1, 'variants' => $variants);
            }
            $this->sma->send_json($pr);
        } else {
            $this->sma->send_json(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
        }
    }

    function addByAjax()
    {
        if (!$this->mPermissions('add')) {
            exit(json_encode(array('msg' => lang('access_denied'))));
        }
        if ($this->input->get('token') && $this->input->get('token') == $this->session->userdata('user_csrf') && $this->input->is_ajax_request()) {
            $production = $this->input->get('production');
            if (!isset($production['code']) || empty($production['code'])) {
                exit(json_encode(array('msg' => lang('production_code_is_required'))));
            }
            if (!isset($production['name']) || empty($production['name'])) {
                exit(json_encode(array('msg' => lang('production_name_is_required'))));
            }
            if (!isset($production['category_id']) || empty($production['category_id'])) {
                exit(json_encode(array('msg' => lang('production_category_is_required'))));
            }
            if (!isset($production['unit']) || empty($production['unit'])) {
                exit(json_encode(array('msg' => lang('production_unit_is_required'))));
            }
            if (!isset($production['price']) || empty($production['price'])) {
                exit(json_encode(array('msg' => lang('production_price_is_required'))));
            }
            if (!isset($production['cost']) || empty($production['cost'])) {
                exit(json_encode(array('msg' => lang('production_cost_is_required'))));
            }
            if ($this->production_model->getproductionByCode($production['code'])) {
                exit(json_encode(array('msg' => lang('production_code_already_exist'))));
            }
            if ($row = $this->production_model->addAjaxproduction($production)) {
                $tax_rate = $this->site->getTaxRateByID($row->tax_rate);
                $pr = array('id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'code' => $row->code, 'qty' => 1, 'cost' => $row->cost, 'name' => $row->name, 'tax_method' => $row->tax_method, 'tax_rate' => $tax_rate, 'discount' => '0');
                $this->sma->send_json(array('msg' => 'success', 'result' => $pr));
            } else {
                exit(json_encode(array('msg' => lang('failed_to_add_production'))));
            }
        } else {
            json_encode(array('msg' => 'Invalid token'));
        }

    }


    /* -------------------------------------------------------- */

    function edit($id = NULL)
    {
		$item = array();
		
        $this->sma->checkPermissions();
        $this->load->helper('security');
        if ($this->input->post('id')) {
            $id = $this->input->post('id');
        }
        $warehouses = $this->site->getAllWarehouses();
		$production = $this->production_model->getproductionByID($id);
		$production_item = $this->production_model->getproductionItems($id);
		
		
		$units = $this->site->getAllUnits();
        if ($production_item) {
            foreach ($production_item as $row) {
                $item[$row->product_id] = array('id' => $row->product_id, 'label' => $row->product_name,  'name' => $row->product_name, 'code' => $row->product_code, 'quantity' => $row->quantity, 'qoh' => $row->qoh, 'sale_unit' => $row->sale_unit, 'given_quantity' => $row->given_quantity, 'given_units' => $row->given_unit, 'units' => $units);
            }
        } 
        
		
        $this->form_validation->set_rules('warehouse_id', lang("warehouse_id"), 'required');
		$this->form_validation->set_rules('production_date', lang("production_date"), 'required');
		
		
        if ($this->form_validation->run() == true) {
			
			$production_array = array(
				'warehouse_id' => $this->input->post('warehouse_id'),
				'production_date' => $this->input->post('production_date'),
				'created_by' => $this->session->userdata('user_id'),
				'date' => date('Y-m-d H:i:s'),
			);
			$k=0;
			for($j=0; $j<count($this->input->post('product_id[]')); $j++){
				if($this->input->post('given_quantity['.$j.']') != 0){
					$k++;
				}
				$purchases_item[] = array(
					'product_id' => $this->input->post('product_id['.$j.']'),
					'product_code' => $this->input->post('product_code['.$j.']'),
					'product_name' => $this->input->post('product_name['.$j.']'),
					'quantity' => $this->input->post('quantity['.$j.']'),
					'sale_unit' => $this->input->post('sale_unit['.$j.']'),
					'given_quantity' => $this->input->post('given_quantity['.$j.']'),
					'given_unit' => $this->input->post('given_unit['.$j.']'),
					'warehouse_id' => $this->input->post('warehouse_id'),
				);
			}
			
			if(empty($purchases_item) || (count($this->input->post('product_id[]')) != $k)){
				 $this->session->set_flashdata('error', lang("purchase_item_is_empty_or_quantity_value_empty"));
           		 admin_redirect('production/edit/'.$id);
			}
           
        }

		
		
		
        if ($this->form_validation->run() == true && $this->production_model->updateproduction_new($id, $production_array, $purchases_item)) {		
			
			
            $this->session->set_flashdata('message', lang("production_updated"));
            admin_redirect('production');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['units'] = $this->site->getAllUnits();
            $this->data['warehouses'] = $warehouses;
			
			$this->data['production'] = $production;
            
			$this->data['production_item'] = json_encode($item);
			
			
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('production'), 'page' => lang('production')), array('link' => '#', 'page' => lang('edit_production')));
			
            $meta = array('page_title' => lang('edit_production'), 'bc' => $bc);
            $this->page_construct('production/edit', $meta, $this->data);
        }
    }
	
	function balance_edit($id = NULL)
    {
		$item = array();
		
        $this->sma->checkPermissions();
        $this->load->helper('security');
        if ($this->input->post('id')) {
            $id = $this->input->post('id');
        }
        $warehouses = $this->site->getAllWarehouses();
		$production = $this->production_model->getproductionByID($id);
		$production_item = $this->production_model->getproductionItems($id);
		
		
		$units = $this->site->getAllUnits();
        if ($production_item) {
            foreach ($production_item as $row) {
                $item[$row->product_id] = array('id' => $row->product_id, 'label' => $row->product_name,  'name' => $row->product_name, 'code' => $row->product_code, 'quantity' => $row->quantity, 'qoh' => $row->qoh,  'sale_unit' => $row->sale_unit, 'given_quantity' => $row->given_quantity, 'given_units' => $row->given_unit, 'balance_quantity' => $row->balance_quantity, 'balance_units' => $row->balance_unit, 'units' => $units);
            }
        } 
        
		
        $this->form_validation->set_rules('warehouse_id', lang("warehouse_id"), 'required');
		
		
        if ($this->form_validation->run() == true) {
			
			$production_array = array(
				'warehouse_id' => $this->input->post('warehouse_id'),
				'production_date' => $this->input->post('production_date'),
				'created_by' => $this->session->userdata('user_id'),
				'status' => 'Closed',
				'date' => date('Y-m-d H:i:s'),
			);
			$k=0;
			for($j=0; $j<count($this->input->post('product_id[]')); $j++){
				if($this->input->post('balance_quantity['.$j.']') != 0){
					$k++;
				}
				$purchases_item[] = array(
					'product_id' => $this->input->post('product_id['.$j.']'),
					'product_code' => $this->input->post('product_code['.$j.']'),
					'product_name' => $this->input->post('product_name['.$j.']'),
					'quantity' => $this->input->post('quantity['.$j.']'),
					'sale_unit' => $this->input->post('sale_unit['.$j.']'),
					'given_quantity' => $this->input->post('given_quantity['.$j.']'),
					'given_unit' => $this->input->post('given_unit['.$j.']'),
					'balance_quantity' => $this->input->post('balance_quantity['.$j.']'),
					'balance_unit' => $this->input->post('balance_unit['.$j.']'),
					'warehouse_id' => $this->input->post('warehouse_id'),
				);
			}
			
			if(empty($purchases_item) || (count($this->input->post('product_id[]')) != $k)){
				 $this->session->set_flashdata('error', lang("purchase_item_is_empty_or_quantity_value_empty"));
           		 admin_redirect('production/balance_edit/'.$id);
			}
           
        }

		
		
		
        if ($this->form_validation->run() == true && $this->production_model->updateproduction_new($id, $production_array, $purchases_item)) {		
			
			
            $this->session->set_flashdata('message', lang("production_updated"));
            admin_redirect('production/balance');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['units'] = $this->site->getAllUnits();
            $this->data['warehouses'] = $warehouses;
			$this->data['production'] = $production;
			$this->data['production_item'] = json_encode($item);
			
			
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('production'), 'page' => lang('production')), array('link' => '#', 'page' => lang('edit balance_production')));
			
            $meta = array('page_title' => lang('edit_production'), 'bc' => $bc);
            $this->page_construct('production/balance_edit', $meta, $this->data);
        }
    }


   
    function delete($id = NULL)
    {
        $this->sma->checkPermissions(NULL, TRUE);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->production_model->deleteproduction($id)) {
            if($this->input->is_ajax_request()) {
                $this->sma->send_json(array('error' => 0, 'msg' => lang("production_deleted")));
            }
            $this->session->set_flashdata('message', lang('production_deleted'));
            admin_redirect('welcome');
        }

    }


    function view($id = NULL)
    {
		
        $this->sma->checkPermissions('index');

        $pr_details = $this->production_model->getproductionByID($id);
		
        if (!$id || !$pr_details) {
            $this->session->set_flashdata('error', lang('production_not_found'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $this->data['barcode'] = "<img src='" . admin_url('production/gen_barcode/' . $pr_details->code . '/' . $pr_details->barcode_symbology . '/40/0') . "' alt='" . $pr_details->code . "' class='pull-left' />";
        if ($pr_details->type == 'combo') {
            $this->data['combo_items'] = $this->production_model->getproductionComboItems($id);
        }
		
        $this->data['production'] = $pr_details;
		
        $this->data['unit'] = $this->site->getUnitByID($pr_details->unit);
        $this->data['brand'] = $this->site->getBrandByID($pr_details->brand);
        $this->data['images'] = $this->production_model->getproductionPhotos($id);
        $this->data['category'] = $this->site->getproductionCategoryByID($pr_details->category_id);
        $this->data['subcategory'] = $pr_details->subcategory_id ? $this->site->getproductionCategoryByID($pr_details->subcategory_id) : NULL;
        $this->data['tax_rate'] = $pr_details->tax_rate ? $this->site->getTaxRateByID($pr_details->tax_rate) : NULL;
        $this->data['popup_attributes'] = $this->popup_attributes;
        $this->data['warehouses'] = $this->production_model->getAllWarehousesWithPQ($id);
        $this->data['options'] = $this->production_model->getproductionOptionsWithWH($id);
        $this->data['variants'] = $this->production_model->getproductionOptions($id);
		
		
		
        $this->data['sold'] = $this->production_model->getSoldQty($id);
		
        //$this->data['purchased'] = $this->production_model->getPurchasedQty($id);

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('production'), 'page' => lang('production')), array('link' => '#', 'page' => $pr_details->name));
		
        $meta = array('page_title' => $pr_details->name, 'bc' => $bc);
		
        $this->page_construct('production/view', $meta, $this->data);
		
    }

    function pdf($id = NULL, $view = NULL)
    {
        $this->sma->checkPermissions('index');

        $pr_details = $this->production_model->getproductionByID($id);
        if (!$id || !$pr_details) {
            $this->session->set_flashdata('error', lang('prduct_not_found'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $this->data['barcode'] = "<img src='" . admin_url('production/gen_barcode/' . $pr_details->code . '/' . $pr_details->barcode_symbology . '/40/0') . "' alt='" . $pr_details->code . "' class='pull-left' />";
        if ($pr_details->type == 'combo') {
            $this->data['combo_items'] = $this->production_model->getproductionComboItems($id);
        }
        $this->data['production'] = $pr_details;
        $this->data['unit'] = $this->site->getUnitByID($pr_details->unit);
        $this->data['brand'] = $this->site->getBrandByID($pr_details->brand);
        $this->data['images'] = $this->production_model->getproductionPhotos($id);
        $this->data['category'] = $this->site->getproductionCategoryByID($pr_details->category_id);
        $this->data['subcategory'] = $pr_details->subcategory_id ? $this->site->getproductionCategoryByID($pr_details->subcategory_id) : NULL;
        $this->data['tax_rate'] = $pr_details->tax_rate ? $this->site->getTaxRateByID($pr_details->tax_rate) : NULL;
        $this->data['popup_attributes'] = $this->popup_attributes;
        $this->data['warehouses'] = $this->production_model->getAllWarehousesWithPQ($id);
        $this->data['options'] = $this->production_model->getproductionOptionsWithWH($id);
        $this->data['variants'] = $this->production_model->getproductionOptions($id);

        $name = $pr_details->code . '_' . str_replace('/', '_', $pr_details->name) . ".pdf";
        if ($view) {
            $this->load->view($this->theme . 'production/pdf', $this->data);
        } else {
            $html = $this->load->view($this->theme . 'production/pdf', $this->data, TRUE);
            if (! $this->Settings->barcode_img) {
                $html = preg_replace("'\<\?xml(.*)\?\>'", '', $html);
            }
            $this->sma->generate_pdf($html, $name);
        }
    }

  
    function production_actions($wh = NULL)
    {
        if (!$this->Owner && !$this->GP['bulk_actions']) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'sync_quantity') {


                    foreach ($_POST['val'] as $id) {
                        $this->site->syncQuantity(NULL, NULL, NULL, $id);
                    }
                    $this->session->set_flashdata('message', $this->lang->line("production_quantity_sync"));
                    redirect($_SERVER["HTTP_REFERER"]);

                } elseif ($this->input->post('form_action') == 'delete') {

                    $this->sma->checkPermissions('delete');
                    foreach ($_POST['val'] as $id) {
                        $this->production_model->deleteproduction($id);
                    }
                    $this->session->set_flashdata('message', $this->lang->line("production_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);

                } elseif ($this->input->post('form_action') == 'labels') {

                    foreach ($_POST['val'] as $id) {
                        $row = $this->production_model->getproductionByID($id);
                        $selected_variants = false;
                        if ($variants = $this->production_model->getproductionOptions($row->id)) {
                            foreach ($variants as $variant) {
                                $selected_variants[$variant->id] = $variant->quantity > 0 ? 1 : 0;
                            }
                        }
                        $pr[$row->id] = array('id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => $row->quantity, 'variants' => $variants, 'selected_variants' => $selected_variants);
                    }

                    $this->data['items'] = isset($pr) ? json_encode($pr) : false;
                    $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
                    $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('production'), 'page' => lang('production')), array('link' => '#', 'page' => lang('print_barcodes')));
                    $meta = array('page_title' => lang('print_barcodes'), 'bc' => $bc);
                    $this->page_construct('production/print_barcodes', $meta, $this->data);

                } elseif ($this->input->post('form_action') == 'export_excel') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle('production');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('code'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('barcode_symbology'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('brand'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('category_code'));
                    $this->excel->getActiveSheet()->SetCellValue('F1', lang('unit_code'));
                    $this->excel->getActiveSheet()->SetCellValue('G1', lang('sale').' '.lang('unit_code'));
                    $this->excel->getActiveSheet()->SetCellValue('H1', lang('purchase').' '.lang('unit_code'));
                    $this->excel->getActiveSheet()->SetCellValue('I1', lang('cost'));
                    $this->excel->getActiveSheet()->SetCellValue('J1', lang('price'));
                    $this->excel->getActiveSheet()->SetCellValue('K1', lang('alert_quantity'));
                    $this->excel->getActiveSheet()->SetCellValue('L1', lang('tax_rate'));
                    $this->excel->getActiveSheet()->SetCellValue('M1', lang('tax_method'));
                    $this->excel->getActiveSheet()->SetCellValue('N1', lang('image'));
                    $this->excel->getActiveSheet()->SetCellValue('O1', lang('subcategory_code'));
                    $this->excel->getActiveSheet()->SetCellValue('P1', lang('production_variants'));
                    $this->excel->getActiveSheet()->SetCellValue('Q1', lang('pcf1'));
                    $this->excel->getActiveSheet()->SetCellValue('R1', lang('pcf2'));
                    $this->excel->getActiveSheet()->SetCellValue('S1', lang('pcf3'));
                    $this->excel->getActiveSheet()->SetCellValue('T1', lang('pcf4'));
                    $this->excel->getActiveSheet()->SetCellValue('U1', lang('pcf5'));
                    $this->excel->getActiveSheet()->SetCellValue('V1', lang('pcf6'));
                    $this->excel->getActiveSheet()->SetCellValue('W1', lang('quantity'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $production = $this->production_model->getproductionDetail($id);
                        $brand = $this->site->getBrandByID($production->brand);
                        $base_unit = $sale_unit = $purchase_unit = '';
                        if($units = $this->site->getUnitsByBUID($production->unit)) {
                            foreach($units as $u) {
                                if ($u->id == $production->unit) {
                                    $base_unit = $u->code;
                                }
                                if ($u->id == $production->sale_unit) {
                                    $sale_unit = $u->code;
                                }
                                if ($u->id == $production->purchase_unit) {
                                    $purchase_unit = $u->code;
                                }
                            }
                        }
                        $variants = $this->production_model->getproductionOptions($id);
                        $production_variants = '';
                        if ($variants) {
                            foreach ($variants as $variant) {
                                $production_variants .= trim($variant->name) . '|';
                            }
                        }
                        $quantity = $production->quantity;
                        if ($wh) {
                            if($wh_qty = $this->production_model->getproductionQuantity($id, $wh)) {
                                $quantity = $wh_qty['quantity'];
                            } else {
                                $quantity = 0;
                            }
                        }
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $production->name);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $production->code);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $production->barcode_symbology);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, ($brand ? $brand->name : ''));
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $production->category_code);
                        $this->excel->getActiveSheet()->SetCellValue('F' . $row, $base_unit);
                        $this->excel->getActiveSheet()->SetCellValue('G' . $row, $sale_unit);
                        $this->excel->getActiveSheet()->SetCellValue('H' . $row, $purchase_unit);
                        if ($this->Owner || $this->Admin || $this->session->userdata('show_cost')) {
                            $this->excel->getActiveSheet()->SetCellValue('I' . $row, $production->cost);
                        }
                        if ($this->Owner || $this->Admin || $this->session->userdata('show_price')) {
                            $this->excel->getActiveSheet()->SetCellValue('J' . $row, $production->price);
                        }
                        $this->excel->getActiveSheet()->SetCellValue('K' . $row, $production->alert_quantity);
                        $this->excel->getActiveSheet()->SetCellValue('L' . $row, $production->tax_rate_name);
                        $this->excel->getActiveSheet()->SetCellValue('M' . $row, $production->tax_method ? lang('exclusive') : lang('inclusive'));
                        $this->excel->getActiveSheet()->SetCellValue('N' . $row, $production->image);
                        $this->excel->getActiveSheet()->SetCellValue('O' . $row, $production->subcategory_code);
                        $this->excel->getActiveSheet()->SetCellValue('P' . $row, $production_variants);
                        $this->excel->getActiveSheet()->SetCellValue('Q' . $row, $production->cf1);
                        $this->excel->getActiveSheet()->SetCellValue('R' . $row, $production->cf2);
                        $this->excel->getActiveSheet()->SetCellValue('S' . $row, $production->cf3);
                        $this->excel->getActiveSheet()->SetCellValue('T' . $row, $production->cf4);
                        $this->excel->getActiveSheet()->SetCellValue('U' . $row, $production->cf5);
                        $this->excel->getActiveSheet()->SetCellValue('V' . $row, $production->cf6);
                        $this->excel->getActiveSheet()->SetCellValue('W' . $row, $quantity);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                    $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(40);
                    $this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(30);
                    $this->excel->getActiveSheet()->getColumnDimension('P')->setWidth(30);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'production_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
            } else {
                $this->session->set_flashdata('error', $this->lang->line("no_production_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'admin/production');
        }
    }

   

    public function getSubUnits($unit_id)
    {
        // $unit = $this->site->getUnitByID($unit_id);
        // if ($units = $this->site->getUnitsByBUID($unit_id)) {
        //     array_push($units, $unit);
        // } else {
        //     $units = array($unit);
        // }
        $units = $this->site->getUnitsByBUID($unit_id);
        $this->sma->send_json($units);
    }

 

}
