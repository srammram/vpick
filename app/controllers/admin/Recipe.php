<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Recipe extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        $this->lang->admin_load('recipe', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('recipe_model');
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('recipe')));
        $meta = array('page_title' => lang('recipe'), 'bc' => $bc);
        $this->page_construct('recipe/index', $meta, $this->data);
    }

    function getrecipe($warehouse_id = NULL)
    {
        $this->sma->checkPermissions('index', TRUE);
        //$supplier = $this->input->get('supplier') ? $this->input->get('supplier') : NULL;

        if ((! $this->Owner || ! $this->Admin)) {
            $user = $this->site->getUser();
        }
        $detail_link = anchor('admin/recipe/view/$1', '<i class="fa fa-file-text-o"></i> ' . lang('recipe_details'));
        $delete_link = "<a href='#' class='tip po' title='<b>" . $this->lang->line("delete_recipe") . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete1' id='a__$1' href='" . admin_url('recipe/delete/$1') . "'>"
            . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
            . lang('delete_recipe') . "</a>";
        $single_barcode = anchor('admin/recipe/print_barcodes/$1', '<i class="fa fa-print"></i> ' . lang('print_barcode_label'));
        // $single_label = anchor_popup('recipe/single_label/$1/' . ($warehouse_id ? $warehouse_id : ''), '<i class="fa fa-print"></i> ' . lang('print_label'), $this->popup_attributes);
        $action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
            . lang('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
            <li>' . $detail_link . '</li>
           
            <li><a href="' . admin_url('recipe/edit/$1') . '"><i class="fa fa-edit"></i> ' . lang('edit_recipe') . '</a></li>';
        if ($warehouse_id) {
            $action .= '<li><a href="' . admin_url('recipe/set_rack/$1/' . $warehouse_id) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-bars"></i> '
                . lang('set_rack') . '</a></li>';
        }
        $action .= '<li><a href="' . base_url() . 'assets/uploads/$2" data-type="image" data-toggle="lightbox"><i class="fa fa-file-photo-o"></i> '
            . lang('view_image') . '</a></li>
            
            <li class="divider"></li>
            <li>' . $delete_link . '</li>
            </ul>
        </div></div>';
        $this->load->library('datatables');
		if ($warehouse_id) {            
//            $this->datatables
//            ->select("'sno',".$this->db->dbprefix('recipe') . ".id as recipeid,{$this->db->dbprefix('recipe')}.name as name,{$this->db->dbprefix('recipe')}.khmer_name as khmer_name,{$this->db->dbprefix('recipe_categories')}.name as cname,{$this->db->dbprefix('recipe')}.image as image, price as price, CASE WHEN dbprefix('recipe')}.recipe_standard = 1 THEN 'Alakat'  WHEN dbprefix('recipe')}.recipe_standard = 2 THEN 'BBQ'  ELSE 'Alakat & BBQ' END  as recipe_standard, active", FALSE)
//            ->from('recipe');
//	    $this->datatables->join('warehouses_recipe', 'recipe.id=warehouses_recipe.recipe_id', 'left');
//	    $this->datatables->join('warehouses', 'warehouses.id=warehouses_recipe.warehouse_id');
//            if ($this->Settings->display_all_products) {
//		
//                $this->datatables->join("( SELECT recipeid, quantity, rack from {$this->db->dbprefix('warehouses_recipe')} WHERE warehouse_id = {$warehouse_id}) wp", 'recipe.id=wp.recipe_id', 'left');
//            } else {
//                //$this->datatables->join('warehouses_recipe wp', 'recipe.id=wp.recipe_id', 'left')
//                $this->datatables->where('warehouses_recipe.warehouse_id', $warehouse_id);
//                //->where('wp.quantity !=', 0);
//            }
//            $this->datatables->join('recipe_categories', 'recipe.category_id=recipe_categories.id', 'left')
//            //->join('units', 'products.unit=units.id', 'left')
//            //->join('brands', 'products.brand=brands.id', 'left')
//            ->group_by("recipe.id")
//                ->order_by('recipe.id desc')
//            ->edit_column('active', '$1__$2', 'active, recipeid')
//            ->edit_column('image', '$1__$2__$3', 'active, recipeid,image');
//            // ->group_by("products.id");
	    $this->datatables
                ->select("'sno',".$this->db->dbprefix('recipe') . ".id as recipeid,,".$this->db->dbprefix('recipe') . ".type as item_type (CASE WHEN {$this->db->dbprefix('recipe')}.recipe_standard = '1' THEN  'Alakat' WHEN {$this->db->dbprefix('recipe')}.recipe_standard = '2' THEN  'BBQ' ELSE 'Alakat and BBQ' END) as recipe_standard, {$this->db->dbprefix('recipe')}.name as name,{$this->db->dbprefix('recipe')}.khmer_name as khmer_name,{$this->db->dbprefix('recipe_categories')}.name as cname,{$this->db->dbprefix('recipe')}.image as image,".$this->db->dbprefix('recipe') . ".id as stock_r_id, price as price,  active", FALSE)
                ->from('recipe')
                ->join('recipe_categories', 'recipe.category_id=recipe_categories.id', 'left')
		->join('warehouses_recipe', 'recipe.id=warehouses_recipe.recipe_id', 'left')
		->join('warehouses', 'warehouses_recipe.warehouse_id=warehouses.id')
		->where('srampos_warehouses_recipe.warehouse_id',$warehouse_id)
                ->group_by("recipe.id")
                ->order_by('recipe.id desc')
                ->edit_column('active', '$1__$2', 'active, recipeid')
                ->edit_column('image', '$1__$2__$3', 'active, recipeid,image');
        } else {
        $this->datatables
                ->select("'sno',".$this->db->dbprefix('recipe') . ".id as recipeid,".$this->db->dbprefix('recipe') . ".type as item_type, (CASE WHEN {$this->db->dbprefix('recipe')}.recipe_standard = '1' THEN  'Alakat' WHEN {$this->db->dbprefix('recipe')}.recipe_standard = '2' THEN  'BBQ' ELSE 'Alakat and BBQ' END) as recipe_standard, {$this->db->dbprefix('recipe')}.name as name,{$this->db->dbprefix('recipe')}.khmer_name as khmer_name,{$this->db->dbprefix('recipe_categories')}.name as cname,{$this->db->dbprefix('recipe')}.image as image,".$this->db->dbprefix('recipe') . ".id as stock_r_id, price as price,  active", FALSE)
                ->from('recipe')
                ->join('recipe_categories', 'recipe.category_id=recipe_categories.id', 'left')
		->join('warehouses_recipe', 'recipe.id=warehouses_recipe.recipe_id', 'left')
		->join('warehouses', 'warehouses_recipe.warehouse_id=warehouses.id')
                ->group_by("recipe.id")
                ->order_by('recipe.id desc')
                ->edit_column('active', '$1__$2', 'active, recipeid')
                ->edit_column('image', '$1__$2__$3', 'active, recipeid,image');
        }
        
        $this->datatables->add_column("Actions", $action, "recipeid, image, name");
		
        echo $this->datatables->generate();
    }

    function set_rack($recipe_id = NULL, $warehouse_id = NULL)
    {
        $this->sma->checkPermissions('edit', true);

        $this->form_validation->set_rules('rack', lang("rack_location"), 'trim|required');

        if ($this->form_validation->run() == true) {
            $data = array('rack' => $this->input->post('rack'),
                'recipe_id' => $recipe_id,
                'warehouse_id' => $warehouse_id,
            );
        } elseif ($this->input->post('set_rack')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("recipe");
        }

        if ($this->form_validation->run() == true && $this->recipe_model->setRack($data)) {
            $this->session->set_flashdata('message', lang("rack_set"));
            admin_redirect("recipe/" . $warehouse_id);
        } else {
            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['warehouse_id'] = $warehouse_id;
            $this->data['recipe'] = $this->site->getrecipeByID($recipe_id);
            $wh_pr = $this->recipe_model->getrecipeQuantity($recipe_id, $warehouse_id);
            $this->data['rack'] = $wh_pr['rack'];
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'recipe/set_rack', $this->data);

        }
    }

    function barcode($recipe_code = NULL, $bcs = 'code128', $height = 40)
    {
        if ($this->Settings->barcode_img) {
            header('Content-Type: image/png');
        } else {
            header('Content-type: image/svg+xml');
        }
        echo $this->sma->barcode($recipe_code, $bcs, $height, true, false, true);
    }

    function print_barcodes($recipe_id = NULL)
    {
        $this->sma->checkPermissions('barcode', true);

        $this->form_validation->set_rules('style', lang("style"), 'required');

        if ($this->form_validation->run() == true) {

            $style = $this->input->post('style');
            $bci_size = ($style == 10 || $style == 12 ? 50 : ($style == 14 || $style == 18 ? 30 : 20));
            $currencies = $this->site->getAllCurrencies();
            $s = isset($_POST['recipe']) ? sizeof($_POST['recipe']) : 0;
            if ($s < 1) {
                $this->session->set_flashdata('error', lang('no_recipe_selected'));
                admin_redirect("recipe/print_barcodes");
            }
            for ($m = 0; $m < $s; $m++) {
                $pid = $_POST['recipe'][$m];
                $quantity = $_POST['quantity'][$m];
                $recipe = $this->recipe_model->getrecipeWithCategory($pid);
                $recipe->price = $this->input->post('check_promo') ? ($recipe->promotion ? $recipe->promo_price : $recipe->price) : $recipe->price;
                if ($variants = $this->recipe_model->getrecipeOptions($pid)) {
                    foreach ($variants as $option) {
                        if ($this->input->post('vt_'.$recipe->id.'_'.$option->id)) {
                            $barcodes[] = array(
                                'site' => $this->input->post('site_name') ? $this->Settings->site_name : FALSE,
                                'name' => $this->input->post('recipe_name') ? $recipe->name.' - '.$option->name : FALSE,
                                'image' => $this->input->post('recipe_image') ? $recipe->image : FALSE,
                                'barcode' => $recipe->code . $this->Settings->barcode_separator . $option->id,
                                'bcs' => 'code128',
                                'bcis' => $bci_size,
                                // 'barcode' => $this->recipe_barcode($recipe->code . $this->Settings->barcode_separator . $option->id, 'code128', $bci_size),
                                'price' => $this->input->post('price') ?  $this->sma->formatMoney($option->price != 0 ? ($recipe->price+$option->price) : $recipe->price) : FALSE,
                                'unit' => $this->input->post('unit') ? $recipe->unit : FALSE,
                                'category' => $this->input->post('category') ? $recipe->category : FALSE,
                                'currencies' => $this->input->post('currencies'),
                                'variants' => $this->input->post('variants') ? $variants : FALSE,
                                'quantity' => $quantity
                                );
                        }
                    }
                } else {
                    $barcodes[] = array(
                        'site' => $this->input->post('site_name') ? $this->Settings->site_name : FALSE,
                        'name' => $this->input->post('recipe_name') ? $recipe->name : FALSE,
                        'image' => $this->input->post('recipe_image') ? $recipe->image : FALSE,
                        // 'barcode' => $this->recipe_barcode($recipe->code, $recipe->barcode_symbology, $bci_size),
                        'barcode' => $recipe->code,
                        'bcs' => $recipe->barcode_symbology,
                        'bcis' => $bci_size,
                        'price' => $this->input->post('price') ?  $this->sma->formatMoney($recipe->price) : FALSE,
                        'unit' => $this->input->post('unit') ? $recipe->unit : FALSE,
                        'category' => $this->input->post('category') ? $recipe->category : FALSE,
                        'currencies' => $this->input->post('currencies'),
                        'variants' => FALSE,
                        'quantity' => $quantity
                        );
                }

            }
            $this->data['barcodes'] = $barcodes;
            $this->data['currencies'] = $currencies;
            $this->data['style'] = $style;
            $this->data['items'] = false;
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('recipe'), 'page' => lang('recipe')), array('link' => '#', 'page' => lang('print_barcodes')));
            $meta = array('page_title' => lang('print_barcodes'), 'bc' => $bc);
            $this->page_construct('recipe/print_barcodes', $meta, $this->data);

        } else {

            if ($this->input->get('purchase') || $this->input->get('transfer')) {
                if ($this->input->get('purchase')) {
                    $purchase_id = $this->input->get('purchase', TRUE);
                    $items = $this->recipe_model->getPurchaseItems($purchase_id);
                } elseif ($this->input->get('transfer')) {
                    $transfer_id = $this->input->get('transfer', TRUE);
                    $items = $this->recipe_model->getTransferItems($transfer_id);
                }
                if ($items) {
                    foreach ($items as $item) {
                        if ($row = $this->recipe_model->getrecipeByID($item->recipe_id)) {
                            $selected_variants = false;
                            if ($variants = $this->recipe_model->getrecipeOptions($row->id)) {
                                foreach ($variants as $variant) {
                                    $selected_variants[$variant->id] = isset($pr[$row->id]['selected_variants'][$variant->id]) && !empty($pr[$row->id]['selected_variants'][$variant->id]) ? 1 : ($variant->id == $item->option_id ? 1 : 0);
                                }
                            }
                            $pr[$row->id] = array('id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => $item->quantity, 'variants' => $variants, 'selected_variants' => $selected_variants);
                        }
                    }
                    $this->data['message'] = lang('recipe_added_to_list');
                }
            }

            if ($recipe_id) {
                if ($row = $this->site->getrecipeByID($recipe_id)) {

                    $selected_variants = false;
                    if ($variants = $this->recipe_model->getrecipeOptions($row->id)) {
                        foreach ($variants as $variant) {
                            $selected_variants[$variant->id] = $variant->quantity > 0 ? 1 : 0;
                        }
                    }
                    $pr[$row->id] = array('id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => $row->quantity, 'variants' => $variants, 'selected_variants' => $selected_variants);

                    $this->data['message'] = lang('recipe_added_to_list');
                }
            }

            if ($this->input->get('category')) {
                if ($recipe = $this->recipe_model->getCategoryrecipe($this->input->get('category'))) {
                    foreach ($recipe as $row) {
                        $selected_variants = false;
                        if ($variants = $this->recipe_model->getrecipeOptions($row->id)) {
                            foreach ($variants as $variant) {
                                $selected_variants[$variant->id] = $variant->quantity > 0 ? 1 : 0;
                            }
                        }
                        $pr[$row->id] = array('id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => $row->quantity, 'variants' => $variants, 'selected_variants' => $selected_variants);
                    }
                    $this->data['message'] = lang('recipe_added_to_list');
                } else {
                    $pr = array();
                    $this->session->set_flashdata('error', lang('no_recipe_found'));
                }
            }

            if ($this->input->get('subcategory')) {
                if ($recipe = $this->recipe_model->getSubCategoryrecipe($this->input->get('subcategory'))) {
                    foreach ($recipe as $row) {
                        $selected_variants = false;
                        if ($variants = $this->recipe_model->getrecipeOptions($row->id)) {
                            foreach ($variants as $variant) {
                                $selected_variants[$variant->id] = $variant->quantity > 0 ? 1 : 0;
                            }
                        }
                        $pr[$row->id] = array('id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => $row->quantity, 'variants' => $variants, 'selected_variants' => $selected_variants);
                    }
                    $this->data['message'] = lang('recipe_added_to_list');
                } else {
                    $pr = array();
                    $this->session->set_flashdata('error', lang('no_recipe_found'));
                }
            }

            $this->data['items'] = isset($pr) ? json_encode($pr) : false;
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('recipe'), 'page' => lang('recipe')), array('link' => '#', 'page' => lang('print_barcodes')));
            $meta = array('page_title' => lang('print_barcodes'), 'bc' => $bc);
            $this->page_construct('recipe/print_barcodes', $meta, $this->data);

        }
    }


    /* ------------------------------------------------------- */

    function add($id = NULL)
    {
		
        $this->sma->checkPermissions();
        $this->load->helper('security');
        $warehouses = $this->site->getAllWarehouses();
        $this->form_validation->set_rules('category', lang("category"), 'required|is_natural_no_zero');
        $warehouse = $this->input->post('warehouse');
        $recipe_standard = ($this->input->post('recipe_standard'))?$this->input->post('recipe_standard'):array();
	
        if(!empty($warehouse))
        {            
            foreach($warehouse as $id => $ware)
            {
                $this->form_validation->set_rules('warehouse[' . $id . ']', 'Warehouse', 'required');                
            }
        }
        else{
            $this->form_validation->set_rules('warehouse', 'Warehouse', 'required');  
        }
        
        if(in_array(2,$recipe_standard))
        {     
            $this->form_validation->set_rules('piece', lang("piece"), 'required');            
        } 
       
		$this->form_validation->set_rules('code', lang("recipe_code"), 'is_unique[recipe.code]');
		$this->form_validation->set_rules('name', lang("recipe_name"), 'is_unique[recipe.name]');        
        /*$this->form_validation->set_rules('slug', lang("slug"), 'required|is_unique[recipe.slug]|alpha_dash');*/
        $this->form_validation->set_rules('recipe_image', lang("recipe_image"), 'xss_clean');
        $this->form_validation->set_rules('digital_file', lang("digital_file"), 'xss_clean');
        $this->form_validation->set_rules('userfile', lang("recipe_gallery_images"), 'xss_clean');        
		//$this->form_validation->set_rules('stock_quantity', lang("stock_quantity"), 'required');		
        if ($this->form_validation->run() == true) {
            /*echo "<pre>";
print_r($_POST);exit;*/

//	    $recipe_pro =array();
//	    for($j=0; $j<count($this->input->post('purchase_item[id]')); $j++){
//					$recipe_pro[] = array(
//						'product_id' => $this->input->post('purchase_item[id]['.$j.']'),
//						'quantity' => $this->input->post('purchase_item[quantity]['.$j.']'),
//						'unit_id' => $this->input->post('purchase_item[unit_id]['.$j.']'),						
//					);
//				}
//				
            //echo '<pre>';print_R($recipe_pro);print_R($_POST);exit;
            $tax_rate = $this->input->post('tax_rate') ? $this->site->getTaxRateByID($this->input->post('tax_rate')) : NULL;
            $data = array(
				'code' => $this->input->post('code'),
				'recipe_standard' => implode(',',$recipe_standard),
				'piece' => $this->input->post('piece'),
				'khmer_name' => $this->input->post('khmer_name'),
				'khmer_image' => str_replace(' ', '-',$this->input->post('name')).'.png',
                'barcode_symbology' => $this->input->post('barcode_symbology') ? $this->input->post('barcode_symbology') : 'code25',
                'name' => $this->input->post('name'),
				'currency_type' => $this->input->post('currency_type'),
				'kitchens_id' => $this->input->post('kitchens_id') ? $this->input->post('kitchens_id') : 0,
                'type' => $this->input->post('type'),
				'stock_quantity' => $this->input->post('stock_quantity') ? $this->input->post('stock_quantity') : 0,
				
                'category_id' => $this->input->post('category'),
                'subcategory_id' => $this->input->post('subcategory') ? $this->input->post('subcategory') : NULL,
                'tax_rate' => $this->input->post('tax_rate'),
                'tax_method' => $this->input->post('tax_method'),
                'tax_amount' => $this->input->post('tax_amount'),
                'track_quantity' => $this->input->post('track_quantity') ? $this->input->post('track_quantity') : '0',
                'details' => $this->input->post('details'),
                'recipe_details' => $this->input->post('recipe_details'),
                'suppliers'=>implode(',',$this->input->post('supplier')) ? implode(',',$this->input->post('supplier')) : '',
		//'supplier1' => $this->input->post('supplier'),
                'supplier1price' => $this->sma->formatDecimal($this->input->post('supplier_price')),
                'supplier2' => $this->input->post('supplier_2'),
                'supplier2price' => $this->sma->formatDecimal($this->input->post('supplier_2_price')),
                'supplier3' => $this->input->post('supplier_3'),
                'supplier3price' => $this->sma->formatDecimal($this->input->post('supplier_3_price')),
                'supplier4' => $this->input->post('supplier_4'),
                'supplier4price' => $this->sma->formatDecimal($this->input->post('supplier_4_price')),
                'supplier5' => $this->input->post('supplier_5'),
                'supplier5price' => $this->sma->formatDecimal($this->input->post('supplier_5_price')),
                'cf1' => $this->input->post('cf1'),
                'cf2' => $this->input->post('cf2'),
                'cf3' => $this->input->post('cf3'),
                'cf4' => $this->input->post('cf4'),
                'cf5' => $this->input->post('cf5'),
                'cf6' => $this->input->post('cf6'),
                'manufacturem_date' => '',
                'expriy_date' =>  '',
                'supplier1_part_no' => $this->input->post('supplier_part_no'),
                'supplier2_part_no' => $this->input->post('supplier_2_part_no'),
                'supplier3_part_no' => $this->input->post('supplier_3_part_no'),
                'supplier4_part_no' => $this->input->post('supplier_4_part_no'),
                'supplier5_part_no' => $this->input->post('supplier_5_part_no'),
                'file' => $this->input->post('file_link'),
                'slug' => $this->input->post('slug'),
				'cost' => $this->input->post('cost'),
				'price' => $this->input->post('cost'),//$this->input->post('price'),
                'featured' => $this->input->post('featured'),
                'hsn_code' => $this->input->post('hsn_code'),
				'active' => 1,
                'hide' => $this->input->post('hide') ? $this->input->post('hide') : 0,
		'preparation_time' => $this->input->post('preparation_time'),
		
		'minimum_quantity' => $this->input->post('minimum_quantity') ? $this->input->post('minimum_quantity') : 0,
		'reorder_quantity' => $this->input->post('reorder_quantity') ? $this->input->post('reorder_quantity') : 0,
		'type_expiry' => $this->input->post('type_expiry') ? $this->input->post('type_expiry') : '',
		'value_expiry' => $this->input->post('value_expiry') ? $this->input->post('value_expiry') : 0,
		'unit' => $this->input->post('unit'),
		'sale_unit' => $this->input->post('default_sale_unit'),
                'purchase_unit' => $this->input->post('default_purchase_unit'),
		'purchase_cost' => $this->input->post('purchase_cost'),
		'maximum_quantity' => $this->input->post('maximum_quantity'),
		'batch_required' => $this->input->post('batch_required'),		
		'expiry_date_required' => $this->input->post('expiry_date_required'),
		'purchase_tax' => $this->input->post('purchase_tax'),
		'brand' => $this->input->post('brand'),
		
            );
			
		//print_r($data);	exit;
			
            $warehouse_qty = NULL;
			$recipe_pro = NULL;
            $this->load->library('upload');
			for($i=0; $i<count($this->input->post('warehouse[]')); $i++){
					$warehouse_qty[] = array(
						'warehouse_id' => $this->input->post('warehouse['.$i.']'),
					);
				}
			
			if ($this->input->post('type') != 'addon') {
				if(array_filter($this->input->post('recipe_addon[]'))){
					for($j=0; $j<count($this->input->post('recipe_addon[]')); $j++){
						$recipe_aon[] = array(
							'recipe_addon' => $this->input->post('recipe_addon['.$j.']'),
						);
					}
				}
			}
			
			
            if ($this->input->post('type') == 'addon' || $this->input->post('type') == 'production' || $this->input->post('type') == 'semi_finished') {
                
				
				
				for($j=0; $j<count($this->input->post('purchase_item[id]')); $j++){
					$recipe_pro[] = array(
						'product_id' => $this->input->post('purchase_item[id]['.$j.']'),
						'quantity' => $this->input->post('purchase_item[quantity]['.$j.']'),
						'unit_id' => $this->input->post('purchase_item[unit_id]['.$j.']'),						
					);
				}
				
              
            }
	    if($this->input->post('type') == 'combo'){
				
		    for($k=0; $k<count($this->input->post('combo_item_id[]')); $k++){
			    $items[] = array(
				    'item_id' => $this->input->post('combo_item_id['.$k.']'),
				    'item_code' => $this->input->post('combo_item_code['.$k.']'),
				    'unit_price' => $this->input->post('combo_item_price['.$k.']'),
				    'quantity' => $this->input->post('combo_item_quantity['.$k.']'),
			    );
		    }
	    }
		
            if ($_FILES['recipe_image']['size'] > 0) {

                $config['upload_path'] = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['max_width'] = $this->Settings->iwidth;
                $config['max_height'] = $this->Settings->iheight;
                $config['overwrite'] = FALSE;
                $config['max_filename'] = 25;
                $config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('recipe_image')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    admin_redirect("recipe/add");
                }
                $photo = $this->upload->file_name;
                $data['image'] = $photo;
                $this->load->library('image_lib');
                $config['image_library'] = 'gd2';
                $config['source_image'] = $this->upload_path . $photo;
                $config['new_image'] = $this->thumbs_path . $photo;
                $config['maintain_ratio'] = TRUE;
                $config['width'] = $this->Settings->twidth;
                $config['height'] = $this->Settings->theight;
                $this->image_lib->clear();
                $this->image_lib->initialize($config);
                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
                if ($this->Settings->watermark) {
                    $this->image_lib->clear();
                    $wm['source_image'] = $this->upload_path . $photo;
                    $wm['wm_text'] = 'Copyright ' . date('Y') . ' - ' . $this->Settings->site_name;
                    $wm['wm_type'] = 'text';
                    $wm['wm_font_path'] = 'system/fonts/texb.ttf';
                    $wm['quality'] = '100';
                    $wm['wm_font_size'] = '16';
                    $wm['wm_font_color'] = '999999';
                    $wm['wm_shadow_color'] = 'CCCCCC';
                    $wm['wm_vrt_alignment'] = 'top';
                    $wm['wm_hor_alignment'] = 'left';
                    $wm['wm_padding'] = '10';
                    $this->image_lib->initialize($wm);
                    $this->image_lib->watermark();
                }
                $this->image_lib->clear();
                $config = NULL;
            }

            if ($_FILES['userfile']['name'][0] != "") {

                $config['upload_path'] = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['max_width'] = $this->Settings->iwidth;
                $config['max_height'] = $this->Settings->iheight;
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $config['max_filename'] = 25;
                $files = $_FILES;
                $cpt = count($_FILES['userfile']['name']);
                for ($i = 0; $i < $cpt; $i++) {

                    $_FILES['userfile']['name'] = $files['userfile']['name'][$i];
                    $_FILES['userfile']['type'] = $files['userfile']['type'][$i];
                    $_FILES['userfile']['tmp_name'] = $files['userfile']['tmp_name'][$i];
                    $_FILES['userfile']['error'] = $files['userfile']['error'][$i];
                    $_FILES['userfile']['size'] = $files['userfile']['size'][$i];

                    $this->upload->initialize($config);

                    if (!$this->upload->do_upload()) {
                        $error = $this->upload->display_errors();
                        $this->session->set_flashdata('error', $error);
                        admin_redirect("recipe/add");
                    } else {

                        $pho = $this->upload->file_name;

                        $photos[] = $pho;

                        $this->load->library('image_lib');
                        $config['image_library'] = 'gd2';
                        $config['source_image'] = $this->upload_path . $pho;
                        $config['new_image'] = $this->thumbs_path . $pho;
                        $config['maintain_ratio'] = TRUE;
                        $config['width'] = $this->Settings->twidth;
                        $config['height'] = $this->Settings->theight;

                        $this->image_lib->initialize($config);

                        if (!$this->image_lib->resize()) {
                            echo $this->image_lib->display_errors();
                        }

                        if ($this->Settings->watermark) {
                            $this->image_lib->clear();
                            $wm['source_image'] = $this->upload_path . $pho;
                            $wm['wm_text'] = 'Copyright ' . date('Y') . ' - ' . $this->Settings->site_name;
                            $wm['wm_type'] = 'text';
                            $wm['wm_font_path'] = 'system/fonts/texb.ttf';
                            $wm['quality'] = '100';
                            $wm['wm_font_size'] = '16';
                            $wm['wm_font_color'] = '999999';
                            $wm['wm_shadow_color'] = 'CCCCCC';
                            $wm['wm_vrt_alignment'] = 'top';
                            $wm['wm_hor_alignment'] = 'left';
                            $wm['wm_padding'] = '10';
                            $this->image_lib->initialize($wm);
                            $this->image_lib->watermark();
                        }

                        $this->image_lib->clear();
                    }
                }
                $config = NULL;
            } else {
                $photos = NULL;
            }
           $filename = 'assets/language/'.str_replace(' ', '-',$_POST['name']).'.png';
	    $this->base64ToImage($_POST['recipe_name_img'],$filename);
        }else{
	    $this->data['sub_cate'] = $this->recipe_model->getrecipeSubCategories($_POST['category']);
	    $this->data['sub_units'] = $this->site->getUnitsByBUID($_POST['unit']);
	}
		
		
	
        if ($this->form_validation->run() == true && $this->recipe_model->addrecipe_new($data, $warehouse_qty, $recipe_pro, $recipe_aon, $items, $photos)) 			{
			
			
            $this->session->set_flashdata('message', lang("recipe_added"));
            admin_redirect('recipe');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['categories'] = $this->site->getAllrecipeCategories();
            $this->data['tax_rates'] = $this->site->getAllTaxRates();
            $this->data['brands'] = $this->site->getAllBrands();
            $this->data['base_units'] = $this->site->getAllBaseUnits();
			$this->data['units'] = $this->site->getAllUnits();
            $this->data['warehouses'] = $warehouses;
            $this->data['warehouses_recipe'] = $id ? $this->recipe_model->getAllWarehousesWithPQ($id) : NULL;
            $this->data['recipe'] = $id ? $this->recipe_model->getrecipeByID($id) : NULL;
            $this->data['variants'] = $this->recipe_model->getAllVariants();
			$this->data['reskitchen'] = $this->site->getAllResKitchen();
			$this->data['rescurrency'] = $this->site->getAllCurrencies();
            $this->data['combo_items'] = ($id && $this->data['recipe']->type == 'combo') ? $this->recipe_model->getrecipeComboItems($id) : NULL;
            $this->data['recipe_options'] = $id ? $this->recipe_model->getrecipeOptionsWithWH($id) : NULL;
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('recipe'), 'page' => lang('recipe')), array('link' => '#', 'page' => lang('add_recipe')));
            $meta = array('page_title' => lang('add_recipe'), 'bc' => $bc);
	    $this->data['suppliers'] = $this->site->getAllSuppliers();
	    $this->data['tax_rates'] = $this->site->getAllTaxRates();
            $this->page_construct('recipe/add', $meta, $this->data);
			
        }
    }
	
	function product_suggestions($term = NULL, $limit = NULL)
    {
        // $this->sma->checkPermissions('index');
        if ($this->input->get('term')) {
            $term = $this->input->get('term', TRUE);
        }
        $limit = $this->input->get('limit', TRUE);
        $rows['results'] = $this->recipe_model->getrecipeProductSuggestions($term, $limit);
        $this->sma->send_json($rows);
    }
	
	function addon_suggestions($term = NULL, $limit = NULL)
    {
        // $this->sma->checkPermissions('index');
        if ($this->input->get('term')) {
            $term = $this->input->get('term', TRUE);
        }
        $limit = $this->input->get('limit', TRUE);
        $rows['results'] = $this->recipe_model->getrecipeAddonSuggestions($term, $limit);
        $this->sma->send_json($rows);
    }
	
	function product_units($term = NULL)
    {
        // $this->sma->checkPermissions('index');
        if ($this->input->get('term')) {
            $term = $this->input->get('term', TRUE);
        }

        $rows['results'] = $this->recipe_model->getrecipeProductsalesUnits($term);
        $this->sma->send_json($rows);
    }
	
    function suggestions()
    {
        $term = $this->input->get('term', TRUE);
        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('welcome') . "'; }, 10);</script>");
        }

        $rows = $this->recipe_model->getrecipeNames($term);
        if ($rows) {
            foreach ($rows as $row) {
                $pr[] = array('id' => $row->id, 'label' => $row->name,  'name' => $row->name, 'qty' => 1, 'price' => $row->price);
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

        $rows = $this->recipe_model->getrecipeForPrinting($term);
        if ($rows) {
            foreach ($rows as $row) {
                $variants = $this->recipe_model->getrecipeOptions($row->id);
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
            $recipe = $this->input->get('recipe');
            if (!isset($recipe['code']) || empty($recipe['code'])) {
                exit(json_encode(array('msg' => lang('recipe_code_is_required'))));
            }
            if (!isset($recipe['name']) || empty($recipe['name'])) {
                exit(json_encode(array('msg' => lang('recipe_name_is_required'))));
            }
            if (!isset($recipe['category_id']) || empty($recipe['category_id'])) {
                exit(json_encode(array('msg' => lang('recipe_category_is_required'))));
            }
            if (!isset($recipe['unit']) || empty($recipe['unit'])) {
                exit(json_encode(array('msg' => lang('recipe_unit_is_required'))));
            }
            if (!isset($recipe['price']) || empty($recipe['price'])) {
                exit(json_encode(array('msg' => lang('recipe_price_is_required'))));
            }
            if (!isset($recipe['cost']) || empty($recipe['cost'])) {
                exit(json_encode(array('msg' => lang('recipe_cost_is_required'))));
            }
            if ($this->recipe_model->getrecipeByCode($recipe['code'])) {
                exit(json_encode(array('msg' => lang('recipe_code_already_exist'))));
            }
            if ($row = $this->recipe_model->addAjaxrecipe($recipe)) {
                $tax_rate = $this->site->getTaxRateByID($row->tax_rate);
                $pr = array('id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'code' => $row->code, 'qty' => 1, 'cost' => $row->cost, 'name' => $row->name, 'tax_method' => $row->tax_method, 'tax_rate' => $tax_rate, 'discount' => '0');
                $this->sma->send_json(array('msg' => 'success', 'result' => $pr));
            } else {
                exit(json_encode(array('msg' => lang('failed_to_add_recipe'))));
            }
        } else {
            json_encode(array('msg' => 'Invalid token'));
        }

    }


    /* -------------------------------------------------------- */

function Piece_required() 
{
      $recipe_standard = $this->input->post('recipe_standard');
      $piece = $this->input->post('piece'); 
      if(($recipe_standard != 1)  AND ($piece ==  0))
      {
        return FALSE;
      }
      else{
        return TRUE;
      }
      
    }

    function edit($id = NULL)
    {
        $this->sma->checkPermissions('edit');
        $this->load->helper('security');
        if ($this->input->post('id')) {
            $id = $this->input->post('id');
        }
        $warehouses = $this->site->getAllWarehouses();
        $warehouses_recipe = $this->recipe_model->getAllWarehouseWithRecipe($id);
	$product_recipe = $this->recipe_model->getAllProductWithRecipe($id);
	$addon_recipe = $this->recipe_model->getAllAddonWithRecipe($id);
	$recipe_standard = ($this->input->post('recipe_standard'))?$this->input->post('recipe_standard'):array();
		
        $recipe = $this->site->getrecipeByID($id);
        if (!$id || !$recipe) {
            $this->session->set_flashdata('error', lang('prduct_not_found'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $this->form_validation->set_rules('category', lang("category"), 'required|is_natural_no_zero');
        
		
        if ($this->input->post('code') !== $recipe->code) {
            $this->form_validation->set_rules('code', lang("recipe_code"), 'is_unique[recipe.code]');
        }
		
		
        if ($this->input->post('name') !== $recipe->name) {
            $this->form_validation->set_rules('name', lang("recipe_name"), 'is_unique[recipe.name]');
        }      

       /* $this->form_validation->set_rules('slug', lang("slug"), 'required|alpha_dash');
        if ($this->input->post('slug') !== $recipe->slug) {
            $this->form_validation->set_rules('slug', lang("slug"), 'required|is_unique[recipe.slug]|alpha_dash');
        }*/
        if(in_array(2,$recipe_standard))
        { 
	    $this->form_validation->set_rules('piece', lang("piece"), 'required|callback_Piece_required');
	}
        $this->form_validation->set_rules('recipe_image', lang("recipe_image"), 'xss_clean');
        $this->form_validation->set_rules('digital_file', lang("digital_file"), 'xss_clean');
        $this->form_validation->set_rules('userfile', lang("recipe_gallery_images"), 'xss_clean');
		//$this->form_validation->set_rules('stock_quantity', lang("stock_quantity"), 'required');
        if ($this->form_validation->run('recipe/edit') == true) {

            /*echo "<pre>";
print_r($_POST);exit;*/
            $data = array(
				'code' => $this->input->post('code'),
				'recipe_standard' => implode(',',$recipe_standard),
				'piece' => $this->input->post('piece'),
				'khmer_name' => $this->input->post('khmer_name'),
				'khmer_image' => str_replace(' ', '-',$this->input->post('name')).'.png',
                'barcode_symbology' => $this->input->post('barcode_symbology') ? $this->input->post('barcode_symbology') : 'code25',
				'name' => $this->input->post('name'),
				'currency_type' => $this->input->post('currency_type'),
				'kitchens_id' => $this->input->post('kitchens_id'),
                //'type' => $this->input->post('type'),
				'stock_quantity' => $this->input->post('stock_quantity') ? $this->input->post('stock_quantity') : 0,
                'category_id' => $this->input->post('category'),
                'subcategory_id' => $this->input->post('subcategory') ? $this->input->post('subcategory') : NULL,
                'tax_rate' => $this->input->post('tax_rate'),
                'tax_method' => $this->input->post('tax_method'),
                'tax_amount' => $this->input->post('tax_amount'),
                'track_quantity' => $this->input->post('track_quantity') ? $this->input->post('track_quantity') : '0',
                'details' => $this->input->post('details'),
                'recipe_details' => $this->input->post('recipe_details'),
		'suppliers'=>implode(',',$this->input->post('supplier')),
                //'supplier1' => $this->input->post('supplier'),
                'supplier1price' => $this->sma->formatDecimal($this->input->post('supplier_price')),
                'supplier2' => $this->input->post('supplier_2'),
                'supplier2price' => $this->sma->formatDecimal($this->input->post('supplier_2_price')),
                'supplier3' => $this->input->post('supplier_3'),
                'supplier3price' => $this->sma->formatDecimal($this->input->post('supplier_3_price')),
                'supplier4' => $this->input->post('supplier_4'),
                'supplier4price' => $this->sma->formatDecimal($this->input->post('supplier_4_price')),
                'supplier5' => $this->input->post('supplier_5'),
                'supplier5price' => $this->sma->formatDecimal($this->input->post('supplier_5_price')),
                'cf1' => $this->input->post('cf1'),
                'cf2' => $this->input->post('cf2'),
                'cf3' => $this->input->post('cf3'),
                'cf4' => $this->input->post('cf4'),
                'cf5' => $this->input->post('cf5'),
                'cf6' => $this->input->post('cf6'),
                'manufacturem_date' => '',
                'expriy_date' =>  '',
                'supplier1_part_no' => $this->input->post('supplier_part_no'),
                'supplier2_part_no' => $this->input->post('supplier_2_part_no'),
                'supplier3_part_no' => $this->input->post('supplier_3_part_no'),
                'supplier4_part_no' => $this->input->post('supplier_4_part_no'),
                'supplier5_part_no' => $this->input->post('supplier_5_part_no'),
                'file' => $this->input->post('file_link'),
                'slug' => $this->input->post('slug'),
				'cost' => $this->input->post('cost'),
				'price' => $this->input->post('cost'),//$this->input->post('price'),
                'featured' => $this->input->post('featured'),
                'hsn_code' => $this->input->post('hsn_code'),
                'hide' => $this->input->post('hide') ? $this->input->post('hide') : 0,
		'preparation_time' => $this->input->post('preparation_time'),
		
		'minimum_quantity' => $this->input->post('minimum_quantity') ? $this->input->post('minimum_quantity') : 0,
		'reorder_quantity' => $this->input->post('reorder_quantity') ? $this->input->post('reorder_quantity') : 0,
		'type_expiry' => $this->input->post('type_expiry') ? $this->input->post('type_expiry') : '',
		'value_expiry' => $this->input->post('value_expiry') ? $this->input->post('value_expiry') : 0,
		'unit' => $this->input->post('unit'),
		'sale_unit' => $this->input->post('default_sale_unit'),
                'purchase_unit' => $this->input->post('default_purchase_unit'),
		'purchase_cost' => $this->input->post('purchase_cost'),
		'maximum_quantity' => $this->input->post('maximum_quantity'),
		'batch_required' => $this->input->post('batch_required'),		
		'expiry_date_required' => $this->input->post('expiry_date_required'),
		'purchase_tax' => $this->input->post('purchase_tax'),
		'brand' => $this->input->post('brand'),
            );
		//echo '<pre>';print_R($data);exit;
			
            $warehouse_qty = NULL;
			$recipe_pro = NULL;
			
            $this->load->library('upload');
			 for($i=0; $i<count($this->input->post('warehouse[]')); $i++){
					$warehouse_qty[] = array(
						'warehouse_id' => $this->input->post('warehouse['.$i.']'),
					);
				}
		
			if ($this->input->post('type') != 'addon') {
				if(array_filter($this->input->post('recipe_addon[]'))){
					for($j=0; $j<count($this->input->post('recipe_addon[]')); $j++){
						$recipe_aon[] = array(
							'recipe_addon' => $this->input->post('recipe_addon['.$j.']'),
						);
					}
				}
			}
				
             if ($this->input->post('type') == 'addon' || $this->input->post('type') == 'production' || $this->input->post('type') == 'semi_finished') {
                
				
				
				for($j=0; $j<count($this->input->post('purchase_item[id]')); $j++){
					$recipe_pro[] = array(
						'product_id' => $this->input->post('purchase_item[id]['.$j.']'),
						'quantity' => $this->input->post('purchase_item[quantity]['.$j.']'),
						'unit_id' => $this->input->post('purchase_item[unit_id]['.$j.']'),
						'id' => ($this->input->post('purchase_item[id]['.$j.']'))?$this->input->post('purchase_item[id]['.$j.']'):0,
					);
				}
				
              
            }
	    if($this->input->post('type') == 'combo'){
				
				for($k=0; $k<count($this->input->post('combo_item_id[]')); $k++){
					$items[] = array(
						'item_id' => $this->input->post('combo_item_id['.$k.']'),
						'item_code' => $this->input->post('combo_item_code['.$k.']'),
						'unit_price' => $this->input->post('combo_item_price['.$k.']'),
						'quantity' => $this->input->post('combo_item_quantity['.$k.']'),
					);
				}
			}
			
			
         
            if ($_FILES['recipe_image']['size'] > 0) {

                $config['upload_path'] = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['max_width'] = $this->Settings->iwidth;
                $config['max_height'] = $this->Settings->iheight;
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $config['max_filename'] = 25;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('recipe_image')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    admin_redirect("recipe/edit/" . $id);
                }
                $photo = $this->upload->file_name;
                $data['image'] = $photo;
                $this->load->library('image_lib');
                $config['image_library'] = 'gd2';
                $config['source_image'] = $this->upload_path . $photo;
                $config['new_image'] = $this->thumbs_path . $photo;
                $config['maintain_ratio'] = TRUE;
                $config['width'] = $this->Settings->twidth;
                $config['height'] = $this->Settings->theight;
                $this->image_lib->clear();
                $this->image_lib->initialize($config);
                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
                if ($this->Settings->watermark) {
                    $this->image_lib->clear();
                    $wm['source_image'] = $this->upload_path . $photo;
                    $wm['wm_text'] = 'Copyright ' . date('Y') . ' - ' . $this->Settings->site_name;
                    $wm['wm_type'] = 'text';
                    $wm['wm_font_path'] = 'system/fonts/texb.ttf';
                    $wm['quality'] = '100';
                    $wm['wm_font_size'] = '16';
                    $wm['wm_font_color'] = '999999';
                    $wm['wm_shadow_color'] = 'CCCCCC';
                    $wm['wm_vrt_alignment'] = 'top';
                    $wm['wm_hor_alignment'] = 'left';
                    $wm['wm_padding'] = '10';
                    $this->image_lib->initialize($wm);
                    $this->image_lib->watermark();
                }
                $this->image_lib->clear();
                $config = NULL;
            }

            if ($_FILES['userfile']['name'][0] != "") {

                $config['upload_path'] = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['max_width'] = $this->Settings->iwidth;
                $config['max_height'] = $this->Settings->iheight;
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $config['max_filename'] = 25;
                $files = $_FILES;
                $cpt = count($_FILES['userfile']['name']);
                for ($i = 0; $i < $cpt; $i++) {

                    $_FILES['userfile']['name'] = $files['userfile']['name'][$i];
                    $_FILES['userfile']['type'] = $files['userfile']['type'][$i];
                    $_FILES['userfile']['tmp_name'] = $files['userfile']['tmp_name'][$i];
                    $_FILES['userfile']['error'] = $files['userfile']['error'][$i];
                    $_FILES['userfile']['size'] = $files['userfile']['size'][$i];

                    $this->upload->initialize($config);

                    if (!$this->upload->do_upload()) {
                        $error = $this->upload->display_errors();
                        $this->session->set_flashdata('error', $error);
                        admin_redirect("recipe/edit/" . $id);
                    } else {

                        $pho = $this->upload->file_name;

                        $photos[] = $pho;

                        $this->load->library('image_lib');
                        $config['image_library'] = 'gd2';
                        $config['source_image'] = $this->upload_path . $pho;
                        $config['new_image'] = $this->thumbs_path . $pho;
                        $config['maintain_ratio'] = TRUE;
                        $config['width'] = $this->Settings->twidth;
                        $config['height'] = $this->Settings->theight;

                        $this->image_lib->initialize($config);

                        if (!$this->image_lib->resize()) {
                            echo $this->image_lib->display_errors();
                        }

                        if ($this->Settings->watermark) {
                            $this->image_lib->clear();
                            $wm['source_image'] = $this->upload_path . $pho;
                            $wm['wm_text'] = 'Copyright ' . date('Y') . ' - ' . $this->Settings->site_name;
                            $wm['wm_type'] = 'text';
                            $wm['wm_font_path'] = 'system/fonts/texb.ttf';
                            $wm['quality'] = '100';
                            $wm['wm_font_size'] = '16';
                            $wm['wm_font_color'] = '999999';
                            $wm['wm_shadow_color'] = 'CCCCCC';
                            $wm['wm_vrt_alignment'] = 'top';
                            $wm['wm_hor_alignment'] = 'left';
                            $wm['wm_padding'] = '10';
                            $this->image_lib->initialize($wm);
                            $this->image_lib->watermark();
                        }

                        $this->image_lib->clear();
                    }
                }
                $config = NULL;
            } else {
                $photos = NULL;
            }
            
            $filename = 'assets/language/'.str_replace(' ', '-',$_POST['name']).'.png';
            $this->base64ToImage($_POST['recipe_name_img'],$filename);
    	    /*$filename = 'assets/language/'.str_replace(' ', '-',$_POST['name']).'.png';
    	    $this->base64ToImage($_POST['recipe_name_img'],$filename);*/
        }

		
		
		
        if ($this->form_validation->run() == true && $this->recipe_model->updaterecipe_new($id, $data, $warehouse_qty, $recipe_pro, $recipe_aon, $items, $photos)) {		
			
			 $this->recipe_model->updateRecipe_variantValues($id);
            $this->session->set_flashdata('message', lang("recipe_updated"));
            admin_redirect('recipe');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['categories'] = $this->site->getAllrecipeCategories();
            $this->data['tax_rates'] = $this->site->getAllTaxRates();
            $this->data['brands'] = $this->site->getAllBrands();
            $this->data['base_units'] = $this->site->getAllBaseUnits();
            $this->data['warehouses'] = $warehouses;
            $this->data['warehouses_recipe'] = $warehouses_recipe;
			$this->data['product_recipe'] = $product_recipe;
			$this->data['addon_recipe'] = $addon_recipe;
			
			$this->data['reskitchen'] = $this->site->getAllResKitchen();
			$this->data['rescurrency'] = $this->site->getAllCurrencies();
			
            $this->data['recipe'] = $recipe;
            $this->data['variants'] = $this->recipe_model->getAllVariants();
            
            $this->data['recipe_variants'] = $this->recipe_model->getrecipeOptions($id);
            $this->data['combo_items'] = $recipe->type == 'combo' ? $this->recipe_model->getrecipeComboItems($recipe->id) : NULL;
            $this->data['recipe_options'] = $id ? $this->recipe_model->getrecipeOptionsWithWH($id) : NULL;
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('recipe'), 'page' => lang('recipe')), array('link' => '#', 'page' => lang('edit_recipe')));
            $meta = array('page_title' => lang('edit_recipe'), 'bc' => $bc);
            $this->data['varient_values'] = $this->recipe_model->getRecipeVariantData($recipe->id);
	    $this->data['suppliers'] = $this->site->getAllSuppliers();
	    $this->data['sub_units'] = $this->site->getUnitsByBUID($recipe->unit);
            $this->page_construct('recipe/edit', $meta, $this->data);
        }
    }

    /* ---------------------------------------------------------------- */

    function import_csv_bk()
    {
        $this->sma->checkPermissions('csv');
        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');

        if ($this->form_validation->run() == true) {

            if (isset($_FILES["userfile"])) {

                $this->load->library('upload');
                $config['upload_path'] = $this->digital_upload_path;
                $config['allowed_types'] = 'csv';
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = TRUE;
                $config['encrypt_name'] = TRUE;
                $config['max_filename'] = 25;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    admin_redirect("recipe/import_csv");
                }

                $csv = $this->upload->file_name;

                $arrResult = array();
                $handle = fopen($this->digital_upload_path . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);
                $keys = array('type', 'name', 'code', 'barcode_symbology', 'category_code', 'subcategory_code', 'kitchens_id', 'currency_type', 'cost',  'image', 'warehouse_id');

                $final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                // $this->sma->print_arrays($final);
                $rw = 2; $items = array();
                foreach ($final as $csv_pr) {
                    if ( ! $this->recipe_model->getrecipeByCode(trim($csv_pr['code']))) {
                        if ($catd = $this->recipe_model->getCategoryByCode(trim($csv_pr['category_code']))) {
                           
                            
                            $prsubcat = $this->recipe_model->getCategoryByCode(trim($csv_pr['subcategory_code']));
                            $items[] = array (
								'type' => trim($csv_pr['type']),
                                'code' => trim($csv_pr['code']),
                                'name' => trim($csv_pr['name']),
								'price' => trim($csv_pr['cost']),
								'kitchens_id' => trim($csv_pr['kitchens_id']),
								'currency_type' => trim($csv_pr['currency_type']),
								'category_id' => $catd->id,
                                'barcode_symbology' => mb_strtolower(trim($csv_pr['barcode_symbology']), 'UTF-8'),
                                'cost' => trim($csv_pr['cost']),
								'price' => trim($csv_pr['cost']),
                                'subcategory_id' => ($prsubcat ? $prsubcat->id : NULL),
                                'image' => trim($csv_pr['image']),
								'warehouse_id' => trim($csv_pr['warehouse_id']),
								'active' => 1,
								
                                );
                        } else {
                            $this->session->set_flashdata('error', lang("check_category_code") . " (" . $csv_pr['category_code'] . "). " . lang("category_code_x_exist") . " " . lang("line_no") . " " . $rw);
                            admin_redirect("recipe/import_csv");
                        }
                    }

                    $rw++;
                }
            }

            // $this->sma->print_arrays($items);
        }

        if ($this->form_validation->run() == true && $prs = $this->recipe_model->add_recipe($items)) {
            $this->session->set_flashdata('message', sprintf(lang("recipe_added"), $prs));
            admin_redirect('recipe');
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['userfile'] = array('name' => 'userfile',
                'id' => 'userfile',
                'type' => 'text',
                'value' => $this->form_validation->set_value('userfile')
            );

            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('recipe'), 'page' => lang('recipe')), array('link' => '#', 'page' => lang('import_recipe_by_csv')));
            $meta = array('page_title' => lang('import_recipe_by_csv'), 'bc' => $bc);
            $this->page_construct('recipe/import_csv', $meta, $this->data);

        }
    }
    function download_sample(){
    $filename = 'sales-item-import.csv';
    header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="'.$filename.'";');
    $header = array('recipe_code','type','name','sales_item_type','kitchen_type','category','subcategory','quantity','recipe_cost','currency','warehouse_id','pieces');
    $fp = fopen('php://output', 'w');       
        fputcsv($fp, $header);
    fclose($fp);    
    }
    
    function import_csv(){
    $this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');
    
 
        if ($this->form_validation->run() == true) {
    //$filename = 'uploaded_sales_item_status.csv';
    //header('Content-Type: application/csv');
    //header('Content-Disposition: attachment; filename="'.$filename.'";');
            if (isset($_FILES["userfile"])) {

                $this->load->library('upload');
                $config['upload_path'] = $this->digital_upload_path;
                $config['allowed_types'] = 'csv';
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = TRUE;
                $config['encrypt_name'] = TRUE;
                $config['max_filename'] = 25;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    admin_redirect("recipe/import_csv");
                }

                $csv = $this->upload->file_name;

                $arrResult = array();
                $handle = fopen($this->digital_upload_path . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ",")) !== FALSE) {                 
            $arrResult[] = $row;
                    }
                    fclose($handle);
                }
        $keys = array_shift($arrResult);//print_R(array_values($keys));exit;
        $values = $arrResult;
                $final = array();
                foreach ($arrResult as $k => $value) {
                    $final[$k] = array_combine($keys,array_values($value));
                }
        
        
                $items = array();
        
        //$fp = fopen('php://output', 'w');
        //header('Set-Cookie: fileLoading=true'); 
        //$keys[] = 'status';
        //fputcsv($fp, $keys);
        $rw = 2;
                foreach ($final as $ik => $csv_pr) {
          $error = false;
            if($this->my_is_unique($csv_pr['recipe_code'],'code','recipe')){
            $this->session->set_flashdata('error', lang("check_recipe_code") . " (" . $csv_pr['recipe_code'] . "). " . lang("recipe_code_already_exist") . " " . lang("line_no") . " " . $rw);
                        admin_redirect("recipe/import_csv");
            }
            if($this->my_is_unique($csv_pr['name'],'name','recipe')){
            $this->session->set_flashdata('error', lang("check_recipe_name") . " (" . $csv_pr['name'] . "). " . lang("recipe_name_already_exist") . " " . lang("line_no") . " " . $rw);
                      
                admin_redirect("recipe/import_csv");
            }
            $currecny = $this->site->getCurrencyByCode($csv_pr['currency']);
            $kitchen_type = $this->recipe_model->getKitchen_idByName($csv_pr['kitchen_type']);
            $category_subcate = $this->recipe_model->getCategoryNsubByName($csv_pr['category'],$csv_pr['subcategory'],$kitchen_type);
            //$items = $csv_pr;
            $items[$ik]['code'] = $csv_pr['recipe_code'];
	    $items[$ik]['recipe_standard'] = $csv_pr['type'];
	    $items[$ik]['piece'] = $csv_pr['pieces'];
            $items[$ik]['type'] = $csv_pr['sales_item_type'];
            $items[$ik]['name'] = $csv_pr['name'];
            $txt = $csv_pr['native_name'];
            $items[$ik]['khmer_name'] = $csv_pr['name'];//mb_convert_encoding($txt, "GB2312", "UTF-8");//mb_convert_encoding($csv_pr['native_name'], "UTF-8");// 'ផ្សិត';//
            $items[$ik]['cost'] = $csv_pr['recipe_cost'];
            $items[$ik]['price'] = $csv_pr['recipe_cost'];
            $items[$ik]['stock_quantity'] = $csv_pr['quantity'];
            $items[$ik]['kitchens_id'] = $kitchen_type;
            $items[$ik]['category_id'] = $category_subcate['cat_id'];
            $items[$ik]['subcategory_id'] = $category_subcate['subcat_id'];
            $items[$ik]['active'] = 1;
            $items[$ik]['currency_type'] = $currecny->rate;
            $items[$ik]['warehouse'] = $csv_pr['warehouse_id'];         
            $rw++;
          //fputcsv($fp, $csv_pr);
                }
        //fclose($fp);
            }
        @unlink($this->digital_upload_path . $csv);
        //$this->session->set_flashdata('message', lang("items_imported_._Please_check_the_downloded_csv"));
        //admin_redirect('recipe/import_csv?success');
            // $this->sma->print_arrays($items);
        }

        if ($this->form_validation->run() == true && $prs = $this->recipe_model->import_recipe($items)) {
            $this->session->set_flashdata('message', sprintf(lang("recipe_added"), $prs));
            admin_redirect('recipe');
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['userfile'] = array('name' => 'userfile',
                'id' => 'userfile',
                'type' => 'text',
                'value' => $this->form_validation->set_value('userfile')
            );

            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('recipe'), 'page' => lang('recipe')), array('link' => '#', 'page' => lang('import_recipe_by_csv')));
            $meta = array('page_title' => lang('import_recipe_by_csv'), 'bc' => $bc);
        $this->data['kitchens'] = $this->site->getAllKitchens();
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['currency'] = $this->site->getAllCurrencies();
        $this->data['sales_item_types'] = $this->site->getAllCurrencies();
            $this->page_construct('recipe/import_csv', $meta, $this->data);

        }
    
    }

    function my_is_unique($value,$field,$table){
        $CI =& get_instance();
        if($CI->site->my_is_unique($value,$field,$table)){
            return false;
        }
        return true;
    }
    /* ------------------------------------------------------------------ */

    function update_price()
    {
        $this->sma->checkPermissions('csv');
        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');

        if ($this->form_validation->run() == true) {

            if (DEMO) {
                $this->session->set_flashdata('message', lang("disabled_in_demo"));
                admin_redirect('welcome');
            }

            if (isset($_FILES["userfile"])) {

                $this->load->library('upload');
                $config['upload_path'] = $this->digital_upload_path;
                $config['allowed_types'] = 'csv';
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = TRUE;
                $config['encrypt_name'] = TRUE;
                $config['max_filename'] = 25;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    admin_redirect("recipe");
                }

                $csv = $this->upload->file_name;

                $arrResult = array();
                $handle = fopen($this->digital_upload_path . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);

                $keys = array('code', 'price');

                $final = array();

                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                $rw = 2;
                foreach ($final as $csv_pr) {
                    if (!$this->recipe_model->getrecipeByCode(trim($csv_pr['code']))) {
                        $this->session->set_flashdata('message', lang("check_recipe_code") . " (" . $csv_pr['code'] . "). " . lang("code_x_exist") . " " . lang("line_no") . " " . $rw);
                        admin_redirect("recipe");
                    }
                    $rw++;
                }
            }

        } elseif ($this->input->post('update_price')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("system_settings/group_recipe_prices/".$group_id);
        }

        if ($this->form_validation->run() == true && !empty($final)) {
            $this->recipe_model->updatePrice($final);
            $this->session->set_flashdata('message', lang("price_updated"));
            admin_redirect('recipe');
        } else {

            $this->data['userfile'] = array('name' => 'userfile',
                'id' => 'userfile',
                'type' => 'text',
                'value' => $this->form_validation->set_value('userfile')
            );
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme.'recipe/update_price', $this->data);

        }
    }

    /* ------------------------------------------------------------------------------- */

    function delete($id = NULL)
    {
        $this->sma->checkPermissions(NULL, TRUE);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
		$delete_check = $this->recipe_model->checkDeleterecipe($id);
		if($delete_check == FALSE){
			
        if ($this->recipe_model->deleterecipe($id)) {
            if($this->input->is_ajax_request()) {
                $this->sma->send_json(array('error' => 0, 'msg' => lang("recipe_deleted")));
            }
            $this->session->set_flashdata('message', lang('recipe_deleted'));
            admin_redirect('welcome');
        }
		}else{
			$this->sma->send_json(array('error' => 1, 'msg' => lang("could_not_be_delete_purchases_item_used_in_sale_item")));	
		}

    }

    /* ----------------------------------------------------------------------------- */

    function quantity_adjustments($warehouse_id = NULL)
    {
        $this->sma->checkPermissions('adjustments');

        if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
        } else {
            $this->data['warehouses'] = null;
            $this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        }

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('recipe'), 'page' => lang('recipe')), array('link' => '#', 'page' => lang('quantity_adjustments')));
        $meta = array('page_title' => lang('quantity_adjustments'), 'bc' => $bc);
        $this->page_construct('recipe/quantity_adjustments', $meta, $this->data);
    }

    function getadjustments($warehouse_id = NULL)
    {
        $this->sma->checkPermissions('adjustments');

        $delete_link = "<a href='#' class='tip po' title='<b>" . $this->lang->line("delete_adjustment") . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('recipe/delete_adjustment/$1') . "'>"
            . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a>";

        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('adjustments')}.id as id, date, reference_no, warehouses.name as wh_name, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as created_by, note, attachment")
            ->from('adjustments')
            ->join('warehouses', 'warehouses.id=adjustments.warehouse_id', 'left')
            ->join('users', 'users.id=adjustments.created_by', 'left')
            ->group_by("adjustments.id");
            if ($warehouse_id) {
                $this->datatables->where('adjustments.warehouse_id', $warehouse_id);
            }
        $this->datatables->add_column("Actions", "<div class='text-center'><a href='" . admin_url('recipe/edit_adjustment/$1') . "' class='tip' title='" . lang("edit_adjustment") . "'><i class='fa fa-edit'></i></a> " . $delete_link . "</div>", "id");

        echo $this->datatables->generate();

    }

    public function view_adjustment($id)
    {
        $this->sma->checkPermissions('adjustments', TRUE);

        $adjustment = $this->recipe_model->getAdjustmentByID($id);
        if (!$id || !$adjustment) {
            $this->session->set_flashdata('error', lang('adjustment_not_found'));
            $this->sma->md();
        }

        $this->data['inv'] = $adjustment;
        $this->data['rows'] = $this->recipe_model->getAdjustmentItems($id);
        $this->data['created_by'] = $this->site->getUser($adjustment->created_by);
        $this->data['updated_by'] = $this->site->getUser($adjustment->updated_by);
        $this->data['warehouse'] = $this->site->getWarehouseByID($adjustment->warehouse_id);
        $this->load->view($this->theme.'recipe/view_adjustment', $this->data);
    }

    function add_adjustment($count_id = NULL)
    {
        $this->sma->checkPermissions('adjustments', true);
        $this->form_validation->set_rules('warehouse', lang("warehouse"), 'required');

        if ($this->form_validation->run() == true) {

            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld($this->input->post('date'));
            } else {
                $date = date('Y-m-d H:s:i');
            }

            $reference_no = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('qa');
            $warehouse_id = $this->input->post('warehouse');
            $note = $this->sma->clear_tags($this->input->post('note'));

            $i = isset($_POST['recipe_id']) ? sizeof($_POST['recipe_id']) : 0;
            for ($r = 0; $r < $i; $r++) {

                $recipe_id = $_POST['recipe_id'][$r];
                $type = $_POST['type'][$r];
                $quantity = $_POST['quantity'][$r];
                $serial = $_POST['serial'][$r];
                $variant = isset($_POST['variant'][$r]) && !empty($_POST['variant'][$r]) ? $_POST['variant'][$r] : NULL;

                if (!$this->Settings->overselling && $type == 'subtraction') {
                    if ($variant) {
                        if($op_wh_qty = $this->recipe_model->getrecipeWarehouseOptionQty($variant, $warehouse_id)) {
                            if ($op_wh_qty->quantity < $quantity) {
                                $this->session->set_flashdata('error', lang('warehouse_option_qty_is_less_than_damage'));
                                redirect($_SERVER["HTTP_REFERER"]);
                            }
                        } else {
                            $this->session->set_flashdata('error', lang('warehouse_option_qty_is_less_than_damage'));
                            redirect($_SERVER["HTTP_REFERER"]);
                        }
                    }
                    if($wh_qty = $this->recipe_model->getrecipeQuantity($recipe_id, $warehouse_id)) {
                        if ($wh_qty['quantity'] < $quantity) {
                            $this->session->set_flashdata('error', lang('warehouse_qty_is_less_than_damage'));
                            redirect($_SERVER["HTTP_REFERER"]);
                        }
                    } else {
                        $this->session->set_flashdata('error', lang('warehouse_qty_is_less_than_damage'));
                        redirect($_SERVER["HTTP_REFERER"]);
                    }
                }

                $recipe[] = array(
                    'recipe_id' => $recipe_id,
                    'type' => $type,
                    'quantity' => $quantity,
                    'warehouse_id' => $warehouse_id,
                    'option_id' => $variant,
                    'serial_no' => $serial,
                    );


            }

            if (empty($recipe)) {
                $this->form_validation->set_rules('recipe', lang("recipe"), 'required');
            } else {
                krsort($recipe);
            }

            $data = array(
                'date' => $date,
                'reference_no' => $reference_no,
                'warehouse_id' => $warehouse_id,
                'note' => $note,
                'created_by' => $this->session->userdata('user_id'),
                'count_id' => $this->input->post('count_id') ? $this->input->post('count_id') : NULL,
                );

            if ($_FILES['document']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = $this->digital_upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = false;
                $config['encrypt_name'] = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('document')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
                $data['attachment'] = $photo;
            }

            // $this->sma->print_arrays($data, $recipe);

        }

        if ($this->form_validation->run() == true && $this->recipe_model->addAdjustment($data, $recipe)) {
            $this->session->set_userdata('remove_qals', 1);
            $this->session->set_flashdata('message', lang("quantity_adjusted"));
            admin_redirect('recipe/quantity_adjustments');
        } else {

            if ($count_id) {
                $stock_count = $this->recipe_model->getStouckCountByID($count_id);
                $items = $this->recipe_model->getStockCountItems($count_id);
                $c = rand(100000, 9999999);
                foreach ($items as $item) {
                    if ($item->counted != $item->expected) {
                        $recipe = $this->site->getrecipeByID($item->recipe_id);
                        $row = json_decode('{}');
                        $row->id = $item->recipe_id;
                        $row->code = $recipe->code;
                        $row->name = $recipe->name;
                        $row->qty = $item->counted-$item->expected;
                        $row->type = $row->qty > 0 ? 'addition' : 'subtraction';
                        $row->qty = $row->qty > 0 ? $row->qty : (0-$row->qty);
                        $options = $this->recipe_model->getrecipeOptions($recipe->id);
                        $row->option = $item->recipe_variant_id ? $item->recipe_variant_id : 0;
                        $row->serial = '';
                        $ri = $this->Settings->item_addition ? $recipe->id : $c;

                        $pr[$ri] = array('id' => str_replace(".", "", microtime(true)), 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")",
                            'row' => $row, 'options' => $options);
                        $c++;
                    }
                }
            }
            $this->data['adjustment_items'] = $count_id ? json_encode($pr) : FALSE;
            $this->data['warehouse_id'] = $count_id ? $stock_count->warehouse_id : FALSE;
            $this->data['count_id'] = $count_id;
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('recipe'), 'page' => lang('recipe')), array('link' => '#', 'page' => lang('add_adjustment')));
            $meta = array('page_title' => lang('add_adjustment'), 'bc' => $bc);
            $this->page_construct('recipe/add_adjustment', $meta, $this->data);

        }
    }

    function edit_adjustment($id)
    {
        $this->sma->checkPermissions('adjustments', true);
        $adjustment = $this->recipe_model->getAdjustmentByID($id);
        if (!$id || !$adjustment) {
            $this->session->set_flashdata('error', lang('adjustment_not_found'));
            $this->sma->md();
        }
        $this->form_validation->set_rules('warehouse', lang("warehouse"), 'required');

        if ($this->form_validation->run() == true) {

            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld($this->input->post('date'));
            } else {
                $date = $adjustment->date;
            }

            $reference_no = $this->input->post('reference_no');
            $warehouse_id = $this->input->post('warehouse');
            $note = $this->sma->clear_tags($this->input->post('note'));

            $i = isset($_POST['recipe_id']) ? sizeof($_POST['recipe_id']) : 0;
            for ($r = 0; $r < $i; $r++) {

                $recipe_id = $_POST['recipe_id'][$r];
                $type = $_POST['type'][$r];
                $quantity = $_POST['quantity'][$r];
                $serial = $_POST['serial'][$r];
                $variant = isset($_POST['variant'][$r]) && !empty($_POST['variant'][$r]) ? $_POST['variant'][$r] : null;

                if (!$this->Settings->overselling && $type == 'subtraction') {
                    if ($variant) {
                        if($op_wh_qty = $this->recipe_model->getrecipeWarehouseOptionQty($variant, $warehouse_id)) {
                            if ($op_wh_qty->quantity < $quantity) {
                                $this->session->set_flashdata('error', lang('warehouse_option_qty_is_less_than_damage'));
                                redirect($_SERVER["HTTP_REFERER"]);
                            }
                        } else {
                            $this->session->set_flashdata('error', lang('warehouse_option_qty_is_less_than_damage'));
                            redirect($_SERVER["HTTP_REFERER"]);
                        }
                    }
                    if($wh_qty = $this->recipe_model->getrecipeQuantity($recipe_id, $warehouse_id)) {
                        if ($wh_qty['quantity'] < $quantity) {
                            $this->session->set_flashdata('error', lang('warehouse_qty_is_less_than_damage'));
                            redirect($_SERVER["HTTP_REFERER"]);
                        }
                    } else {
                        $this->session->set_flashdata('error', lang('warehouse_qty_is_less_than_damage'));
                        redirect($_SERVER["HTTP_REFERER"]);
                    }
                }

                $recipe[] = array(
                    'recipe_id' => $recipe_id,
                    'type' => $type,
                    'quantity' => $quantity,
                    'warehouse_id' => $warehouse_id,
                    'option_id' => $variant,
                    'serial_no' => $serial,
                    );

            }

            if (empty($recipe)) {
                $this->form_validation->set_rules('recipe', lang("recipe"), 'required');
            } else {
                krsort($recipe);
            }

            $data = array(
                'date' => $date,
                'reference_no' => $reference_no,
                'warehouse_id' => $warehouse_id,
                'note' => $note,
                'created_by' => $this->session->userdata('user_id')
                );

            if ($_FILES['document']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = $this->digital_upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = false;
                $config['encrypt_name'] = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('document')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
                $data['attachment'] = $photo;
            }

            // $this->sma->print_arrays($data, $recipe);

        }

        if ($this->form_validation->run() == true && $this->recipe_model->updateAdjustment($id, $data, $recipe)) {
            $this->session->set_userdata('remove_qals', 1);
            $this->session->set_flashdata('message', lang("quantity_adjusted"));
            admin_redirect('recipe/quantity_adjustments');
        } else {

            $inv_items = $this->recipe_model->getAdjustmentItems($id);
            // krsort($inv_items);
            $c = rand(100000, 9999999);
            foreach ($inv_items as $item) {
                $recipe = $this->site->getrecipeByID($item->recipe_id);
                $row = json_decode('{}');
                $row->id = $item->recipe_id;
                $row->code = $recipe->code;
                $row->name = $recipe->name;
                $row->qty = $item->quantity;
                $row->type = $item->type;
                $options = $this->recipe_model->getrecipeOptions($recipe->id);
                $row->option = $item->option_id ? $item->option_id : 0;
                $row->serial = $item->serial_no ? $item->serial_no : '';
                $ri = $this->Settings->item_addition ? $recipe->id : $c;

                $pr[$ri] = array('id' => str_replace(".", "", microtime(true)), 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")",
                    'row' => $row, 'options' => $options);
                $c++;
            }

            $this->data['adjustment'] = $adjustment;
            $this->data['adjustment_items'] = json_encode($pr);
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('recipe'), 'page' => lang('recipe')), array('link' => '#', 'page' => lang('edit_adjustment')));
            $meta = array('page_title' => lang('edit_adjustment'), 'bc' => $bc);
            $this->page_construct('recipe/edit_adjustment', $meta, $this->data);

        }
    }

    function add_adjustment_by_csv()
    {
        $this->sma->checkPermissions('adjustments', true);
        $this->form_validation->set_rules('warehouse', lang("warehouse"), 'required');

        if ($this->form_validation->run() == true) {

            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld($this->input->post('date'));
            } else {
                $date = date('Y-m-d H:s:i');
            }

            $reference_no = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('qa');
            $warehouse_id = $this->input->post('warehouse');
            $note = $this->sma->clear_tags($this->input->post('note'));
            $data = array(
                'date' => $date,
                'reference_no' => $reference_no,
                'warehouse_id' => $warehouse_id,
                'note' => $note,
                'created_by' => $this->session->userdata('user_id'),
                'count_id' => NULL,
                );

            if ($_FILES['csv_file']['size'] > 0) {

                $this->load->library('upload');
                $config['upload_path'] = $this->digital_upload_path;
                $config['allowed_types'] = 'csv';
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = false;
                $config['encrypt_name'] = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('csv_file')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                $csv = $this->upload->file_name;
                $data['attachment'] = $csv;

                $arrResult = array();
                $handle = fopen($this->digital_upload_path . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);
                $keys = array('code', 'quantity', 'variant');
                $final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                // $this->sma->print_arrays($final);
                $rw = 2;
                foreach ($final as $pr) {
                    if ($recipe = $this->recipe_model->getrecipeByCode(trim($pr['code']))) {
                        $csv_variant = trim($pr['variant']);
                        $variant = !empty($csv_variant) ? $this->recipe_model->getrecipeVariantID($recipe->id, $csv_variant) : FALSE;

                        $csv_quantity = trim($pr['quantity']);
                        $type = $csv_quantity > 0 ? 'addition' : 'subtraction';
                        $quantity = $csv_quantity > 0 ? $csv_quantity : (0-$csv_quantity);

                        if (!$this->Settings->overselling && $type == 'subtraction') {
                            if ($variant) {
                                if($op_wh_qty = $this->recipe_model->getrecipeWarehouseOptionQty($variant, $warehouse_id)) {
                                    if ($op_wh_qty->quantity < $quantity) {
                                        $this->session->set_flashdata('error', lang('warehouse_option_qty_is_less_than_damage'). ' - ' . lang('line_no') . ' ' . $rw);
                                        redirect($_SERVER["HTTP_REFERER"]);
                                    }
                                } else {
                                    $this->session->set_flashdata('error', lang('warehouse_option_qty_is_less_than_damage'). ' - ' . lang('line_no') . ' ' . $rw);
                                    redirect($_SERVER["HTTP_REFERER"]);
                                }
                            }
                            if($wh_qty = $this->recipe_model->getrecipeQuantity($recipe->id, $warehouse_id)) {
                                if ($wh_qty['quantity'] < $quantity) {
                                    $this->session->set_flashdata('error', lang('warehouse_qty_is_less_than_damage'). ' - ' . lang('line_no') . ' ' . $rw);
                                    redirect($_SERVER["HTTP_REFERER"]);
                                }
                            } else {
                                $this->session->set_flashdata('error', lang('warehouse_qty_is_less_than_damage'). ' - ' . lang('line_no') . ' ' . $rw);
                                redirect($_SERVER["HTTP_REFERER"]);
                            }
                        }

                        $recipe[] = array(
                            'recipe_id' => $recipe->id,
                            'type' => $type,
                            'quantity' => $quantity,
                            'warehouse_id' => $warehouse_id,
                            'option_id' => $variant,
                            );

                    } else {
                        $this->session->set_flashdata('error', lang('check_recipe_code') . ' (' . $pr['code'] . '). ' . lang('recipe_code_x_exist') . ' ' . lang('line_no') . ' ' . $rw);
                        redirect($_SERVER["HTTP_REFERER"]);
                    }
                    $rw++;
                }

            } else {
                $this->form_validation->set_rules('csv_file', lang("upload_file"), 'required');
            }

            // $this->sma->print_arrays($data, $recipe);

        }

        if ($this->form_validation->run() == true && $this->recipe_model->addAdjustment($data, $recipe)) {
            $this->session->set_flashdata('message', lang("quantity_adjusted"));
            admin_redirect('recipe/quantity_adjustments');
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('recipe'), 'page' => lang('recipe')), array('link' => '#', 'page' => lang('add_adjustment')));
            $meta = array('page_title' => lang('add_adjustment_by_csv'), 'bc' => $bc);
            $this->page_construct('recipe/add_adjustment_by_csv', $meta, $this->data);

        }
    }

    function delete_adjustment($id = NULL)
    {
        $this->sma->checkPermissions('delete', TRUE);

        if ($this->recipe_model->deleteAdjustment($id)) {
            $this->sma->send_json(array('error' => 0, 'msg' => lang("adjustment_deleted")));
        }

    }

    /* --------------------------------------------------------------------------------------------- */

    function modal_view($id = NULL)
    {
        $this->sma->checkPermissions('index', TRUE);

        $pr_details = $this->site->getrecipeByID($id);
        if (!$id || !$pr_details) {
            $this->session->set_flashdata('error', lang('prduct_not_found'));
            $this->sma->md();
        }
        $this->data['barcode'] = "<img src='" . admin_url('recipe/gen_barcode/' . $pr_details->code . '/' . $pr_details->barcode_symbology . '/40/0') . "' alt='" . $pr_details->code . "' class='pull-left' />";
        if ($pr_details->type == 'combo') {
            $this->data['combo_items'] = $this->recipe_model->getrecipeComboItems($id);
        }
        $this->data['recipe'] = $pr_details;
        $this->data['unit'] = $this->site->getUnitByID($pr_details->unit);
        $this->data['brand'] = $this->site->getBrandByID($pr_details->brand);
        $this->data['images'] = $this->recipe_model->getrecipePhotos($id);
        $this->data['category'] = $this->site->getrecipeCategoryByID($pr_details->category_id);
        $this->data['subcategory'] = $pr_details->subcategory_id ? $this->site->getrecipeCategoryByID($pr_details->subcategory_id) : NULL;
        $this->data['tax_rate'] = $pr_details->tax_rate ? $this->site->getTaxRateByID($pr_details->tax_rate) : NULL;
        $this->data['warehouses'] = $this->recipe_model->getAllWarehousesWithPQ($id);
        $this->data['options'] = $this->recipe_model->getrecipeOptionsWithWH($id);
        $this->data['variants'] = $this->recipe_model->getrecipeOptions($id);

        $this->load->view($this->theme.'recipe/modal_view', $this->data);
    }

    function view($id = NULL)
    {
		
        $this->sma->checkPermissions('index');

        $pr_details = $this->recipe_model->getrecipeByID($id);
		
        if (!$id || !$pr_details) {
            $this->session->set_flashdata('error', lang('recipe_not_found'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $this->data['barcode'] = "<img src='" . admin_url('recipe/gen_barcode/' . $pr_details->code . '/' . $pr_details->barcode_symbology . '/40/0') . "' alt='" . $pr_details->code . "' class='pull-left' />";
        if ($pr_details->type == 'combo') {
            $this->data['combo_items'] = $this->recipe_model->getrecipeComboItems($id);
        }
		
        $this->data['recipe'] = $pr_details;
		
        $this->data['unit'] = $this->site->getUnitByID($pr_details->unit);
        $this->data['brand'] = $this->site->getBrandByID($pr_details->brand);
        $this->data['images'] = $this->recipe_model->getrecipePhotos($id);
        $this->data['category'] = $this->site->getrecipeCategoryByID($pr_details->category_id);
        $this->data['subcategory'] = $pr_details->subcategory_id ? $this->site->getrecipeCategoryByID($pr_details->subcategory_id) : NULL;
        $this->data['tax_rate'] = $pr_details->tax_rate ? $this->site->getTaxRateByID($pr_details->tax_rate) : NULL;
        $this->data['popup_attributes'] = $this->popup_attributes;
        $this->data['warehouses'] = $this->recipe_model->getAllWarehousesWithPQ($id);
        $this->data['options'] = $this->recipe_model->getrecipeOptionsWithWH($id);
        $this->data['variants'] = $this->recipe_model->getrecipeOptions($id);
		
		
		
        $this->data['sold'] = $this->recipe_model->getSoldQty($id);
		
        //$this->data['purchased'] = $this->recipe_model->getPurchasedQty($id);

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('recipe'), 'page' => lang('recipe')), array('link' => '#', 'page' => $pr_details->name));
		
        $meta = array('page_title' => $pr_details->name, 'bc' => $bc);
		
        $this->page_construct('recipe/view', $meta, $this->data);
		
    }

    function pdf($id = NULL, $view = NULL)
    {
        $this->sma->checkPermissions('index');

        $pr_details = $this->recipe_model->getrecipeByID($id);
        if (!$id || !$pr_details) {
            $this->session->set_flashdata('error', lang('prduct_not_found'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $this->data['barcode'] = "<img src='" . admin_url('recipe/gen_barcode/' . $pr_details->code . '/' . $pr_details->barcode_symbology . '/40/0') . "' alt='" . $pr_details->code . "' class='pull-left' />";
        if ($pr_details->type == 'combo') {
            $this->data['combo_items'] = $this->recipe_model->getrecipeComboItems($id);
        }
        $this->data['recipe'] = $pr_details;
        $this->data['unit'] = $this->site->getUnitByID($pr_details->unit);
        $this->data['brand'] = $this->site->getBrandByID($pr_details->brand);
        $this->data['images'] = $this->recipe_model->getrecipePhotos($id);
        $this->data['category'] = $this->site->getrecipeCategoryByID($pr_details->category_id);
        $this->data['subcategory'] = $pr_details->subcategory_id ? $this->site->getrecipeCategoryByID($pr_details->subcategory_id) : NULL;
        $this->data['tax_rate'] = $pr_details->tax_rate ? $this->site->getTaxRateByID($pr_details->tax_rate) : NULL;
        $this->data['popup_attributes'] = $this->popup_attributes;
        $this->data['warehouses'] = $this->recipe_model->getAllWarehousesWithPQ($id);
        $this->data['options'] = $this->recipe_model->getrecipeOptionsWithWH($id);
        $this->data['variants'] = $this->recipe_model->getrecipeOptions($id);

        $name = $pr_details->code . '_' . str_replace('/', '_', $pr_details->name) . ".pdf";
        if ($view) {
            $this->load->view($this->theme . 'recipe/pdf', $this->data);
        } else {
            $html = $this->load->view($this->theme . 'recipe/pdf', $this->data, TRUE);
            if (! $this->Settings->barcode_img) {
                $html = preg_replace("'\<\?xml(.*)\?\>'", '', $html);
            }
            $this->sma->generate_pdf($html, $name);
        }
    }

    

    function recipe_actions($wh = NULL)
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
                    $this->session->set_flashdata('message', $this->lang->line("recipe_quantity_sync"));
                    redirect($_SERVER["HTTP_REFERER"]);

                } elseif ($this->input->post('form_action') == 'delete') {

                    $this->sma->checkPermissions('delete');
                    foreach ($_POST['val'] as $id) {
                        $this->recipe_model->deleterecipe($id);
                    }
                    $this->session->set_flashdata('message', $this->lang->line("recipe_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);

                } elseif ($this->input->post('form_action') == 'labels') {

                    foreach ($_POST['val'] as $id) {
                        $row = $this->recipe_model->getrecipeByID($id);
                        $selected_variants = false;
                        if ($variants = $this->recipe_model->getrecipeOptions($row->id)) {
                            foreach ($variants as $variant) {
                                $selected_variants[$variant->id] = $variant->quantity > 0 ? 1 : 0;
                            }
                        }
                        $pr[$row->id] = array('id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => $row->quantity, 'variants' => $variants, 'selected_variants' => $selected_variants);
                    }

                    $this->data['items'] = isset($pr) ? json_encode($pr) : false;
                    $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
                    $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('recipe'), 'page' => lang('recipe')), array('link' => '#', 'page' => lang('print_barcodes')));
                    $meta = array('page_title' => lang('print_barcodes'), 'bc' => $bc);
                    $this->page_construct('recipe/print_barcodes', $meta, $this->data);

                } elseif ($this->input->post('form_action') == 'export_excel') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle('recipe');
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
                    $this->excel->getActiveSheet()->SetCellValue('P1', lang('recipe_variants'));
                    $this->excel->getActiveSheet()->SetCellValue('Q1', lang('pcf1'));
                    $this->excel->getActiveSheet()->SetCellValue('R1', lang('pcf2'));
                    $this->excel->getActiveSheet()->SetCellValue('S1', lang('pcf3'));
                    $this->excel->getActiveSheet()->SetCellValue('T1', lang('pcf4'));
                    $this->excel->getActiveSheet()->SetCellValue('U1', lang('pcf5'));
                    $this->excel->getActiveSheet()->SetCellValue('V1', lang('pcf6'));
                    $this->excel->getActiveSheet()->SetCellValue('W1', lang('quantity'));
					$this->excel->getActiveSheet()->SetCellValue('X1', lang('khmer_name'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $recipe = $this->recipe_model->getrecipeDetail($id);
                        $brand = $this->site->getBrandByID($recipe->brand);
                        $base_unit = $sale_unit = $purchase_unit = '';
                        if($units = $this->site->getUnitsByBUID($recipe->unit)) {
                            foreach($units as $u) {
                                if ($u->id == $recipe->unit) {
                                    $base_unit = $u->code;
                                }
                                if ($u->id == $recipe->sale_unit) {
                                    $sale_unit = $u->code;
                                }
                                if ($u->id == $recipe->purchase_unit) {
                                    $purchase_unit = $u->code;
                                }
                            }
                        }
                        $variants = $this->recipe_model->getrecipeOptions($id);
                        $recipe_variants = '';
                        if ($variants) {
                            foreach ($variants as $variant) {
                                $recipe_variants .= trim($variant->name) . '|';
                            }
                        }
                        $quantity = $recipe->quantity;
                        if ($wh) {
                            if($wh_qty = $this->recipe_model->getrecipeQuantity($id, $wh)) {
                                $quantity = $wh_qty['quantity'];
                            } else {
                                $quantity = 0;
                            }
                        }
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $recipe->name);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $recipe->code);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $recipe->barcode_symbology);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, ($brand ? $brand->name : ''));
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $recipe->category_code);
                        $this->excel->getActiveSheet()->SetCellValue('F' . $row, $base_unit);
                        $this->excel->getActiveSheet()->SetCellValue('G' . $row, $sale_unit);
                        $this->excel->getActiveSheet()->SetCellValue('H' . $row, $purchase_unit);
                        if ($this->Owner || $this->Admin || $this->session->userdata('show_cost')) {
                            $this->excel->getActiveSheet()->SetCellValue('I' . $row, $recipe->cost);
                        }
                        if ($this->Owner || $this->Admin || $this->session->userdata('show_price')) {
                            $this->excel->getActiveSheet()->SetCellValue('J' . $row, $recipe->price);
                        }
                        $this->excel->getActiveSheet()->SetCellValue('K' . $row, $recipe->alert_quantity);
                        $this->excel->getActiveSheet()->SetCellValue('L' . $row, $recipe->tax_rate_name);
                        $this->excel->getActiveSheet()->SetCellValue('M' . $row, $recipe->tax_method ? lang('exclusive') : lang('inclusive'));
                        $this->excel->getActiveSheet()->SetCellValue('N' . $row, $recipe->image);
                        $this->excel->getActiveSheet()->SetCellValue('O' . $row, $recipe->subcategory_code);
                        $this->excel->getActiveSheet()->SetCellValue('P' . $row, $recipe_variants);
                        $this->excel->getActiveSheet()->SetCellValue('Q' . $row, $recipe->cf1);
                        $this->excel->getActiveSheet()->SetCellValue('R' . $row, $recipe->cf2);
                        $this->excel->getActiveSheet()->SetCellValue('S' . $row, $recipe->cf3);
                        $this->excel->getActiveSheet()->SetCellValue('T' . $row, $recipe->cf4);
                        $this->excel->getActiveSheet()->SetCellValue('U' . $row, $recipe->cf5);
                        $this->excel->getActiveSheet()->SetCellValue('V' . $row, $recipe->cf6);
                        $this->excel->getActiveSheet()->SetCellValue('W' . $row, $quantity);
						$this->excel->getActiveSheet()->SetCellValue('X' . $row, $recipe->khmer_name);
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
                    $filename = 'recipe_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
            } else {
                $this->session->set_flashdata('error', $this->lang->line("no_recipe_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'admin/recipe');
        }
    }

    public function delete_image($id = NULL)
    {
        $this->sma->checkPermissions('edit', true);
        if ($id && $this->input->is_ajax_request()) {
            header('Content-Type: application/json');
            $this->db->delete('recipe_photos', array('id' => $id));
            $this->sma->send_json(array('error' => 0, 'msg' => lang("image_deleted")));
        }
        $this->sma->send_json(array('error' => 1, 'msg' => lang("ajax_error")));
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

    public function qa_suggestions()
    {
        $term = $this->input->get('term', true);

        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('welcome') . "'; }, 10);</script>");
        }

        $analyzed = $this->sma->analyze_term($term);
        $sr = $analyzed['term'];
        $option_id = $analyzed['option_id'];

        $rows = $this->recipe_model->getQASuggestions($sr);
        if ($rows) {
            foreach ($rows as $row) {
                $row->qty = 1;
                $options = $this->recipe_model->getrecipeOptions($row->id);
                $row->option = $option_id;
                $row->serial = '';

                $pr[] = array('id' => str_replace(".", "", microtime(true)), 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")",
                    'row' => $row, 'options' => $options);

            }
            $this->sma->send_json($pr);
        } else {
            $this->sma->send_json(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
        }
    }

    function adjustment_actions()
    {
        if (!$this->Owner && !$this->GP['bulk_actions']) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {

                    $this->sma->checkPermissions('delete');
                    foreach ($_POST['val'] as $id) {
                        $this->recipe_model->deleteAdjustment($id);
                    }
                    $this->session->set_flashdata('message', $this->lang->line("adjustment_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);

                } elseif ($this->input->post('form_action') == 'export_excel') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle('quantity_adjustments');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference_no'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('warehouse'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('created_by'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('note'));
                    $this->excel->getActiveSheet()->SetCellValue('F1', lang('items'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $adjustment = $this->recipe_model->getAdjustmentByID($id);
                        $created_by = $this->site->getUser($adjustment->created_by);
                        $warehouse = $this->site->getWarehouseByID($adjustment->warehouse_id);
                        $items = $this->recipe_model->getAdjustmentItems($id);
                        $recipe = '';
                        if ($items) {
                            foreach ($items as $item) {
                                $recipe .= $item->recipe_name.'('.$this->sma->formatQuantity($item->type == 'subtraction' ? -$item->quantity : $item->quantity).')'."\n";
                            }
                        }

                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($adjustment->date));
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $adjustment->reference_no);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $warehouse->name);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $created_by->first_name.' ' .$created_by->last_name);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $this->sma->decode_html($adjustment->note));
                        $this->excel->getActiveSheet()->SetCellValue('F' . $row, $recipe);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
                    $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'quantity_adjustments_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', $this->lang->line("no_record_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    function stock_counts($warehouse_id = NULL)
    {
        $this->sma->checkPermissions('stock_count');

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

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('recipe'), 'page' => lang('recipe')), array('link' => '#', 'page' => lang('stock_counts')));
        $meta = array('page_title' => lang('stock_counts'), 'bc' => $bc);
        $this->page_construct('recipe/stock_counts', $meta, $this->data);
    }

    function getCounts($warehouse_id = NULL)
    {
        $this->sma->checkPermissions('stock_count', TRUE);

        if ((! $this->Owner || ! $this->Admin) && ! $warehouse_id) {
            $user = $this->site->getUser();
            $warehouse_id = $user->warehouse_id;
        }
        $detail_link = anchor('admin/recipe/view_count/$1', '<label class="label label-primary pointer">'.lang('details').'</label>', 'class="tip" title="'.lang('details').'" data-toggle="modal" data-target="#myModal"');

        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('stock_counts')}.id as id, date, reference_no, {$this->db->dbprefix('warehouses')}.name as wh_name, type, brand_names, category_names, initial_file, final_file")
            ->from('stock_counts')
            ->join('warehouses', 'warehouses.id=stock_counts.warehouse_id', 'left');
        if ($warehouse_id) {
            $this->datatables->where('warehouse_id', $warehouse_id);
        }

        $this->datatables->add_column('Actions', '<div class="text-center">'.$detail_link.'</div>', "id");
        echo $this->datatables->generate();
    }

    function view_count($id)
    {
        $this->sma->checkPermissions('stock_count', TRUE);
        $stock_count = $this->recipe_model->getStouckCountByID($id);
        if ( ! $stock_count->finalized) {
            $this->sma->md('admin/recipe/finalize_count/'.$id);
        }

        $this->data['stock_count'] = $stock_count;
        $this->data['stock_count_items'] = $this->recipe_model->getStockCountItems($id);
        $this->data['warehouse'] = $this->site->getWarehouseByID($stock_count->warehouse_id);
        $this->data['adjustment'] = $this->recipe_model->getAdjustmentByCountID($id);
        $this->load->view($this->theme.'recipe/view_count', $this->data);
    }

    function count_stock($page = NULL)
    {
        $this->sma->checkPermissions('stock_count');
        $this->form_validation->set_rules('warehouse', lang("warehouse"), 'required');
        $this->form_validation->set_rules('type', lang("type"), 'required');

        if ($this->form_validation->run() == true) {

            $warehouse_id = $this->input->post('warehouse');
            $type = $this->input->post('type');
            $categories = $this->input->post('category') ? $this->input->post('category') : NULL;
            $brands = $this->input->post('brand') ? $this->input->post('brand') : NULL;
            $this->load->helper('string');
            $name = random_string('md5').'.csv';
            $recipe = $this->recipe_model->getStockCountrecipe($warehouse_id, $type, $categories, $brands);
            $pr = 0; $rw = 0;
            foreach ($recipe as $recipe) {
                if ($variants = $this->recipe_model->getStockCountrecipeVariants($warehouse_id, $recipe->id)) {
                    foreach ($variants as $variant) {
                        $items[] = array(
                            'recipe_code' => $recipe->code,
                            'recipe_name' => $recipe->name,
                            'variant' => $variant->name,
                            'expected' => $variant->quantity,
                            'counted' => ''
                            );
                        $rw++;
                    }
                } else {
                    $items[] = array(
                        'recipe_code' => $recipe->code,
                        'recipe_name' => $recipe->name,
                        'variant' => '',
                        'expected' => $recipe->quantity,
                        'counted' => ''
                        );
                    $rw++;
                }
                $pr++;
            }
            if ( ! empty($items)) {
                $csv_file = fopen('./files/'.$name, 'w');
                fputcsv($csv_file, array(lang('recipe_code'), lang('recipe_name'), lang('variant'), lang('expected'), lang('counted')));
                foreach ($items as $item) {
                    fputcsv($csv_file, $item);
                }
                // file_put_contents('./files/'.$name, $csv_file);
                // fwrite($csv_file, $txt);
                fclose($csv_file);
            } else {
                $this->session->set_flashdata('error', lang('no_recipe_found'));
                redirect($_SERVER["HTTP_REFERER"]);
            }

            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld($this->input->post('date'));
            } else {
                $date = date('Y-m-d H:s:i');
            }
            $category_ids = '';
            $brand_ids = '';
            $category_names = '';
            $brand_names = '';
            if ($categories) {
                $r = 1; $s = sizeof($categories);
                foreach ($categories as $category_id) {
                    $category = $this->site->getrecipeCategoryByID($category_id);
                    if ($r == $s) {
                        $category_names .= $category->name;
                        $category_ids .= $category->id;
                    } else {
                        $category_names .= $category->name.', ';
                        $category_ids .= $category->id.', ';
                    }
                    $r++;
                }
            }
            if ($brands) {
                $r = 1; $s = sizeof($brands);
                foreach ($brands as $brand_id) {
                    $brand = $this->site->getBrandByID($brand_id);
                    if ($r == $s) {
                        $brand_names .= $brand->name;
                        $brand_ids .= $brand->id;
                    } else {
                        $brand_names .= $brand->name.', ';
                        $brand_ids .= $brand->id.', ';
                    }
                    $r++;
                }
            }
            $data = array(
                'date' => $date,
                'warehouse_id' => $warehouse_id,
                'reference_no' => $this->input->post('reference_no'),
                'type' => $type,
                'categories' => $category_ids,
                'category_names' => $category_names,
                'brands' => $brand_ids,
                'brand_names' => $brand_names,
                'initial_file' => $name,
                'recipe' => $pr,
                'rows' => $rw,
                'created_by' => $this->session->userdata('user_id')
            );

        }

        if ($this->form_validation->run() == true && $this->recipe_model->addStockCount($data)) {
            $this->session->set_flashdata('message', lang("stock_count_intiated"));
            admin_redirect('recipe/stock_counts');

        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['categories'] = $this->site->getAllrecipeCategories();
            $this->data['brands'] = $this->site->getAllBrands();
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('recipe'), 'page' => lang('recipe')), array('link' => '#', 'page' => lang('count_stock')));
            $meta = array('page_title' => lang('count_stock'), 'bc' => $bc);
            $this->page_construct('recipe/count_stock', $meta, $this->data);

        }

    }

    function finalize_count($id)
    {
        $this->sma->checkPermissions('stock_count');
        $stock_count = $this->recipe_model->getStouckCountByID($id);
        if ( ! $stock_count || $stock_count->finalized) {
            $this->session->set_flashdata('error', lang("stock_count_finalized"));
            admin_redirect('recipe/stock_counts');
        }

        $this->form_validation->set_rules('count_id', lang("count_stock"), 'required');

        if ($this->form_validation->run() == true) {

            if ($_FILES['csv_file']['size'] > 0) {
                $note = $this->sma->clear_tags($this->input->post('note'));
                $data = array(
                    'updated_by' => $this->session->userdata('user_id'),
                    'updated_at' => date('Y-m-d H:s:i'),
                    'note' => $note
                );

                $this->load->library('upload');
                $config['upload_path'] = $this->digital_upload_path;
                $config['allowed_types'] = 'csv';
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = false;
                $config['encrypt_name'] = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('csv_file')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                $csv = $this->upload->file_name;

                $arrResult = array();
                $handle = fopen($this->digital_upload_path . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);
                $keys = array('recipe_code', 'recipe_name', 'recipe_variant', 'expected', 'counted');
                $final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                // $this->sma->print_arrays($final);
                $rw = 2; $differences = 0; $matches = 0;
                foreach ($final as $pr) {
                    if ($recipe = $this->recipe_model->getrecipeByCode(trim($pr['recipe_code']))) {
                        $pr['counted'] = !empty($pr['counted']) ? $pr['counted'] : 0;
                        if ($pr['expected'] == $pr['counted']) {
                            $matches++;
                        } else {
                            $pr['stock_count_id'] = $id;
                            $pr['recipe_id'] = $recipe->id;
                            $pr['cost'] = $recipe->cost;
                            $pr['recipe_variant_id'] = empty($pr['recipe_variant']) ? NULL : $this->recipe_model->getrecipeVariantID($pr['recipe_id'], $pr['recipe_variant']);
                            $recipe[] = $pr;
                            $differences++;
                        }
                    } else {
                        $this->session->set_flashdata('error', lang('check_recipe_code') . ' (' . $pr['recipe_code'] . '). ' . lang('recipe_code_x_exist') . ' ' . lang('line_no') . ' ' . $rw);
                        admin_redirect('recipe/finalize_count/'.$id);
                    }
                    $rw++;
                }

                $data['final_file'] = $csv;
                $data['differences'] = $differences;
                $data['matches'] = $matches;
                $data['missing'] = $stock_count->rows-($rw-2);
                $data['finalized'] = 1;
            }

            // $this->sma->print_arrays($data, $recipe);
        }

        if ($this->form_validation->run() == true && $this->recipe_model->finalizeStockCount($id, $data, $recipe)) {
            $this->session->set_flashdata('message', lang("stock_count_finalized"));
            admin_redirect('recipe/stock_counts');
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['stock_count'] = $stock_count;
            $this->data['warehouse'] = $this->site->getWarehouseByID($stock_count->warehouse_id);
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('recipe'), 'page' => lang('recipe')), array('link' => admin_url('recipe/stock_counts'), 'page' => lang('stock_counts')), array('link' => '#', 'page' => lang('finalize_count')));
            $meta = array('page_title' => lang('finalize_count'), 'bc' => $bc);
            $this->page_construct('recipe/finalize_count', $meta, $this->data);

        }

    }
    function activate_old($id)
        {
           $this->recipe_model->activate($id);
           redirect($_SERVER["HTTP_REFERER"]);
        }    


    function activate($id = NULL)
    {
        

        // $this->sma->checkPermissions('edit');
       
        $this->form_validation->set_rules('confirm', lang("confirm"), 'required');

        if ($this->form_validation->run() == FALSE) {
            if ($this->input->post('deactivate')) {
                $this->session->set_flashdata('error', validation_errors());
                redirect($_SERVER["HTTP_REFERER"]);
            } else {               
                $this->data['recipe'] = $this->recipe_model->getrecipeByID($id);
                $this->data['modal_js'] = $this->site->modal_js();
                $this->load->view($this->theme . 'recipe/activate_recipe', $this->data);
            }
        } else {            
            if ($this->input->post('confirm') == 'yes') {                
                if ($id != $this->input->post('id')) {                  
                    show_error(lang('error_csrf'));
                }
                // if ($this->Owner) {                                        
                     $this->recipe_model->activate($id);
                    
                // }
            }
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }        
    function deactivate($id = NULL)
    {
       // $this->sma->checkPermissions('edit');
       
        $this->form_validation->set_rules('confirm', lang("confirm"), 'required');

        if ($this->form_validation->run() == FALSE) {
            if ($this->input->post('deactivate')) {
                $this->session->set_flashdata('error', validation_errors());
                redirect($_SERVER["HTTP_REFERER"]);
            } else {
                $this->data['recipe'] = $this->recipe_model->getrecipeByID($id);
                $this->data['modal_js'] = $this->site->modal_js();
                $this->load->view($this->theme . 'recipe/deactivate_recipe', $this->data);
            }
        } else {
            if ($this->input->post('confirm') == 'yes') {
                if ($id != $this->input->post('id')) {
                    show_error(lang('error_csrf'));
                }
                // if ($this->Owner) {
                    
                    $this->recipe_model->deactivate($id);
                    
                // }
            }
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
    function base64ToImage($imageData,$filename){
    $data = 'data:image/png;base64,AAAFBfj42Pj4';
    list($type, $imageData) = explode(';', $imageData);
    list(,$extension) = explode('/',$type);
    list(,$imageData)      = explode(',', $imageData);
    //$fileName = uniqid().'.'.$extension;
    $imageData = base64_decode($imageData);
    file_put_contents($filename, $imageData);
}
/*recipe variant start*/

   function varients()
    {
    $this->sma->checkPermissions();
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('varients')));
        $meta = array('page_title' => lang('varients'), 'bc' => $bc);
        $this->page_construct('recipe/varients', $meta, $this->data);
    }
    function getVarients()
    {
    $this->sma->checkPermissions('categories');
        
        $this->load->library('datatables');
        $this->datatables
            ->select("'sno',{$this->db->dbprefix('recipe_variants')}.id as id,  {$this->db->dbprefix('recipe_variants')}.name, {$this->db->dbprefix('recipe_variants')}.native_name ", FALSE)
            ->from("recipe_variants")
            ->add_column("Actions", "<div class=\"text-center\"> <a href='" . admin_url('recipe/edit_varient/$1') . "'  class='tip' title='" . lang("edit_varient") . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_category") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('recipe/delete_varient/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", "id");

        echo $this->datatables->generate();
    }
   function add_varient()
    {
    $this->sma->checkPermissions();
        $this->load->helper('security');
        $this->form_validation->set_rules('name', lang("name"), 'required|min_length[3]|is_unique[recipe_variants.name]');
    $this->form_validation->set_rules('variant_code', lang("variant_code"), 'required|min_length[3]|is_unique[recipe_variants.variant_code]');
        $this->form_validation->set_rules('native_name', lang("native_name"), 'required');
        if ($this->form_validation->run() == true) {
            $data = array(
                'name' => $this->input->post('name'),
                'native_name' => $this->input->post('native_name'),
        'variant_code' => $this->input->post('variant_code')
            );

            

        } elseif ($this->input->post('add_varient')) {
            $error = validation_errors();
            $response['error'] = $error;
            echo json_encode($response);exit;
        }
        if ($this->form_validation->run() == true && $id = $this->recipe_model->add_varient($data)) {
            $data['id'] = $id;
            $response['varient'] = $data;
         $this->session->set_flashdata('message', lang("Varient_added"));
            echo json_encode($response);exit;
            //admin_redirect("recipe/varients");
        } else {
            
            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'recipe/add_varient', $this->data);

        }
    }
    function delete_varient($id){
    $this->recipe_model->delete_varient($id);
    $this->session->set_flashdata('message', lang("Varient_added"));
    $this->sma->send_json(array('error' => 0, 'msg' => lang("varient_deleted")));
    }
    function search_varients(){
    $term = $this->input->post('term');
    $existing = $this->input->post('existing');
    $result = $this->recipe_model->getVarients($term,$existing);
    $this->sma->send_json($result);
    }
    function deleteRecipeVariant($id){
    $this->recipe_model->deleteRecipeVariant($id);
    echo json_encode(array('status'=>'success'));exit;
    }
     function edit_varient($id)
    {
    $this->sma->checkPermissions();
        $this->load->helper('security');
        $this->form_validation->set_rules('name', lang("name"), 'required|min_length[3]|callback_my_is_unique[recipe_variants.name.'.$id.']');
    $this->form_validation->set_rules('variant_code', lang("variant_code"), 'required|min_length[3]|callback_my_is_unique[recipe_variants.variant_code.'.$id.']');
        $this->form_validation->set_rules('native_name', lang("native_name"), 'required');
        if ($this->form_validation->run() == true) {
            $data = array(
                'name' => $this->input->post('name'),
                'native_name' => $this->input->post('native_name'),
        'variant_code' => $this->input->post('variant_code')
            );

            

        } elseif ($this->input->post('edit_varient')) {
            $error = validation_errors();
            $response['error'] = $error;
        $this->session->set_flashdata('error', $error);
            echo json_encode($response);exit;
        }
        if ($this->form_validation->run() == true &&  $this->recipe_model->update_varient($id,$data)) {
            $data['id'] = $id;
            $response['varient'] = $data;
        $this->session->set_flashdata('message', lang("Varient_updated"));
            echo json_encode($response);exit;
            //admin_redirect("recipe/varients");
        } else {
            
            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
        $this->data['variant'] = $this->recipe_model->getVariantbyID($id);
           $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('recipe/variants'), 'page' => lang('variants')), array('link' => '#', 'page' => lang('edit_varient')));
            $meta = array('page_title' => lang('edit_varient'), 'bc' => $bc);
            $this->page_construct('recipe/edit_varient', $meta, $this->data);
        }
    }
    function search_purchase_items(){
    $term = $this->input->post('term');
    $type = $this->input->post('item_type');
    $existing = $this->input->post('existing');
    //echo '<pre>';print_r($_POST);exit;
    $result = $this->recipe_model->getPurchase_items($term,$existing,$type);
    $this->sma->send_json($result);
    }
     function delete_purchase_item($id){
    $this->recipe_model->delete_purchase_item($id);
    echo json_encode(array('status'=>'success'));exit;
    }
    function stock($rid){
	$id =$rid;
	$this->data['recipe'] = $this->recipe_model->getrecipeByID($id);
	$this->data['id'] = $id;
	$this->data['modal_js'] = $this->site->modal_js();
	$this->load->view($this->theme . 'recipe/stock', $this->data);
    }
