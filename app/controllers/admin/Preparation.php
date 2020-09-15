<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Preparation extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        $this->lang->admin_load('preparation', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('preparation_model');
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
        //$this->sma->checkPermissions();

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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('preparation')));
        $meta = array('page_title' => lang('preparation'), 'bc' => $bc);
        $this->page_construct('preparation/index', $meta, $this->data);
    }

    function getpreparation($warehouse_id = NULL)
    {
        //$this->sma->checkPermissions('index', TRUE);
        //$supplier = $this->input->get('supplier') ? $this->input->get('supplier') : NULL;
		
        if ((! $this->Owner || ! $this->Admin)) {
            $user = $this->site->getUser();
        }
     
        $action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
            . lang('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
           
           
            <li><a href="' . admin_url('preparation/edit/$1') . '"><i class="fa fa-edit"></i> ' . lang('edit_preparation') . '</a></li>';
        if ($warehouse_id) {
            $action .= '<li><a href="' . admin_url('preparation/set_rack/$1/' . $warehouse_id) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-bars"></i> '
                . lang('set_rack') . '</a></li>';
        }
        $action .= '
            
            <li class="divider"></li>
            
            </ul>
        </div></div>';
        $this->load->library('datatables');
		
      
				
        $this->datatables
                ->select("{$this->db->dbprefix('preparation')}.id as id, {$this->db->dbprefix('preparation')}.preparation_date,  {$this->db->dbprefix('preparation')}.reference_no, , w.name as bname", FALSE)
                ->from('preparation')
                ->join('warehouses w', "w.id=preparation.warehouse_id", 'left')
				->where("preparation.status", 'Open')
                ->group_by("preparation.id");
        
        $this->datatables->add_column("Actions", $action, "id");
		
        echo $this->datatables->generate();
		
    }

	function balance($warehouse_id = NULL)
    {
        //$this->sma->checkPermissions();

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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('preparation')));
        $meta = array('page_title' => lang('preparation'), 'bc' => $bc);
        $this->page_construct('preparation/balance', $meta, $this->data);
    }
   

	function getbalancepreparation($warehouse_id = NULL)
    {
        //$this->sma->checkPermissions('index', TRUE);
        //$supplier = $this->input->get('supplier') ? $this->input->get('supplier') : NULL;

        if ((! $this->Owner || ! $this->Admin)) {
            $user = $this->site->getUser();
        }
     
        $action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
            . lang('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
            <li>' . $detail_link . '</li>
           
            <li><a href="' . admin_url('preparation/balance_edit/$1') . '"><i class="fa fa-edit"></i> ' . lang('edit_preparation') . '</a></li>';
        if ($warehouse_id) {
            $action .= '<li><a href="' . admin_url('preparation/set_rack/$1/' . $warehouse_id) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-bars"></i> '
                . lang('set_rack') . '</a></li>';
        }
        $action .= '
            
            <li class="divider"></li>
            <li>' . $delete_link . '</li>
            </ul>
        </div></div>';
        $this->load->library('datatables');
		
        $this->datatables
                ->select($this->db->dbprefix('preparation') . ".id as preparationid,   {$this->db->dbprefix('preparation')}.preparation_name as name,  {$this->db->dbprefix('preparation')}.preparation_date as date, {$this->db->dbprefix('preparation')}.status as status, {$this->db->dbprefix('warehouses')}.name as bname", FALSE)
                ->from('preparation')
                ->join('warehouses', 'warehouses.id=preparation.warehouse_id', 'left')
                ->group_by("preparation.id");
       
        
        $this->datatables->add_column("Actions", $action, "preparationid, name, date, bname");
        echo $this->datatables->generate();
    }
	
    /* ------------------------------------------------------- */

    function add($id = NULL)
    {
		
        //$this->sma->checkPermissions();
        $this->load->helper('security');
        $warehouses = $this->site->getAllWarehouses();
        
        
        $this->form_validation->set_rules('warehouse_id', lang("warehouse_id"), 'required');
		//$this->form_validation->set_rules('preparation_name', lang("preparation_name"), 'required');
		$this->form_validation->set_rules('preparation_date', lang("preparation_date"), 'required');
		
		
        if ($this->form_validation->run() == true) {
			$date_check = $this->preparation_model->checkDate($this->input->post('preparation_date'));
			if($date_check == TRUE){
				$this->session->set_flashdata('error', lang("preparation_date already exit. please change your date"));
           		admin_redirect('preparation');
			}
			$preparation_array = array(
				'warehouse_id' => $this->input->post('warehouse_id'),
				'preparation_date' => $this->input->post('preparation_date'),
				'created_by' => $this->session->userdata('user_id'),
				'date' => date('Y-m-d H:i:s'),
				'status' => 'Open',
				'reference_no' => date('YmdHis')	
			);
	
			for($j=0; $j<count($this->input->post('product_id[]')); $j++){
				$purchases_item[] = array(
					'recipe_id' => $this->input->post('product_id['.$j.']'),
					'recipe_code' => $this->input->post('product_code['.$j.']'),
					'recipe_name' => $this->input->post('product_name['.$j.']'),
					'stock_quantity' => $this->input->post('given_quantity['.$j.']'),
					//'given_unit' => $this->input->post('given_unit['.$j.']'),
					'warehouse_id' => $this->input->post('warehouse_id'),
				);
			}
           
        }
		
        if ($this->form_validation->run() == true && $this->preparation_model->addpreparation_new($preparation_array, $purchases_item)) 			{
			
            $this->session->set_flashdata('message', lang("preparation_added"));
            admin_redirect('preparation');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['warehouses'] = $warehouses;
			$this->data['units'] = $this->site->getAllUnits();
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('preparation'), 'page' => lang('preparation')), array('link' => '#', 'page' => lang('add_preparation')));
            $meta = array('page_title' => lang('add_preparation'), 'bc' => $bc);
            $this->page_construct('preparation/add', $meta, $this->data);
			
        }
    }
	
	function product_suggestions($term = NULL, $limit = NULL)
    {
        // //$this->sma->checkPermissions('index');
        if ($this->input->get('term')) {
            $term = $this->input->get('term', TRUE);
        }
        $limit = $this->input->get('limit', TRUE);
        $rows['results'] = $this->preparation_model->getpreparationProductSuggestions($term, $limit);
        $this->sma->send_json($rows);
    }
	
	function product_units($term = NULL)
    {
        // //$this->sma->checkPermissions('index');
        if ($this->input->get('term')) {
            $term = $this->input->get('term', TRUE);
        }

        $rows['results'] = $this->preparation_model->getpreparationProductsalesUnits($term);
        $this->sma->send_json($rows);
    }
	
    function suggestions()
    {
        $term = $this->input->get('term', TRUE);
        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('welcome') . "'; }, 10);</script>");
        }

        $rows = $this->preparation_model->getpreparationNames($term);
		$units = $this->site->getAllUnits();
        if ($rows) {
            foreach ($rows as $row) {
                $pr[] = array('id' => $row->id, 'label' => $row->name,  'name' => $row->name, 'code' => $row->code, 'quantity' => $row->quantity, 'sale_unit' => $row->sale_unit, 'given_quantity' => 0, 'given_units' => '',   'units' => $units);
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

        $rows = $this->preparation_model->getpreparationForPrinting($term);
        if ($rows) {
            foreach ($rows as $row) {
                $variants = $this->preparation_model->getpreparationOptions($row->id);
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
            $preparation = $this->input->get('preparation');
            if (!isset($preparation['code']) || empty($preparation['code'])) {
                exit(json_encode(array('msg' => lang('preparation_code_is_required'))));
            }
            if (!isset($preparation['name']) || empty($preparation['name'])) {
                exit(json_encode(array('msg' => lang('preparation_name_is_required'))));
            }
            if (!isset($preparation['category_id']) || empty($preparation['category_id'])) {
                exit(json_encode(array('msg' => lang('preparation_category_is_required'))));
            }
            if (!isset($preparation['unit']) || empty($preparation['unit'])) {
                exit(json_encode(array('msg' => lang('preparation_unit_is_required'))));
            }
            if (!isset($preparation['price']) || empty($preparation['price'])) {
                exit(json_encode(array('msg' => lang('preparation_price_is_required'))));
            }
            if (!isset($preparation['cost']) || empty($preparation['cost'])) {
                exit(json_encode(array('msg' => lang('preparation_cost_is_required'))));
            }
            if ($this->preparation_model->getpreparationByCode($preparation['code'])) {
                exit(json_encode(array('msg' => lang('preparation_code_already_exist'))));
            }
            if ($row = $this->preparation_model->addAjaxpreparation($preparation)) {
                $tax_rate = $this->site->getTaxRateByID($row->tax_rate);
                $pr = array('id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'code' => $row->code, 'qty' => 1, 'cost' => $row->cost, 'name' => $row->name, 'tax_method' => $row->tax_method, 'tax_rate' => $tax_rate, 'discount' => '0');
                $this->sma->send_json(array('msg' => 'success', 'result' => $pr));
            } else {
                exit(json_encode(array('msg' => lang('failed_to_add_preparation'))));
            }
        } else {
            json_encode(array('msg' => 'Invalid token'));
        }

    }


    /* -------------------------------------------------------- */

    function edit($id = NULL)
    {
		$item = array();
		
        //$this->sma->checkPermissions();
        $this->load->helper('security');
        if ($this->input->post('id')) {
            $id = $this->input->post('id');
        }
        $warehouses = $this->site->getAllWarehouses();
		$preparation = $this->preparation_model->getpreparationByID($id);
		$preparation_item = $this->preparation_model->getpreparationItems($id);
		
		
		$units = $this->site->getAllUnits();
        if ($preparation_item) {
            foreach ($preparation_item as $row) {
                $item[$row->recipe_id] = array('id' => $row->recipe_id, 'label' => $row->recipe_name,  'name' => $row->recipe_name, 'code' => $row->recipe_code, 'quantity' => $row->stock_quantity, 'given_quantity' => $row->stock_quantity);
            }
        } 
        
		
        $this->form_validation->set_rules('warehouse_id', lang("warehouse_id"), 'required');
		$this->form_validation->set_rules('preparation_name', lang("preparation_name"), 'required');
		$this->form_validation->set_rules('preparation_date', lang("preparation_date"), 'required');
		
		
        if ($this->form_validation->run() == true) {
			
			$preparation_array = array(
				'warehouse_id' => $this->input->post('warehouse_id'),
				'preparation_date' => $this->input->post('preparation_date'),
				'created_by' => $this->session->userdata('user_id'),
				'date' => date('Y-m-d H:i:s'),
			);
	
			for($j=0; $j<count($this->input->post('product_id[]')); $j++){
				$purchases_item[] = array(
					'recipe_id' => $this->input->post('product_id['.$j.']'),
					'recipe_code' => $this->input->post('product_code['.$j.']'),
					'recipe_name' => $this->input->post('product_name['.$j.']'),
					'stock_quantity' => $this->input->post('given_quantity['.$j.']'),
					'warehouse_id' => $this->input->post('warehouse_id'),
				);
			}
           
        }

		
		
		
        if ($this->form_validation->run() == true && $this->preparation_model->updatepreparation_new($id, $preparation_array, $purchases_item)) {		
			
			
            $this->session->set_flashdata('message', lang("preparation_updated"));
            admin_redirect('preparation');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['units'] = $this->site->getAllUnits();
            $this->data['warehouses'] = $warehouses;
			$this->data['preparation'] = $preparation;
			
			$this->data['preparation_item'] = json_encode($item);
			
			
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('preparation'), 'page' => lang('preparation')), array('link' => '#', 'page' => lang('edit_preparation')));
			
            $meta = array('page_title' => lang('edit_preparation'), 'bc' => $bc);
            $this->page_construct('preparation/edit', $meta, $this->data);
        }
    }
	
	function balance_edit($id = NULL)
    {
		$item = array();
		
        //$this->sma->checkPermissions();
        $this->load->helper('security');
        if ($this->input->post('id')) {
            $id = $this->input->post('id');
        }
        $warehouses = $this->site->getAllWarehouses();
		$preparation = $this->preparation_model->getpreparationByID($id);
		$preparation_item = $this->preparation_model->getpreparationItems($id);
		
		
		$units = $this->site->getAllUnits();
        if ($preparation_item) {
            foreach ($preparation_item as $row) {
                $item[$row->product_id] = array('id' => $row->product_id, 'label' => $row->product_name,  'name' => $row->product_name, 'code' => $row->product_code, 'quantity' => $row->quantity, 'sale_unit' => $row->sale_unit, 'given_quantity' => $row->given_quantity, 'given_units' => $row->given_unit, 'balance_quantity' => $row->balance_quantity, 'balance_units' => $row->balance_unit, 'units' => $units);
            }
        } 
        
		
        $this->form_validation->set_rules('warehouse_id', lang("warehouse_id"), 'required');
		$this->form_validation->set_rules('preparation_name', lang("preparation_name"), 'required');
		$this->form_validation->set_rules('preparation_date', lang("preparation_date"), 'required');
		
		
        if ($this->form_validation->run() == true) {
			
			$preparation_array = array(
				'warehouse_id' => $this->input->post('warehouse_id'),
				'preparation_name' => $this->input->post('preparation_name'),
				'preparation_date' => $this->input->post('preparation_date'),
				'created_by' => $this->session->userdata('user_id'),
				'status' => 'Closed',
				'date' => date('Y-m-d H:i:s'),
			);
	
			for($j=0; $j<count($this->input->post('product_id[]')); $j++){
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
           
        }

		
		
		
        if ($this->form_validation->run() == true && $this->preparation_model->updatepreparation_new($id, $preparation_array, $purchases_item)) {		
			
			
            $this->session->set_flashdata('message', lang("preparation_updated"));
            admin_redirect('preparation/balance');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['units'] = $this->site->getAllUnits();
            $this->data['warehouses'] = $warehouses;
			$this->data['preparation'] = $preparation;
			$this->data['preparation_item'] = json_encode($item);
			
			
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('preparation'), 'page' => lang('preparation')), array('link' => '#', 'page' => lang('edit balance_preparation')));
			
            $meta = array('page_title' => lang('edit_preparation'), 'bc' => $bc);
            $this->page_construct('preparation/balance_edit', $meta, $this->data);
        }
    }


   
    function delete($id = NULL)
    {
        //$this->sma->checkPermissions(NULL, TRUE);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->preparation_model->deletepreparation($id)) {
            if($this->input->is_ajax_request()) {
                $this->sma->send_json(array('error' => 0, 'msg' => lang("preparation_deleted")));
            }
            $this->session->set_flashdata('message', lang('preparation_deleted'));
            admin_redirect('welcome');
        }

    }


    function view($id = NULL)
    {
		
        //$this->sma->checkPermissions('index');

        $pr_details = $this->preparation_model->getpreparationByID($id);
		
        if (!$id || !$pr_details) {
            $this->session->set_flashdata('error', lang('preparation_not_found'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $this->data['barcode'] = "<img src='" . admin_url('preparation/gen_barcode/' . $pr_details->code . '/' . $pr_details->barcode_symbology . '/40/0') . "' alt='" . $pr_details->code . "' class='pull-left' />";
        if ($pr_details->type == 'combo') {
            $this->data['combo_items'] = $this->preparation_model->getpreparationComboItems($id);
        }
		
        $this->data['preparation'] = $pr_details;
		
        $this->data['unit'] = $this->site->getUnitByID($pr_details->unit);
        $this->data['brand'] = $this->site->getBrandByID($pr_details->brand);
        $this->data['images'] = $this->preparation_model->getpreparationPhotos($id);
        $this->data['category'] = $this->site->getpreparationCategoryByID($pr_details->category_id);
        $this->data['subcategory'] = $pr_details->subcategory_id ? $this->site->getpreparationCategoryByID($pr_details->subcategory_id) : NULL;
        $this->data['tax_rate'] = $pr_details->tax_rate ? $this->site->getTaxRateByID($pr_details->tax_rate) : NULL;
        $this->data['popup_attributes'] = $this->popup_attributes;
        $this->data['warehouses'] = $this->preparation_model->getAllWarehousesWithPQ($id);
        $this->data['options'] = $this->preparation_model->getpreparationOptionsWithWH($id);
        $this->data['variants'] = $this->preparation_model->getpreparationOptions($id);
		
		
		
        $this->data['sold'] = $this->preparation_model->getSoldQty($id);
		
        //$this->data['purchased'] = $this->preparation_model->getPurchasedQty($id);

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('preparation'), 'page' => lang('preparation')), array('link' => '#', 'page' => $pr_details->name));
		
        $meta = array('page_title' => $pr_details->name, 'bc' => $bc);
		
        $this->page_construct('preparation/view', $meta, $this->data);
		
    }

    function pdf($id = NULL, $view = NULL)
    {
        //$this->sma->checkPermissions('index');

        $pr_details = $this->preparation_model->getpreparationByID($id);
        if (!$id || !$pr_details) {
            $this->session->set_flashdata('error', lang('prduct_not_found'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $this->data['barcode'] = "<img src='" . admin_url('preparation/gen_barcode/' . $pr_details->code . '/' . $pr_details->barcode_symbology . '/40/0') . "' alt='" . $pr_details->code . "' class='pull-left' />";
        if ($pr_details->type == 'combo') {
            $this->data['combo_items'] = $this->preparation_model->getpreparationComboItems($id);
        }
        $this->data['preparation'] = $pr_details;
        $this->data['unit'] = $this->site->getUnitByID($pr_details->unit);
        $this->data['brand'] = $this->site->getBrandByID($pr_details->brand);
        $this->data['images'] = $this->preparation_model->getpreparationPhotos($id);
        $this->data['category'] = $this->site->getpreparationCategoryByID($pr_details->category_id);
        $this->data['subcategory'] = $pr_details->subcategory_id ? $this->site->getpreparationCategoryByID($pr_details->subcategory_id) : NULL;
        $this->data['tax_rate'] = $pr_details->tax_rate ? $this->site->getTaxRateByID($pr_details->tax_rate) : NULL;
        $this->data['popup_attributes'] = $this->popup_attributes;
        $this->data['warehouses'] = $this->preparation_model->getAllWarehousesWithPQ($id);
        $this->data['options'] = $this->preparation_model->getpreparationOptionsWithWH($id);
        $this->data['variants'] = $this->preparation_model->getpreparationOptions($id);

        $name = $pr_details->code . '_' . str_replace('/', '_', $pr_details->name) . ".pdf";
        if ($view) {
            $this->load->view($this->theme . 'preparation/pdf', $this->data);
        } else {
            $html = $this->load->view($this->theme . 'preparation/pdf', $this->data, TRUE);
            if (! $this->Settings->barcode_img) {
                $html = preg_replace("'\<\?xml(.*)\?\>'", '', $html);
            }
            $this->sma->generate_pdf($html, $name);
        }
    }

  
    function preparation_actions($wh = NULL)
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
                    $this->session->set_flashdata('message', $this->lang->line("preparation_quantity_sync"));
                    redirect($_SERVER["HTTP_REFERER"]);

                } elseif ($this->input->post('form_action') == 'delete') {

                    //$this->sma->checkPermissions('delete');
                    foreach ($_POST['val'] as $id) {
                        $this->preparation_model->deletepreparation($id);
                    }
                    $this->session->set_flashdata('message', $this->lang->line("preparation_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);

                } elseif ($this->input->post('form_action') == 'labels') {

                    foreach ($_POST['val'] as $id) {
                        $row = $this->preparation_model->getpreparationByID($id);
                        $selected_variants = false;
                        if ($variants = $this->preparation_model->getpreparationOptions($row->id)) {
                            foreach ($variants as $variant) {
                                $selected_variants[$variant->id] = $variant->quantity > 0 ? 1 : 0;
                            }
                        }
                        $pr[$row->id] = array('id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => $row->quantity, 'variants' => $variants, 'selected_variants' => $selected_variants);
                    }

                    $this->data['items'] = isset($pr) ? json_encode($pr) : false;
                    $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
                    $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('preparation'), 'page' => lang('preparation')), array('link' => '#', 'page' => lang('print_barcodes')));
                    $meta = array('page_title' => lang('print_barcodes'), 'bc' => $bc);
                    $this->page_construct('preparation/print_barcodes', $meta, $this->data);

                } elseif ($this->input->post('form_action') == 'export_excel') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle('preparation');
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
                    $this->excel->getActiveSheet()->SetCellValue('P1', lang('preparation_variants'));
                    $this->excel->getActiveSheet()->SetCellValue('Q1', lang('pcf1'));
                    $this->excel->getActiveSheet()->SetCellValue('R1', lang('pcf2'));
                    $this->excel->getActiveSheet()->SetCellValue('S1', lang('pcf3'));
                    $this->excel->getActiveSheet()->SetCellValue('T1', lang('pcf4'));
                    $this->excel->getActiveSheet()->SetCellValue('U1', lang('pcf5'));
                    $this->excel->getActiveSheet()->SetCellValue('V1', lang('pcf6'));
                    $this->excel->getActiveSheet()->SetCellValue('W1', lang('quantity'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $preparation = $this->preparation_model->getpreparationDetail($id);
                        $brand = $this->site->getBrandByID($preparation->brand);
                        $base_unit = $sale_unit = $purchase_unit = '';
                        if($units = $this->site->getUnitsByBUID($preparation->unit)) {
                            foreach($units as $u) {
                                if ($u->id == $preparation->unit) {
                                    $base_unit = $u->code;
                                }
                                if ($u->id == $preparation->sale_unit) {
                                    $sale_unit = $u->code;
                                }
                                if ($u->id == $preparation->purchase_unit) {
                                    $purchase_unit = $u->code;
                                }
                            }
                        }
                        $variants = $this->preparation_model->getpreparationOptions($id);
                        $preparation_variants = '';
                        if ($variants) {
                            foreach ($variants as $variant) {
                                $preparation_variants .= trim($variant->name) . '|';
                            }
                        }
                        $quantity = $preparation->quantity;
                        if ($wh) {
                            if($wh_qty = $this->preparation_model->getpreparationQuantity($id, $wh)) {
                                $quantity = $wh_qty['quantity'];
                            } else {
                                $quantity = 0;
                            }
                        }
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $preparation->name);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $preparation->code);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $preparation->barcode_symbology);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, ($brand ? $brand->name : ''));
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $preparation->category_code);
                        $this->excel->getActiveSheet()->SetCellValue('F' . $row, $base_unit);
                        $this->excel->getActiveSheet()->SetCellValue('G' . $row, $sale_unit);
                        $this->excel->getActiveSheet()->SetCellValue('H' . $row, $purchase_unit);
                        if ($this->Owner || $this->Admin || $this->session->userdata('show_cost')) {
                            $this->excel->getActiveSheet()->SetCellValue('I' . $row, $preparation->cost);
                        }
                        if ($this->Owner || $this->Admin || $this->session->userdata('show_price')) {
                            $this->excel->getActiveSheet()->SetCellValue('J' . $row, $preparation->price);
                        }
                        $this->excel->getActiveSheet()->SetCellValue('K' . $row, $preparation->alert_quantity);
                        $this->excel->getActiveSheet()->SetCellValue('L' . $row, $preparation->tax_rate_name);
                        $this->excel->getActiveSheet()->SetCellValue('M' . $row, $preparation->tax_method ? lang('exclusive') : lang('inclusive'));
                        $this->excel->getActiveSheet()->SetCellValue('N' . $row, $preparation->image);
                        $this->excel->getActiveSheet()->SetCellValue('O' . $row, $preparation->subcategory_code);
                        $this->excel->getActiveSheet()->SetCellValue('P' . $row, $preparation_variants);
                        $this->excel->getActiveSheet()->SetCellValue('Q' . $row, $preparation->cf1);
                        $this->excel->getActiveSheet()->SetCellValue('R' . $row, $preparation->cf2);
                        $this->excel->getActiveSheet()->SetCellValue('S' . $row, $preparation->cf3);
                        $this->excel->getActiveSheet()->SetCellValue('T' . $row, $preparation->cf4);
                        $this->excel->getActiveSheet()->SetCellValue('U' . $row, $preparation->cf5);
                        $this->excel->getActiveSheet()->SetCellValue('V' . $row, $preparation->cf6);
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
                    $filename = 'preparation_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
            } else {
                $this->session->set_flashdata('error', $this->lang->line("no_preparation_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'admin/preparation');
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