function stock_details($id)
    {
       
        
        $this->load->library('datatables');
	    
        $this->datatables
                ->select("'sno',".$this->db->dbprefix('warehouses') . ".name as store_name,stock_in,stock_out,batch,cost_price,selling_price,expiry_date", FALSE)
                ->from('pro_stock_master')
		->join('warehouses','warehouses.id=pro_stock_master.store_id')
                ->where('product_id',$id);
        
        
		
        echo $this->datatables->generate();
    }
/*recipe variant end*/
    function getRecipeCategories($type){
	if($type=="standard" || $type=="production" || $type=="combo"){
	    if ($rows = $this->recipe_model->getrecipeCategories()) {
			
		$data = json_encode($rows);
	    } else {
		$data = false;
			    
	    }
	    echo $data;
	}else{
	    if ($rows = $this->recipe_model->getPurchaseCategories()) {
			
		$data = json_encode($rows);
	    } else {
		$data = false;
			    
	    }
	    echo $data;
	}
	
    }
    function getrecipeSubCategories($type,$category_id = NULL)
    {
	if($type=="standard" || $type=="production" || $type=="combo"){
	    if ($rows = $this->recipe_model->getrecipeSubCategories($category_id)) {
			    
		$data = json_encode($rows);
	    } else {
		$data = false;
			    
	    }
	    echo $data;
	}else{
	    if ($rows = $this->recipe_model->getPurchaseSubCategories($category_id)) {
			    
		$data = json_encode($rows);
	    } else {
		$data = false;
			    
	    }
	    echo $data;
	}
    }
}
