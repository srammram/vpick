<?php defined('BASEPATH') or exit('No direct script access allowed');

class Purchase_orders extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        if ($this->Customer) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $this->lang->admin_load('procurment/purchase_orders', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('procurment/purchase_orders_model');
        $this->digital_upload_path = 'files/';
        $this->upload_path = 'assets/uploads/';
        $this->thumbs_path = 'assets/uploads/thumbs/';
        $this->image_types = 'gif|jpg|jpeg|png|tif';
        $this->digital_upload_path = 'assets/uploads/procurment/po/';
        $this->digital_file_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
        $this->allowed_file_size = '1024';
	if (!file_exists($this->digital_upload_path)) {
		mkdir($this->digital_upload_path, 0777, true);
	}
        $this->data['logo'] = true;
	
		$this->Muser_id = $this->session->userdata('user_id');
		$this->Maccess_id = 8;

    }
	
	public function purchase_orders_list(){
		$poref =  $this->input->get('poref');
		
		$data['purchase_orders'] = $this->purchase_orders_model->getQuotationByID($poref);
		$q_request = $this->purchase_orders_model->getRequestByRef($data['purchase_orders']->req_reference_no);
		$stores = $this->purchase_orders_model->getStoresByIDs($q_request->store_request_ids);
		$inv_items = $this->purchase_orders_model->getAllRequestItems($poref);
		/*print_r($inv_items);die;*/
		 krsort($inv_items);
		$c = rand(100000, 9999999);
		foreach ($inv_items as $item) {
			$row = $this->siteprocurment->getRecipeByID($item->product_id);
			$row->expiry = (($item->expiry && $item->expiry != '0000-00-00') ? $this->sma->hrsd($item->expiry) : '');
			$row->mfg = (($item->mfg && $item->mfg != '0000-00-00') ? $this->sma->hrsd($item->mfg) : '');
			
			$row->batch_no = $item->batch_no ? $item->batch_no : '';
						
			$row->base_quantity = $item->quantity;
			$row->base_unit = $row->unit ? $row->unit : $item->product_unit_id;
			$row->base_unit_cost = $row->cost ? $row->cost : $item->unit_cost;
			
			$row->unit_cost = $row->cost;
			$row->real_unit_cost = $row->cost;
			$row->unit = $item->product_unit_id;
			$row->qty = $item->unit_quantity;
			$row->oqty = $item->quantity;
			$row->supplier_part_no = $item->supplier_part_no;
			$row->received = $item->quantity_received ? $item->quantity_received : $item->quantity;
			$row->quantity_balance = $item->quantity_balance + ($item->quantity-$row->received);
			// $row->discount = $item->discount ? $item->discount : '0';
			$options = $this->purchase_orders_model->getProductOptions($row->id);
			$row->option = $item->option_id;
            //$row->real_unit_cost = $item->unit_cost;
            $row->cost = $this->sma->formatDecimal($item->net_unit_price + ($item->item_discount / $item->quantity));

            $row->new_entry = 1;
            $row->expiry = '';
            // $row->qty = 1;
            $row->quantity_balance = '';

            $row->item_discount_percent = '0';
            $row->item_discount_amt = '0';
            $row->item_bill_discount = '0';
            $row->item_tax_rate = '0';
            $row->item_selling_price = '0';
            $row->discount = '0';
	    $row->item_dis_type='p';
                $row->tax_rate = $item->tax_rate_id;
				 $row->tax_method = $item->item_tax_method;
                unset($row->details, $row->product_details, $row->price, $row->file, $row->product_group_id);
                $units = $this->siteprocurment->getUnitsByBUID($row->base_unit);
                $tax_rate = $this->siteprocurment->getTaxRateByID($row->tax_rate);
			$ri = $this->Settings->item_addition ? $row->id : $row->id;
		
			$pr[$ri.'_'.$item->store_id] = array('id' => $c,'store_id'=>$item->store_id, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")",
				'row' => $row, 'stores'=>$stores, 'tax_rate' => $row->tax_rate, 'units' => $units, 'options' => $options);
			$c++;
		}
		
		$data['purchase_ordersitem'] = $pr;
		
		
		
		if(!empty($data)){
			$response['status'] = 'success';
			$response['value'] = $data;
		}else{
			$response['status'] = 'error';
			$response['value'] = '';
		}
		echo json_encode($response);
		exit;
	}

	public function supplier(){
		
		$supplier_id =  $this->input->get('supplier_id');
		$data = $this->purchase_orders_model->getSupplierdetails($supplier_id);
		
		if(!empty($data)){
			$response['supplier_name'] = $data->name;
			$response['supplier_code'] = $data->ref_id;
			$response['supplier_vatno'] = $data->vat_no;
			$response['supplier_address'] = $data->address.' '.$data->city.' '.$data->state.' '.$data->country;
			$response['supplier_email'] = $data->email;
			$response['supplier_phno'] = $data->phone;
		}else{
			$response['supplier_name'] = '';
			$response['supplier_code'] = '';
			$response['supplier_vatno'] = '';
			$response['supplier_address'] = '';
			$response['supplier_email'] = '';
			$response['supplier_phno'] = '';
		}
		echo json_encode($response);
		exit;
	}
    /* ------------------------------------------------------------------------- */

    public function index($warehouse_id = null)
    {
         
       // //$this->sma->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
            $this->data['warehouses'] = $this->siteprocurment->getAllWarehouses();
            $this->data['warehouse_id'] = $warehouse_id;
            $this->data['warehouse'] = $warehouse_id ? $this->siteprocurment->getWarehouseByID($warehouse_id) : null;
        } else {
            $this->data['warehouses'] = null;
            $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
            $this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->siteprocurment->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        }

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('purchase_orders')));
        $meta = array('page_title' => lang('purchase_orders'), 'bc' => $bc);
        $this->page_construct('procurment/purchase_orders/index', $meta, $this->data);

    }

    public function getPurchase_orders($warehouse_id = null)
    { 

               
        $detail_link = anchor('admin/procurment/purchase_orders/view/$1', '<i class="fa fa-file-text-o"></i> ' . lang('purchase_orders_details'));
        $payments_link = anchor('admin/procurment/purchase_orders/payments/$1', '<i class="fa fa-money"></i> ' . lang('view_payments'), 'data-toggle="modal" data-target="#myModal"');
        $add_payment_link = anchor('admin/procurment/purchase_orders/add_payment/$1', '<i class="fa fa-money"></i> ' . lang('add_payment'), 'data-toggle="modal" data-target="#myModal"');
        $email_link = anchor('admin/procurment/purchase_orders/email/$1', '<i class="fa fa-envelope"></i> ' . lang('email_purchase_orders'), 'data-toggle="modal" data-target="#myModal"');
        $edit_link = anchor('admin/procurment/purchase_orders/edit/$1', '<i class="fa fa-edit"></i> ' . lang('edit_purchase_orders'));
        $view_link = '<a href="'.admin_url('procurment/purchase_orders/view/$1').'" data-toggle="modal" data-target="#myModal"><i class="fa fa-edit"></i>'.lang('view_purchase_orders').'</a>';
        $pdf_link = anchor('admin/procurment/purchase_orders/pdf/$1', '<i class="fa fa-file-pdf-o"></i> ' . lang('download_pdf'));
        $print_barcode = anchor('admin/procurment/products/print_barcodes/?purchase=$1', '<i class="fa fa-print"></i> ' . lang('print_barcodes'));
        $return_link = anchor('admin/procurment/purchase_orders/return_purchase/$1', '<i class="fa fa-angle-double-left"></i> ' . lang('return_purchase'));
        $delete_link = "<a href='#' class='po' title='<b>" . $this->lang->line("delete_quotation") . "</b>' data-content=\"<p>"
        . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('procurment/purchase_orders/delete/$1') . "'>"
        . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
        . lang('delete_purchase_orders') . "</a>";
       /* $action = '<div class="text-center"><div class="btn-group text-left">'
        . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
        . lang('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
            <li>' . $detail_link . '</li>            
            <li>' . $edit_link . '</li>
            <li>' . $pdf_link . '</li>
            <li>' . $email_link . '</li>            
            <li>' . $delete_link . '</li>
        </ul>
    </div></div>';*/
	 $action = '<div class="text-center"><div class="btn-group text-left">'
        . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
        . lang('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
            <li>' . $edit_link . '</li>
	    <li>' . $view_link . '</li>
            <li>' . $delete_link . '</li>
        </ul>
    </div></div>';
        //$action = '<div class="text-center">' . $detail_link . ' ' . $edit_link . ' ' . $email_link . ' ' . $delete_link . '</div>';
// echo "string";exit;
        $this->load->library('datatables');
        if ($warehouse_id) {        
        // echo "fg";exit;    
            $this->datatables
                ->select("id, DATE_FORMAT(date, '%Y-%m-%d %T') as date, reference_no, req_reference_no, supplier, total, total_discount, total_tax, grand_total, status,attachment")
                ->from('pro_purchase_orders')
                ->where('warehouse_id', $warehouse_id);
        } else {
            // echo "sdsd";exit;
            $this->datatables
                ->select("id, DATE_FORMAT(date, '%Y-%m-%d %T') as date, reference_no, req_reference_no, supplier, total, total_discount, total_tax, grand_total, status,attachment")
                ->from('pro_purchase_orders');
                
        }
        // $this->datatables->where('status !=', 'returned');
        /*if (!$this->Customer && !$this->Supplier && !$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $this->datatables->where('created_by', $this->session->userdata('user_id'));
        } elseif ($this->Supplier) {
            $this->datatables->where('customer_id', $this->session->userdata('user_id'));
        }*/
	$this->datatables->edit_column('attachment', '$1__$2', $this->digital_upload_path.', attachment');
        $this->datatables->add_column("Actions", $action, "id");

        echo $this->datatables->generate();
    }

    /* ----------------------------------------------------------------------------- */

    public function modal_view($purchase_orders_id = null)
    {
        //$this->sma->checkPermissions('index', true);

        if ($this->input->get('id')) {
            $purchase_orders_id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $po = $this->purchase_orders_model->getPurchase_ordersByID($purchase_orders_id);
        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($po->created_by, true);
        }
        $this->data['rows'] = $this->purchase_orders_model->getAllPurchase_ordersItems($purchase_orders_id);
        $this->data['supplier'] = $this->siteprocurment->getCompanyByID($po->customer_id);
        $this->data['warehouse'] = $this->siteprocurment->getWarehouseByID($po->warehouse_id);
        $this->data['inv'] = $po;
        $this->data['payments'] = $this->purchase_orders_model->getPaymentsForPurchase($purchase_orders_id);
        $this->data['created_by'] = $this->siteprocurment->getUser($po->created_by);
        $this->data['updated_by'] = $po->updated_by ? $this->siteprocurment->getUser($po->updated_by) : null;
        // $this->data['return_purchase'] = $po->return_id ? $this->purchase_orders_model->getPurchase_ordersByID($po->return_id) : NULL;
        // $this->data['return_rows'] = $po->return_id ? $this->purchase_orders_model->getAllPurchase_ordersItems($po->return_id) : NULL;

        $this->load->view($this->theme . 'purchase_orders/modal_view', $this->data);

    }

    public function view($id = null)
    {
		
      
	$store_id = $this->data['default_store'];
  
		 
        
	$this->data['orders'] =  $this->purchase_orders_model->getPurchase_ordersByID($id);
	$this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
	$this->data['suppliers'] = $this->siteprocurment->getAllCompanies('supplier');
	$this->data['categories'] = $this->siteprocurment->getAllCategories();
	$this->data['currencies'] = $this->siteprocurment->getAllCurrencies();
	$this->data['tax_rates'] = $this->siteprocurment->getAllTaxRates();
	$this->data['warehouses'] = $this->siteprocurment->getAllWarehouses();
	$this->data['purchaseorder'] = array();
	$this->data['requestnumber'] = $this->siteprocurment->getAllQUOTESNUMBERedit();
	$this->data['order_items'] =  $this->purchase_orders_model->getAllPurchase_ordersItems($id);   
    //echo '<pre>';print_R($this->data['order_items']);exit;
      $c=1;
      foreach ($this->data['order_items'] as $item) {
		    
	  $row = $this->siteprocurment->getItemByID($item->product_id);
	  $storeqty = $this->purchase_orders_model->getPO_StoreQty($item->product_id,$id);
	  $row->name = $item->product_name;
	  $row->id = $item->product_id;
	  $row->code = $item->product_code;
	  $row->qty = $item->quantity;
	  $row->quantity_balance = $item->quantity;
	  $row->batch_no = $item->batch_no;
	  $row->expiry = $item->expiry;
	  $row->unit_cost = $item->cost;
	  $row->real_unit_cost = $item->cost;
	  //$row->real_unit_cost = $item->gross;
	  $row->item_discount_percent = $item->item_disc ? $item->item_disc : '0';
	  $row->item_discount_amt = $item->item_disc_amt ? $item->item_disc_amt : '0';
	  $row->item_dis_type = $item->item_dis_type;
	  $row->item_bill_discount = $item->item_bill_disc_amt ? $item->item_bill_disc_amt : '0';
	  $row->tax_rate = $item->item_tax_method;
	  $tax = $this->siteprocurment->getTaxRateByID($item->item_tax_method);
	  $row->tax_rate_val = $tax->rate;
	  $row->item_selling_price =$item->selling_price;
	  
	  
	  $row->base_unit = $row->unit ? $row->unit : $item->product_unit_id;
      
	  $options = $this->purchase_orders_model->getProductOptions($row->id);

	  $units = $this->siteprocurment->getUnitsByBUID($row->base_unit);
	  $ri = $this->Settings->item_addition ? $row->id : $row->id;

	  $pr[$ri.'_'.$item->store_id] = array('id' => $c,'store_id'=>$item->store_id,'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")",
	      'row' => $item, 'storeqty'=>$storeqty,'tax_rate_val' => $row->tax_rate_val,'tax_rate' => $row->tax_rate, 'units' => $units, 'options' => $options);
	  $c++;
      }
	    //echo json_encode($pr);exit;
        $this->data['po_order_items'] = $pr;
         //echo '<pre>';print_R($this->data['po_order_items']);exit;
       
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('procurment/purchase_orders'), 'page' => lang('purchase_orders')), array('link' => '#', 'page' => lang('view')));
        $meta = array('page_title' => lang('view_purchase_orders_details'), 'bc' => $bc);
	$this->load->view($this->theme . 'procurment/purchase_orders/view', $this->data);

    }

    /* ----------------------------------------------------------------------------- */

//generate pdf and force to download

    public function pdf($purchase_orders_id = null, $view = null, $save_bufffer = null)
    {
        //$this->sma->checkPermissions();

        if ($this->input->get('id')) {
            $purchase_orders_id = $this->input->get('id');
        }

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $po = $this->purchase_orders_model->getPurchase_ordersByID($purchase_orders_id);

        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($po->created_by);
        }
        $this->data['rows'] = $this->purchase_orders_model->getAllPurchase_ordersItems($purchase_orders_id);
        $this->data['supplier'] = $this->siteprocurment->getCompanyByID($po->customer_id);
        $this->data['warehouse'] = $this->siteprocurment->getWarehouseByID($po->warehouse_id);
        $this->data['created_by'] = $this->siteprocurment->getUser($po->created_by);
        $this->data['inv'] = $po;
        $this->data['return_purchase'] = $po->return_id ? $this->purchase_orders_model->getPurchase_ordersByID($po->return_id) : NULL;
        $this->data['return_rows'] = $po->return_id ? $this->purchase_orders_model->getAllPurchase_ordersItems($po->return_id) : NULL;
        $name = $this->lang->line("purchase_orders") . "_" . str_replace('/', '_', $po->reference_no) . ".pdf";
        $html = $this->load->view($this->theme . 'purchase_orders/pdf', $this->data, true);
        if (! $this->Settings->barcode_img) {
            $html = preg_replace("'\<\?xml(.*)\?\>'", '', $html);
        }
        if ($view) {
            $this->load->view($this->theme . 'purchase_orders/pdf', $this->data);
        } elseif ($save_bufffer) {
            return $this->sma->generate_pdf($html, $name, $save_bufffer);
        } else {
            $this->sma->generate_pdf($html, $name);
        }

    }
	
	public function saveStorepurchasewise(){
		$product_id = $_GET['product_id'];
		$quote_id = $_GET['quote_id'];
		$quantity = explode(',', $_GET['quantity']);
		$store_id = explode(',', $_GET['store_id']);
		for($i=0; $i<count($store_id); $i++){
			$insert_array[] = array(
				'quote_id' => $quote_id,
				'product_id' => $product_id,
				'quantity' => $quantity[$i],
				'store_id' => $store_id[$i]
			);
		}
		$result = $this->purchase_orders_model->addStorepurchasewise($insert_array, $quote_id, $product_id);
		if(!empty($result))
		{
			$response['status'] = 'Success';
		}else{
			$response['status'] = 'Error';
		}
		echo json_encode($response);
		exit;
	}
	public function getProductStores(){
		$product_id = $_GET['product_id'];
		$row_id = $_GET['row'];
		$qty = $_GET['qty'];
		$quote_id = $_GET['quote_id'];
		
		$res = $this->purchase_orders_model->productQuotesID($product_id, $quote_id);
		
		$result = $this->purchase_orders_model->getProductStores($product_id);
		$html = '<h3>Add Store and Quantity</h3>';
		
		if(!empty($res)){
			$i = 0;
			foreach($res as $row_data){
				
			$html .= '<div class="col-lg-5"><label>Stores</label>';
			$html .= '<select name="store_id[]" class="form-control store_id store_id_'.$row_id.'">';
			foreach($result as $row){
				
				if($row_data->store_id == $row->store_id){
					$selected = 'selected';
				}else{
					$selected = '';
				}
				$html .= '<option '.$selected.' value='.$row->store_id.'>'.$row->name.'</option>';
			}
			$html .= '</select>';
			$html .= '</div><div class="col-lg-5"><label>Quantity</label>';
			$html .= '<input type="text" name="store_quantity[]" value="'.$row_data->quantity.'" class="form-control store_quantity store_'.$row_id.'"> <input type="hidden" name="store_quantity1[]" value="'.$row_data->quantity.'" class="form-control store_quantity1 store1_'.$row_id.'"></div>';
			
			if($i == 0){
				$html .= '<div class="col-lg-2"><label>&nbsp;</label><br><button type="button" class="btn btn-primary ds_add" data-title="'.$row_id.'" data-type="'.$product_id.'" >Add</button></div>';
			}else{
				$html .= '<div class="col-lg-2"><label>&nbsp;</label><br><button type="button" class="btn btn-warning ds_delete" data-title="'.$row_id.'" >Delete</button></div></div>';
			}
			
			$i++;
			}
		}else{
			
				
			$html .= '<div class="col-lg-5"><label>Stores</label>';
			$html .= '<select name="store_id[]" class="form-control store_id store_id_'.$row_id.'">';
			foreach($result as $row){
				if($row_data->store_id == $row->store_id){
					$selected = 'selected';
				}else{
					$selected = '';
				}
				$html .= '<option '.$selected.' value='.$row->store_id.'>'.$row->name.'</option>';
			}
			$html .= '</select>';
			$html .= '</div><div class="col-lg-5"><label>Quantity</label>';
			$html .= '<input type="text" name="store_quantity[]" value="'.$qty.'" class="form-control store_quantity store_'.$row_id.'"> <input type="hidden" name="store_quantity1[]" value="'.$qty.'" class="form-control store_quantity1 store1_'.$row_id.'"></div>';
			
		
				$html .= '<div class="col-lg-2"><label>&nbsp;</label><br><button type="button" class="btn btn-primary ds_add" data-title="'.$row_id.'" data-type="'.$product_id.'" >Add</button></div>';
			
		}
		
		
		echo $html;
	}
	
	public function getProductStoresDelete(){
		$product_id = $_GET['product_id'];
		$row_id = $_GET['row'];
		
		$result = $this->purchase_orders_model->getProductStores($product_id);
		$html = '<div class="ds"><div class="clearfix"></div>';
		$html .= '<div class="col-lg-5"><label>Stores</label>';
		$html .= '<select name="store_id[]" class="form-control store_id store_id_'.$row_id.'">';
		foreach($result as $row){
			$html .= '<option value='.$row->store_id.'>'.$row->name.'</option>';
		}
		$html .= '</select>';
		$html .= '</div><div class="col-lg-5"><label>Quantity</label>';
		$html .= '<input type="text" name="store_quantity[]" value="" class="form-control store_quantity store_'.$row_id.'"><input type="hidden" name="store_quantity1[]" value="" class="form-control store_quantity1 store1_'.$row_id.'"></div>';
		$html .= '<div class="col-lg-2"><label>&nbsp;</label><br><button type="button" class="btn btn-warning ds_delete" data-title="'.$row_id.'" >Delete</button></div></div>';
		echo $html;
	}

    public function combine_pdf($purchase_orders_id)
    {
        //$this->sma->checkPermissions('pdf');

        foreach ($purchase_orders_id as $purchase_orders_id) {

            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
            $inv = $this->purchase_orders_model->getPurchase_ordersByID($purchase_orders_id);
            if (!$this->session->userdata('view_right')) {
                $this->sma->view_rights($inv->created_by);
            }
            $this->data['rows'] = $this->purchase_orders_model->getAllPurchase_ordersItems($purchase_orders_id);
            $this->data['supplier'] = $this->siteprocurment->getCompanyByID($inv->supplier_id);
            $this->data['warehouse'] = $this->siteprocurment->getWarehouseByID($inv->warehouse_id);
            $this->data['created_by'] = $this->siteprocurment->getUser($inv->created_by);
            $this->data['inv'] = $inv;
            $this->data['return_purchase'] = $inv->return_id ? $this->purchase_orders_model->getPurchase_ordersByID($inv->return_id) : NULL;
            $this->data['return_rows'] = $inv->return_id ? $this->purchase_orders_model->getAllPurchase_ordersItems($inv->return_id) : NULL;
            $inv_html = $this->load->view($this->theme . 'purchase_orders/pdf', $this->data, true);
            if (! $this->Settings->barcode_img) {
                $inv_html = preg_replace("'\<\?xml(.*)\?\>'", '', $inv_html);
            }
            $html[] = array(
                'content' => $inv_html,
                'footer' => '',
            );
        }

        $name = lang("purchase_orders") . ".pdf";
        $this->sma->generate_pdf($html, $name);

    }

    public function email($purchase_orders_id = null)
    {

        //$this->sma->checkPermissions(false, true);

        if ($this->input->get('id')) {
            $purchase_orders_id = $this->input->get('id');
        }
        $po = $this->purchase_orders_model->getPurchase_ordersByID($purchase_orders_id);
        $this->form_validation->set_rules('to', $this->lang->line("to") . " " . $this->lang->line("email"), 'trim|required|valid_email');
        $this->form_validation->set_rules('subject', $this->lang->line("subject"), 'trim|required');
        $this->form_validation->set_rules('cc', $this->lang->line("cc"), 'trim|valid_emails');
        $this->form_validation->set_rules('bcc', $this->lang->line("bcc"), 'trim|valid_emails');
        $this->form_validation->set_rules('note', $this->lang->line("message"), 'trim');

        if ($this->form_validation->run() == true) {
            if (!$this->session->userdata('view_right')) {
                $this->sma->view_rights($po->created_by);
            }
            $to = $this->input->post('to');
            $subject = $this->input->post('subject');
            if ($this->input->post('cc')) {
                $cc = $this->input->post('cc');
            } else {
                $cc = null;
            }
            if ($this->input->post('bcc')) {
                $bcc = $this->input->post('bcc');
            } else {
                $bcc = null;
            }
            $supplier = $this->siteprocurment->getCompanyByID($po->customer_id);
            $this->load->library('parser');
            $parse_data = array(
                'reference_number' => $po->reference_no,
                'contact_person' => $supplier->name,
                'company' => $supplier->company,
                'site_link' => base_url(),
                'site_name' => $this->Settings->site_name,
                'logo' => '<img src="' . base_url() . 'assets/uploads/logos/' . $this->Settings->logo . '" alt="' . $this->Settings->site_name . '"/>',
            );
            $msg = $this->input->post('note');
            $message = $this->parser->parse_string($msg, $parse_data);
            $attachment = $this->pdf($purchase_orders_id, null, 'S');

            try {
                if ($this->sma->send_email($to, $subject, $message, null, null, $attachment, $cc, $bcc)) {
                    delete_files($attachment);
                    $this->db->update('purchase_orders', array('status' => 'ordered'), array('id' => $purchase_orders_id));
                    $this->session->set_flashdata('message', $this->lang->line("email_sent"));
                    admin_redirect("procurment/purchase_orders");
                }
            } catch (Exception $e) {
                $this->session->set_flashdata('error', $e->getMessage());
                redirect($_SERVER["HTTP_REFERER"]);
            }

        } elseif ($this->input->post('send_email')) {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->session->set_flashdata('error', $this->data['error']);
            redirect($_SERVER["HTTP_REFERER"]);

        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            if (file_exists('./themes/' . $this->Settings->theme . '/admin/views/email_templates/purchase_orders.html')) {
                $purchase_orders_temp = file_get_contents('themes/' . $this->Settings->theme . '/admin/views/email_templates/purchase_orders.html');
            } else {
                $purchase_orders_temp = file_get_contents('./themes/default/admin/views/email_templates/purchase_orders.html');
            }
            $this->data['subject'] = array('name' => 'subject',
                'id' => 'subject',
                'type' => 'text',
                'value' => $this->form_validation->set_value('subject', lang('purchase_orders').' (' . $po->reference_no . ') '.lang('from').' ' . $this->Settings->site_name),
            );
            $this->data['note'] = array('name' => 'note',
                'id' => 'note',
                'type' => 'text',
                'value' => $this->form_validation->set_value('note', $purchase_orders_temp),
            );
            $this->data['supplier'] = $this->siteprocurment->getCompanyByID($po->customer_id);

            $this->data['id'] = $purchase_orders_id;
            $this->data['modal_js'] = $this->siteprocurment->modal_js();
            $this->load->view($this->theme . 'purchase_orders/email', $this->data);

        }
    }

    /* -------------------------------------------------------------------------------------------------------------------------------- */

    public function add_bk($purchase_orders_id = null)
    {
        //$this->sma->checkPermissions();
       
        $this->form_validation->set_rules('warehouse', $this->lang->line("warehouse"), 'required|is_natural_no_zero');
        $this->form_validation->set_rules('supplier', $this->lang->line("supplier"), 'required');
		
		$store_id = $this->data['default_store'];
        $this->session->unset_userdata('csrf_token');
        if ($this->form_validation->run() == true) {          
            
            /*$gst_data = [];
            $total_cgst = $total_sgst = $total_igst = 0;
            if (isset($item_tax_rate) && $item_tax_rate != 0) {
                $tax_details = $this->siteprocurment->getTaxRateByID($item_tax_rate);
                $ctax = $this->siteprocurment->calculateTax($product_details, $tax_details, $unit_cost);
                $item_tax = $ctax['amount'];
                $tax = $ctax['tax'];
                if ($product_details->tax_method != 1) {
                    $item_net_cost = $unit_cost - $item_tax;
                }
                $pr_item_tax = $this->sma->formatDecimal($item_tax * $item_unit_quantity, 4);
                if ($this->Settings->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, ($this->Settings->state == $supplier_details->state), $tax_details)) {
                    $total_cgst += $gst_data['cgst'];
                    $total_sgst += $gst_data['sgst'];
                    $total_igst += $gst_data['igst'];
                }
            }
            if ($this->Settings->indian_gst) {
                $data['cgst'] = $total_cgst;
                $data['sgst'] = $total_sgst;
                $data['igst'] = $total_igst;
            }*/

            $join_ref_no = $this->purchase_orders_model->getReqBYID($this->input->post('requestnumber'));

            $data = array(
                    'reference_no' => $this->input->post('reference_no'),                    
                    'quotation_id' => $this->input->post('requestnumber'),
                    'date' => date('Y-m-d H:i:s'),
                    'supplier_id' => $this->input->post('supplier'),                    
                    'warehouse_id' =>  $this->input->post('warehouse'),                            
                    'note' => $this->sma->clear_tags($this->input->post('note')),
                    'tax_method' => $this->input->post('tax_method'),
                    'freight' => $this->input->post('feright_chargers_shipping'),
                    'bill_disc' => $this->input->post('bill_disc'),     
                    'round_off' => $this->input->post('round_off'),
                    // 'invoice_amt' => $this->input->post('invoice_amt'),
                    'supplier_address' => $this->input->post('supplier_address'),
                    'status' => $this->input->post('status'),
                    'currency' => $this->input->post('currency'),
                    'no_of_items' => $this->input->post('total_no_items'),
                    'no_of_qty' => $this->input->post('total_no_qty'),
                    'total' => $this->input->post('final_gross_amt'),
                    'item_discount' => $this->input->post('item_disc'),
                    'bill_disc_val' => $this->input->post('bill_disc_val'),               
                    'sub_total' => $this->input->post('sub_total'),   
                    'total_tax' => $this->input->post('tax'),
                    'grand_total' => $this->input->post('net_amt'),                
                    'created_by' => $this->session->userdata('user_id'),
                    'created_on' => date('Y-m-d H:i:s'),      
                    'requestdate' => $join_ref_no->date ?  $join_ref_no->date : 0,
                    'req_reference_no' => $join_ref_no->reference_no ?  $join_ref_no->reference_no : 0       
            );

        $items =  array();
        if(isset($_POST['product'])){
          $p_count = count($_POST['product']);
            for($i=0;$i<$p_count;$i++){
                $items[$i]['product_id'] = $this->input->post('product_id['.$i.']');
                $items[$i]['product_code'] = $this->input->post('product['.$i.']');
                $items[$i]['product_name'] = $this->input->post('product_name['.$i.']');
                
                $items[$i]['quantity'] = $this->input->post('quantity['.$i.']');            
                $items[$i]['cost'] = $this->input->post('unit_cost['.$i.']');
                $items[$i]['gross'] = $this->input->post('unit_gross['.$i.']');
                
                $items[$i]['item_disc'] = $this->input->post('item_dis['.$i.']');
                $items[$i]['item_dis_type'] = $this->input->post('item_dis_type['.$i.']');
                $items[$i]['item_disc_amt'] = $this->input->post('item_disc_amt['.$i.']');
                
                //$items[0]['item_bill_disc'] = $this->input->post('item_bill_disc['.$i.']');
                $items[$i]['item_bill_disc_amt'] = $this->input->post('item_bill_disc_amt['.$i.']');            
                $items[$i]['total'] = $this->input->post('total['.$i.']');          
                $items[$i]['item_tax_method'] = $this->input->post('tax2['.$i.']');
                $items[$i]['item_tax'] = $this->input->post('item_tax['.$i.']');
                $items[$i]['landing_cost'] = $this->input->post('landing_cost['.$i.']');
                $items[$i]['selling_price'] = $this->input->post('selling_price['.$i.']');
                $items[$i]['margin'] = $this->input->post('margin['.$i.']');
                $items[$i]['net_amt'] = $this->input->post('net_cost['.$i.']');
                
            }
        }

            $warehouse_id = $this->input->post('warehouse');
            $supplier_id = $this->input->post('supplier');            
            $status = $this->input->post('status');
            $shipping = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
            $supplier_details = $this->siteprocurment->getCompanyByID($supplier_id);
            $supplier = $supplier_details->company != '-'  ? $supplier_details->company : $supplier_details->name;
            $note = $this->sma->clear_tags($this->input->post('note'));            

            $total = 0;
            $product_tax = 0;
            $product_discount = 0;
            $i = sizeof($_POST['product']);
                 
            if (empty($items)) {
                $this->form_validation->set_rules('product', lang("order_items"), 'required');
            } else {
                krsort($items);
            }

            if($this->siteprocurment->GETaccessModules('')){
				$approved_by = $this->session->userdata('user_id');
			}
			if($status == 'process'){
				$un = $this->siteprocurment->getUsersnotificationWithoutSales();
				foreach($un as $un_row)
				$notification = array(
					'user_id' => $un_row->user_id,
					'group_id' => $un_row->group_id,
					'title' => 'Purchases Request',
					'message' => 'The new purchase request has been created. REF No:'.$reference.', Date:'.$date,
					'created_by' => $this->session->userdata('user_id'),
					'created_on' => date('Y-m-d H:i:s'),
				);	
				$this->siteprocurment->insertNotification($notification);
			}

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
			
			if($this->input->post('requestnumber') != ''){
				$purchase_orders_array = array(
					'status' => 'completed',
				);
			}
        }
		 
        if ($this->form_validation->run() == true && $this->purchase_orders_model->addPurchase_orders($data, $items, $purchase_orders_array, $this->input->post('requestnumber'))) {            
            $this->session->set_userdata('remove_pols', 1);
            $this->session->set_flashdata('message', $this->lang->line("purchase_orders_added"));
            admin_redirect('procurment/purchase_orders');
        } else {			
			
            if ($purchase_orders_id) {
                $this->data['purchase_orders'] = $this->purchase_orders_model->getPurchase_ordersByID($purchase_orders_id);
                $supplier_id = $this->data['purchase_orders']->supplier_id;
                $items = $this->purchase_orders_model->getAllPurchase_ordersItems($purchase_orders_id);
                krsort($items);
                $c = rand(100000, 9999999);
                foreach ($items as $item) {
                    $row = $this->siteprocurment->getProductByID($item->product_id);
                    if ($row->type == 'combo') {
                        $combo_items = $this->siteprocurment->getProductComboItems($row->id, $item->warehouse_id);
                        foreach ($combo_items as $citem) {
                            $crow = $this->siteprocurment->getProductByID($citem->id);
                            if (!$crow) {
                                $crow = json_decode('{}');
                                $crow->qty = $item->quantity;
                            } else {
                                unset($crow->details, $crow->product_details, $crow->price);
                                $crow->qty = $citem->qty*$item->quantity;
                            }
                            $crow->base_quantity = $item->quantity;
                            $crow->base_unit = $crow->unit ? $crow->unit : $item->product_unit_id;
                            $crow->base_unit_cost = $crow->cost ? $crow->cost : $item->unit_cost;
                            $crow->unit = $item->product_unit_id;
                            $crow->discount = $item->discount ? $item->discount : '0';
                            $supplier_cost = $supplier_id ? $this->getSupplierCost($supplier_id, $crow) : $crow->cost;
                            $crow->cost = $supplier_cost ? $supplier_cost : 0;
                            $crow->tax_rate = $item->tax_rate_id;
                            $crow->real_unit_cost = $crow->cost ? $crow->cost : 0;
                            $crow->expiry = '';
                            $options = $this->purchase_orders_model->getProductOptions($crow->id);
                            $units = $this->siteprocurment->getUnitsByBUID($row->base_unit);
                            $tax_rate = $this->siteprocurment->getTaxRateByID($crow->tax_rate);
                            $ri = $this->Settings->item_addition ? $crow->id : $c;

                            $pr[$ri] = array('id' => $c, 'item_id' => $crow->id, 'label' => $crow->name . " (" . $crow->code . ")", 'row' => $crow, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options);
                            $c++;
                        }
                    } elseif ($row->type == 'standard') {
                        if (!$row) {
                            $row = json_decode('{}');
                            $row->quantity = 0;
                        } else {
                            unset($row->details, $row->product_details);
                        }

                        $row->id = $item->product_id;
                        $row->code = $item->product_code;
                        $row->name = $item->product_name;
                        $row->base_quantity = $item->quantity;
                        $row->base_unit = $row->unit ? $row->unit : $item->product_unit_id;
                        $row->base_unit_cost = $row->cost ? $row->cost : $item->unit_cost;
                        $row->unit = $item->product_unit_id;
                        $row->qty = $item->unit_quantity;
                        $row->option = $item->option_id;
                        $row->discount = $item->discount ? $item->discount : '0';
                        $supplier_cost = $supplier_id ? $this->getSupplierCost($supplier_id, $row) : $row->cost;
                        $row->cost = $supplier_cost ? $supplier_cost : 0;
                        $row->tax_rate = $item->tax_rate_id;
						$row->tax_method = $item->item_tax_method ? $item->item_tax_method : 0;
                        $row->expiry = '';
                        $row->real_unit_cost = $row->cost ? $row->cost : 0;
                        $options = $this->purchase_orders_model->getProductOptions($row->id);

                        $units = $this->siteprocurment->getUnitsByBUID($row->base_unit);
                        $tax_rate = $this->siteprocurment->getTaxRateByID($row->tax_rate);
                        $ri = $this->Settings->item_addition ? $row->id : $row->id;

                        $pr[$ri.'_'.$item->store_id] = array('id' => $c, 'store_id'=>$item->store_id,'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")",
                            'row' => $row, 'tax_rate' => $row->tax_rate, 'units' => $units, 'options' => $options);
                        $c++;
                    }
                }
                $this->data['purchase_orders_items'] = json_encode($pr);
            }

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['purchase_orders_id'] = $purchase_orders_id;
            $this->data['suppliers'] = $this->siteprocurment->getAllCompanies('supplier');
            $this->data['categories'] = $this->siteprocurment->getAllCategories();
            $this->data['currencies'] = $this->siteprocurment->getAllCurrencies();
            $this->data['tax_rates'] = $this->siteprocurment->getAllTaxRates();
            $this->data['warehouses'] = $this->siteprocurment->getAllWarehouses();
			$this->data['ref_requestnumber'] = $_GET['ref'];
			$this->data['requestnumber'] = $this->siteprocurment->getAllQUOTESNUMBER();
			
            $this->data['ponumber'] = ''; //$this->siteprocurment->getReference('po');
            $this->load->helper('string');
            $value = random_string('alnum', 20);
            $this->session->set_userdata('user_csrf', $value);
            $this->data['csrf'] = $this->session->userdata('user_csrf');
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('procurment/purchase_orders'), 'page' => lang('purchase_orders')), array('link' => '#', 'page' => lang('add_purchase_orders')));
            $meta = array('page_title' => lang('add_purchase_orders'), 'bc' => $bc);
            $this->page_construct('procurment/purchase_orders/add', $meta, $this->data);
        }
    }

    
    public function add($purchase_invoices_id = null)
    {
              
        $this->form_validation->set_rules('supplier', $this->lang->line("supplier"), 'required');
	
	$store_id = $this->data['default_store'];
        $this->session->unset_userdata('csrf_token');
        if ($this->form_validation->run() == true) {  

	    //echo '<pre>';print_R($_POST);//exit;
            $warehouse_id = $this->input->post('warehouse');
          
            $status = $this->input->post('status');                      
            $supplier_details = $this->siteprocurment->getCompanyByID($this->input->post('supplier'));
            $supplier = $supplier_details->company != '-'  ? $supplier_details->company : $supplier_details->name;
            $join_ref_no = $this->purchase_orders_model->getReqBYID($this->input->post('requestnumber'));
	    $n = $this->siteprocurment->lastidPurchase();
	    $reference = 'PO'.str_pad($n + 1, 5, 0, STR_PAD_LEFT);
            $data = array(
		'reference_no' => $reference,
                
		'date' => date('Y-m-d H:i:s'),
                'supplier_id' => $this->input->post('supplier'),
		'supplier' => $supplier,
                'quotation_id' => $this->input->post('requestnumber'),
                'warehouse_id' => $warehouse_id,		
		'quotation_date' =>  $this->input->post('requestdate'),
                'note' => $this->sma->clear_tags($this->input->post('note')),
                'tax_method' => $this->input->post('tax_method'),
		'shipping' => ($this->input->post('shipping_charge'))?$this->input->post('shipping_charge'):0,
		'bill_disc' => $this->input->post('bill_disc'),		
		'round_off' => $this->input->post('round_off'),
		'supplier_address' => $this->input->post('supplier_address'),
                'status' => $this->input->post('status'),
                'currency' => $this->input->post('currency'),
                'no_of_items' => $this->input->post('total_no_items'),
		'no_of_qty' => $this->input->post('total_no_qty'),
                'total' => $this->input->post('final_gross_amt'),
                'item_discount' => $this->input->post('item_disc'),
                'bill_disc_val' => $this->input->post('bill_disc_val'),               
		'sub_total' => $this->input->post('sub_total'),   
                'total_tax' => $this->input->post('tax'),
                'grand_total' => $this->input->post('net_amt'),                
                'created_by' => $this->session->userdata('user_id'),
		'created_on' => date('Y-m-d H:i:s'),
		'total_discount' => $this->input->post('item_disc')+$this->input->post('bill_disc_val'),
                'requestdate' => $join_ref_no->date ?  $join_ref_no->date : 0,
                'req_reference_no' => $join_ref_no->reference_no ?  $join_ref_no->reference_no : 0  
            );
	    $items =  array();
	    if(isset($_POST['product'])){
		$p_count = count($_POST['product']);
		for($i=0;$i<$p_count;$i++){
		    //$items[$i]['invoice_reference_no'] = $this->input->post('reference_no');
		    $items[$i]['store_id'] = $this->input->post('store_id['.$i.']');
		    $items[$i]['product_id'] = $this->input->post('product_id['.$i.']');
		    $items[$i]['product_code'] = $this->input->post('product['.$i.']');
		    $items[$i]['product_name'] = $this->input->post('product_name['.$i.']');
		    
		    $items[$i]['quantity'] = $this->input->post('quantity['.$i.']');
		    $items[$i]['batch_no'] = $this->input->post('batch_no['.$i.']');
		    $items[$i]['expiry'] = $this->input->post('expiry['.$i.']');
		    
		    $items[$i]['cost'] = $this->input->post('unit_cost['.$i.']');
		    $items[$i]['gross'] = $this->input->post('unit_gross['.$i.']');
		    
		    $items[$i]['item_disc'] = $this->input->post('item_dis['.$i.']');
		    $items[$i]['item_dis_type'] = @$this->input->post('item_dis_type['.$i.']');
		    $items[$i]['item_disc_amt'] = $this->input->post('item_disc_amt['.$i.']');
		    
		    //$items[0]['item_bill_disc'] = $this->input->post('item_bill_disc['.$i.']');
		    $items[$i]['item_bill_disc_amt'] = $this->input->post('item_bill_disc_amt['.$i.']');		    
		    $items[$i]['total'] = $this->input->post('total['.$i.']');		    
		    $items[$i]['item_tax_method'] = $this->input->post('tax2['.$i.']');
		    $items[$i]['item_tax'] = $this->input->post('item_tax['.$i.']');
		    $t_rate = $this->siteprocurment->getTaxRateByID($this->input->post('tax2['.$i.']'));
		    $items[$i]['tax_rate'] = $t_rate->rate;
		    $items[$i]['landing_cost'] = $this->input->post('landing_cost['.$i.']');
		    $items[$i]['selling_price'] = $this->input->post('selling_price['.$i.']');
		    $items[$i]['margin'] = $this->input->post('margin['.$i.']');
		    $items[$i]['net_amt'] = $this->input->post('net_cost['.$i.']');
		    
		    
		}
	    }
	    
	    $stores_qty = array();
	    $i = 0;
	    if(isset($_POST['stores'])){
		
	    
	    foreach($_POST['stores'] as $k => $row){
		foreach($row as $kk => $row1){
		    $stores_qty[$i]['store_id'] = $kk;
		    $stores_qty[$i]['product_id'] = $row1['product_id'];
		    $stores_qty[$i]['qty'] = $row1['qty'];
		    $i++;
		}
		
		
	    }
	    }
            //echo '<pre>';print_R($stores_qty);exit;
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
            if($this->input->post('requestnumber') != ''){
		$purchase_orders_array = array(
                    'status' => 'completed',
                );
            }
		//echo '<pre>';print_R($items);print_R($data);print_R($purchase_orders_array);exit;
        }
		 
        if ($this->form_validation->run() == true && $this->purchase_orders_model->addPurchase_orders($data, $items, $stores_qty,$purchase_orders_array, $this->input->post('requestnumber'))) {
            $this->session->set_userdata('remove_pols', 1);
            $this->session->set_flashdata('message', $this->lang->line("purchase_order_added"));
            admin_redirect('procurment/purchase_orders');
        } else {
            
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
	    
            $this->data['suppliers'] = $this->siteprocurment->getAllCompanies('supplier');
            $this->data['categories'] = $this->siteprocurment->getAllCategories();
            $this->data['currencies'] = $this->siteprocurment->getAllCurrencies();
            $this->data['tax_rates'] = $this->siteprocurment->getAllTaxRates();
            $this->data['warehouses'] = $this->siteprocurment->getAllWarehouses();
            $this->data['requestnumber'] = $this->siteprocurment->getAllQUOTESNUMBER();
            $this->data['purchaseorder'] = array();
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('procurment/purchase_orders'), 'page' => lang('purchase_orders')), array('link' => '#', 'page' => lang('add_purchase_order')));
            $meta = array('page_title' => lang('add_purchase_orders'), 'bc' => $bc);
            $this->page_construct('procurment/purchase_orders/add', $meta, $this->data);
        }
    }

    /* ------------------------------------------------------------------------------------- */
     public function edit($id = null)
    {
              
        $this->form_validation->set_rules('supplier', $this->lang->line("supplier"), 'required');
	$this->data['orders'] =  $this->purchase_orders_model->getPurchase_ordersByID($id);
	$this->data['orders']->requestnumber =  $this->data['orders']->quotation_id;
	$quotation = $this->purchase_orders_model->getQuotationByID($this->data['orders']->quotation_id);
	$stores =false;
	if(isset($quotation->req_reference_no)){
	    $q_request = $this->purchase_orders_model->getRequestByRef($quotation->req_reference_no);
	    $stores = $this->purchase_orders_model->getStoresByIDs($q_request->store_request_ids);
	}
	
	if ($this->data['orders']->status == 'approved' || $this->data['orders']->status == 'completed') {
		$this->session->set_flashdata('error', lang("Do not allowed edit option"));
		admin_redirect("procurment/purchase_orders");
	}
	$store_id =$this->data['default_store'];
        $this->session->unset_userdata('csrf_token');
        if ($this->form_validation->run() == true) {  

	    		
            $warehouse_id = $this->input->post('warehouse');
          
            $status = $this->input->post('status');                      
            $supplier_details = $this->siteprocurment->getCompanyByID($this->input->post('supplier'));
            $supplier = $supplier_details->company != '-'  ? $supplier_details->company : $supplier_details->name;
            $join_ref_no = $this->purchase_orders_model->getReqBYID($this->input->post('requestnumber'));
            
           $data = array(
		'reference_no' => $this->input->post('reference_no'),
                
		'date' => date('Y-m-d H:i:s'),
                'supplier_id' => $this->input->post('supplier'),
		'supplier' => $supplier,
                'quotation_id' => $this->input->post('requestnumber'),
                'warehouse_id' => $warehouse_id,		
		'quotation_date' =>  $this->input->post('requestdate'),
                'note' => $this->sma->clear_tags($this->input->post('note')),
                'tax_method' => $this->input->post('tax_method'),
		'shipping' => ($this->input->post('shipping_charge'))?$this->input->post('shipping_charge'):0,
		'bill_disc' => $this->input->post('bill_disc'),		
		'round_off' => $this->input->post('round_off'),
		'supplier_address' => $this->input->post('supplier_address'),
                'status' => $this->input->post('status'),
                'currency' => $this->input->post('currency'),
                'no_of_items' => $this->input->post('total_no_items'),
		'no_of_qty' => $this->input->post('total_no_qty'),
                'total' => $this->input->post('final_gross_amt'),
                'item_discount' => $this->input->post('item_disc'),
                'bill_disc_val' => $this->input->post('bill_disc_val'),               
		'sub_total' => $this->input->post('sub_total'),   
                'total_tax' => $this->input->post('tax'),
                'grand_total' => $this->input->post('net_amt'),                
                'created_by' => $this->session->userdata('user_id'),
		'created_on' => date('Y-m-d H:i:s'),
		'total_discount' => $this->input->post('item_disc')+$this->input->post('bill_disc_val'),
                'requestdate' => $join_ref_no->date ?  $join_ref_no->date : 0,
                'req_reference_no' => $join_ref_no->reference_no ?  $join_ref_no->reference_no : 0  
            );
            $items =  array();
	    if(isset($_POST['product'])){
		$p_count = count($_POST['product']);
		for($i=0;$i<$p_count;$i++){
		    $items[$i]['store_id'] = $this->input->post('store_id['.$i.']');
		    //$items[$i]['invoice_reference_no'] = $this->input->post('reference_no');
		    $items[$i]['product_id'] = $this->input->post('product_id['.$i.']');
		    $items[$i]['product_code'] = $this->input->post('product['.$i.']');
		    $items[$i]['product_name'] = $this->input->post('product_name['.$i.']');
		   
		    $items[$i]['quantity'] = $this->input->post('quantity['.$i.']');
		    $items[$i]['batch_no'] = $this->input->post('batch_no['.$i.']');
		    $items[$i]['expiry'] = $this->input->post('expiry['.$i.']');
		    
		    $items[$i]['cost'] = $this->input->post('unit_cost['.$i.']');
		    $items[$i]['gross'] = $this->input->post('unit_gross['.$i.']');
		    
		    $items[$i]['item_disc'] = $this->input->post('item_dis['.$i.']');
		    $items[$i]['item_dis_type'] = @$this->input->post('item_dis_type['.$i.']');
		    $items[$i]['item_disc_amt'] = $this->input->post('item_disc_amt['.$i.']');
		    
		    //$items[0]['item_bill_disc'] = $this->input->post('item_bill_disc['.$i.']');
		    $items[$i]['item_bill_disc_amt'] = $this->input->post('item_bill_disc_amt['.$i.']');		    
		    $items[$i]['total'] = $this->input->post('total['.$i.']');		    
		    $items[$i]['item_tax_method'] = $this->input->post('tax2['.$i.']');
		    $items[$i]['item_tax'] = $this->input->post('item_tax['.$i.']');
		    $t_rate = $this->siteprocurment->getTaxRateByID($this->input->post('tax2['.$i.']'));
		    $items[$i]['tax_rate'] = $t_rate->rate;
		    $items[$i]['landing_cost'] = $this->input->post('landing_cost['.$i.']');
		    $items[$i]['selling_price'] = $this->input->post('selling_price['.$i.']');
		    $items[$i]['margin'] = $this->input->post('margin['.$i.']');
		    $items[$i]['net_amt'] = $this->input->post('net_cost['.$i.']');
		    
		}
	    }
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
		@unlink($this->digital_upload_path.$inv->attachment);
            }
		//echo '<pre>';print_R($items);print_R($data);print_R($purchase_orders_array);exit;
        }
		 
        if ($this->form_validation->run() == true && $this->purchase_orders_model->updatePurchase_orders($id,$data,$items)) {
            
            $this->session->set_userdata('remove_pols', 1);
            $this->session->set_flashdata('message', $this->lang->line("purchase_order_updated"));
            admin_redirect('procurment/purchase_orders');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['suppliers'] = $this->siteprocurment->getAllCompanies('supplier');
            $this->data['categories'] = $this->siteprocurment->getAllCategories();
            $this->data['currencies'] = $this->siteprocurment->getAllCurrencies();
            $this->data['tax_rates'] = $this->siteprocurment->getAllTaxRates();
            $this->data['warehouses'] = $this->siteprocurment->getAllWarehouses();
	    $this->data['purchaseorder'] = array();
            $this->data['requestnumber'] = $this->siteprocurment->getAllQUOTESNUMBERedit();
            $this->data['order_items'] =  $this->purchase_orders_model->getAllPurchase_ordersItems($id);   
	  //echo '<pre>';print_R($this->data['order_items']);exit;
	    $c=1;
	    foreach ($this->data['order_items'] as $item) {
                          
                $row = $this->siteprocurment->getItemByID($item->product_id);
		$storeqty = $this->purchase_orders_model->getPO_StoreQty($item->product_id,$id);
		$row->name = $item->product_name;
		$row->id = $item->product_id;
		$row->code = $item->product_code;
                $row->qty = $item->quantity;
		$row->quantity_balance = $item->quantity;
		$row->batch_no = $item->batch_no;
		$row->expiry = $item->expiry;
		$row->unit_cost = $item->cost;
		$row->real_unit_cost = $item->cost;
                //$row->real_unit_cost = $item->gross;
		$row->item_discount_percent = $item->item_disc ? $item->item_disc : '0';
		$row->item_discount_amt = $item->item_disc_amt ? $item->item_disc_amt : '0';
		$row->item_dis_type = $item->item_dis_type;
		$row->item_bill_discount = $item->item_bill_disc_amt ? $item->item_bill_disc_amt : '0';
		$row->tax_rate = $item->item_tax_method;
		$tax = $this->siteprocurment->getTaxRateByID($item->item_tax_method);
		$row->tax_rate_val = $tax->rate;
                $row->item_selling_price =$item->selling_price;
		
		
		$row->base_unit = $row->unit ? $row->unit : $item->product_unit_id;
            
                $options = $this->purchase_orders_model->getProductOptions($row->id);

                $units = $this->siteprocurment->getUnitsByBUID($row->base_unit);
                $ri = $this->Settings->item_addition ? $row->id : $row->id;

                $pr[$ri.'_'.$item->store_id] = array('id' => $c,'store_id'=>$item->store_id,'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")",
                    'row' => $row, 'storeqty'=>$storeqty,'stores'=>$stores,'tax_rate_val' => $row->tax_rate_val,'tax_rate' => $row->tax_rate, 'units' => $units, 'options' => $options);
                $c++;
            }
	    //echo json_encode($pr);exit;
            $this->data['json_order_items'] = json_encode($pr);
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('procurment/purchase_orders'), 'page' => lang('purchase_orders')), array('link' => '#', 'page' => lang('edit_purchase_orders')));
            $meta = array('page_title' => lang('edit_purchase_orders'), 'bc' => $bc);
            $this->page_construct('procurment/purchase_orders/edit', $meta, $this->data);
        }
    }
  
    public function edit_bk($id = null)
    {
        ////$this->sma->checkPermissions();

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
		
		
        $inv = $this->purchase_orders_model->getPurchase_ordersByID($id);
        if ($inv->status == 'approved' || $inv->status == 'completed') {
			$this->session->set_flashdata('error', lang("Do not allowed edit option"));
			admin_redirect("procurment/purchase_orders");
		}
     
       $this->form_validation->set_rules('warehouse', $this->lang->line("warehouse"), 'required|is_natural_no_zero');
        
		
        $this->session->unset_userdata('csrf_token');

        if ($this->form_validation->run() == true) {
            /*$gst_data = [];
            $total_cgst = $total_sgst = $total_igst = 0;
            if (isset($item_tax_rate) && $item_tax_rate != 0) {
                $tax_details = $this->siteprocurment->getTaxRateByID($item_tax_rate);
                $ctax = $this->siteprocurment->calculateTax($product_details, $tax_details, $unit_cost);
                $item_tax = $ctax['amount'];
                $tax = $ctax['tax'];
                if ($product_details->tax_method != 1) {
                    $item_net_cost = $unit_cost - $item_tax;
                }
                $pr_item_tax = $this->sma->formatDecimal($item_tax * $item_unit_quantity, 4);
                if ($this->Settings->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, ($this->Settings->state == $supplier_details->state), $tax_details)) {
                    $total_cgst += $gst_data['cgst'];
                    $total_sgst += $gst_data['sgst'];
                    $total_igst += $gst_data['igst'];
                }
            }
            if ($this->Settings->indian_gst) {
                $data['cgst'] = $total_cgst;
                $data['sgst'] = $total_sgst;
                $data['igst'] = $total_igst;
            }*/

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

                $data = array(
                    'reference_no' => $this->input->post('reference_no'),                    
                    'quotation_id' => $this->input->post('requestnumber'),
                    'date' => date('Y-m-d H:i:s'),
                    'supplier_id' => $this->input->post('supplier'),                    
                    'warehouse_id' =>  $this->input->post('warehouse'),                            
                    'note' => $this->sma->clear_tags($this->input->post('note')),
                    'tax_method' => $this->input->post('tax_method'),
                    'freight' => $this->input->post('feright_chargers_shipping'),
                    'bill_disc' => $this->input->post('bill_disc'),     
                    'round_off' => $this->input->post('round_off'),
                    // 'invoice_amt' => $this->input->post('invoice_amt'),
                    'supplier_address' => $this->input->post('supplier_address'),
                    'status' => $this->input->post('status'),
                    'currency' => $this->input->post('currency'),
                    'no_of_items' => $this->input->post('total_no_items'),
                    'no_of_qty' => $this->input->post('total_no_qty'),
                    'total' => $this->input->post('final_gross_amt'),
                    'item_discount' => $this->input->post('item_disc'),
                    'bill_disc_val' => $this->input->post('bill_disc_val'),               
                    'sub_total' => $this->input->post('sub_total'),   
                    'total_tax' => $this->input->post('tax'),
                    'grand_total' => $this->input->post('net_amt'),                
                    'created_by' => $this->session->userdata('user_id'),
                    'created_on' => date('Y-m-d H:i:s'),      
                    'requestdate' => $join_ref_no->date ?  $join_ref_no->date : 0,
                    'req_reference_no' => $join_ref_no->reference_no ?  $join_ref_no->reference_no : 0       
            );
            $items =  array();
            if(isset($_POST['product'])){
              $p_count = count($_POST['product']);
                for($i=0;$i<$p_count;$i++){
                    
                    $items[$i]['product_id'] = $this->input->post('product_id['.$i.']');
                    $items[$i]['product_code'] = $this->input->post('product['.$i.']');
                    $items[$i]['product_name'] = $this->input->post('product_name['.$i.']');
                    
                    $items[$i]['quantity'] = $this->input->post('quantity['.$i.']');            
                    $items[$i]['cost'] = $this->input->post('unit_cost['.$i.']');
                    $items[$i]['gross'] = $this->input->post('unit_gross['.$i.']');
                    
                    $items[$i]['item_disc'] = $this->input->post('item_dis['.$i.']');
                    $items[$i]['item_dis_type'] = $this->input->post('item_dis_type['.$i.']');
                    $items[$i]['item_disc_amt'] = $this->input->post('item_disc_amt['.$i.']');
                    
                    //$items[0]['item_bill_disc'] = $this->input->post('item_bill_disc['.$i.']');
                    $items[$i]['item_bill_disc_amt'] = $this->input->post('item_bill_disc_amt['.$i.']');            
                    $items[$i]['total'] = $this->input->post('total['.$i.']');          
                    $items[$i]['item_tax_method'] = $this->input->post('tax2['.$i.']');
                    $items[$i]['item_tax'] = $this->input->post('item_tax['.$i.']');
                    $items[$i]['landing_cost'] = $this->input->post('landing_cost['.$i.']');
                    $items[$i]['selling_price'] = $this->input->post('selling_price['.$i.']');
                    $items[$i]['margin'] = $this->input->post('margin['.$i.']');
                    $items[$i]['net_amt'] = $this->input->post('net_cost['.$i.']');                
                }
            }
            if (empty($items)) {
                $this->form_validation->set_rules('product', lang("order_items"), 'required');
            } else {                
                krsort($items);
            }
             
        }

        if ($this->form_validation->run() == true && $this->purchase_orders_model->updatePurchase_orders($id, $data, $items, $inv->requestnumber)) {             
            $this->session->set_userdata('remove_pols', 1);
            $this->session->set_flashdata('message', $this->lang->line("purchase_orders_added"));
            admin_redirect('procurment/purchase_orders');
        } else {
            
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['inv'] = $inv;
            if ($this->Settings->disable_editing) {
                if ($this->data['inv']->date <= date('Y-m-d', strtotime('-'.$this->Settings->disable_editing.' days'))) {
                    $this->session->set_flashdata('error', sprintf(lang("purchase_orders_x_edited_older_than_x_days"), $this->Settings->disable_editing));
                    redirect($_SERVER["HTTP_REFERER"]);
                }
            }
            $inv_items = $this->purchase_orders_model->getAllPurchase_ordersItems($id);   
            /*echo "<pre>";
            print_r($inv_items);die;*/

            // krsort($inv_items);
            $c = rand(100000, 9999999);
            foreach ($inv_items as $item) {

                $row = $this->siteprocurment->getItemByID($item->product_id);
                $row->name = $item->product_name;
                $row->id = $item->product_id;
                $row->code = $item->product_code;
                $row->qty = $item->quantity;
                $row->batch_no = $item->batch_no;                
                $row->unit_cost = $item->cost;
                $row->real_unit_cost = $item->cost;                
                $row->item_discount_percent = $item->item_disc ? $item->item_disc : '0';
                $row->item_discount_amt = $item->item_disc_amt ? $item->item_disc_amt : '0';
                $row->item_dis_type = $item->item_dis_type;
                $row->item_bill_discount = $item->item_bill_disc_amt ? $item->item_bill_disc_amt : '0';
                $row->tax_rate = $item->item_tax_method;
                $tax = $this->siteprocurment->getTaxRateByID($item->item_tax_method);
                $row->tax_rate_val = $tax->rate;
                $row->item_selling_price =$item->selling_price;
                $row->base_unit = $row->unit ? $row->unit : $item->product_unit_id;
              
                $options = $this->purchase_orders_model->getProductOptions($row->id);

                $row->option = $item->option_id;
                // $row->real_unit_cost = $item->real_unit_price;
                $row->cost = $this->sma->formatDecimal($item->net_unit_price + ($item->item_discount / $item->quantity));
                $row->tax_rate = $item->tax_rate_id;
				$row->tax_method = $item->item_tax_method ? $item->item_tax_method : 0;
                unset($row->details, $row->product_details, $row->price, $row->file, $row->product_group_id);
                $units = $this->siteprocurment->getUnitsByBUID($row->base_unit);
                $tax_rate = $this->siteprocurment->getTaxRateByID($row->tax_rate);
                $ri = $this->Settings->item_addition ? $row->id : $row->id;
                $pr[$ri] = array('id' => $c, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")",
                    'row' => $row, 'tax_rate' => $row->tax_rate, 'units' => $units, 'options' => $options);
                $c++;
            }
/*echo "<pre>";
print_r($row);die;*/
            $this->data['inv_items'] = json_encode($pr);
            $this->data['id'] = $id;
            $this->data['suppliers'] = $this->siteprocurment->getAllCompanies('supplier');
            $this->data['purchase_orders'] = $this->purchase_orders_model->getPurchase_ordersByID($id);
            $this->data['categories'] = $this->siteprocurment->getAllCategories();
            $this->data['currencies'] = $this->siteprocurment->getAllCurrencies();
            $this->data['tax_rates'] = $this->siteprocurment->getAllTaxRates();
            $this->data['warehouses'] = $this->siteprocurment->getAllWarehouses();
			// $this->data['ref_requestnumber'] = $_GET['ref'];
			 $this->data['requestnumber'] = $this->siteprocurment->getAllQUOTESNUMBERedit();
			 
            $this->load->helper('string');
            $value = random_string('alnum', 20);
            $this->session->set_userdata('user_csrf', $value);
            $this->session->set_userdata('remove_pols', 1);
            $this->data['csrf'] = $this->session->userdata('user_csrf');
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('procurment/purchase_orders'), 'page' => lang('purchase_orders')), array('link' => '#', 'page' => lang('edit_purchase_orders')));
            $meta = array('page_title' => lang('edit_purchase_orders'), 'bc' => $bc);
          
            $this->page_construct('procurment/purchase_orders/edit', $meta, $this->data);
        }
    }

    /* ----------------------------------------------------------------------------------------------------------- */

    public function purchase_orders_by_csv()
    {
        //$this->sma->checkPermissions('csv');
        $this->load->helper('security');
        $this->form_validation->set_message('is_natural_no_zero', $this->lang->line("no_zero_required"));
        $this->form_validation->set_rules('warehouse', $this->lang->line("warehouse"), 'required|is_natural_no_zero');
        $this->form_validation->set_rules('supplier', $this->lang->line("supplier"), 'required');
        $this->form_validation->set_rules('userfile', $this->lang->line("upload_file"), 'xss_clean');

        if ($this->form_validation->run() == true) {
            $quantity = "quantity";
            $product = "product";
            $unit_cost = "unit_cost";
            $tax_rate = "tax_rate";
            $reference = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->siteprocurment->getReference('po');
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = null;
            }
            $warehouse_id = $this->input->post('warehouse');
            $supplier_id = $this->input->post('supplier');
            $status = $this->input->post('status');
            $shipping = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
            $supplier_details = $this->siteprocurment->getCompanyByID($supplier_id);
            $supplier = $supplier_details->company != '-'  ? $supplier_details->company : $supplier_details->name;
            $note = $this->sma->clear_tags($this->input->post('note'));

            $total = 0;
            $product_tax = 0;
            $product_discount = 0;
            $gst_data = [];
            $total_cgst = $total_sgst = $total_igst = 0;

            if (isset($_FILES["userfile"])) {

                $this->load->library('upload');

                $config['upload_path'] = $this->digital_upload_path;
                $config['allowed_types'] = 'csv';
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = true;

                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    admin_redirect("procurment/purchase_orders/purchase_orders_by_csv");
                }

                $csv = $this->upload->file_name;

                $arrResult = array();
                $handle = fopen($this->digital_upload_path . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 1000, ",")) !== false) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);

                $keys = array('code', 'net_unit_cost', 'quantity', 'variant', 'item_tax_rate', 'discount', 'expiry');
                $final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                $rw = 2;
                foreach ($final as $csv_pr) {

                    if (isset($csv_pr['code']) && isset($csv_pr['net_unit_cost']) && isset($csv_pr['quantity'])) {

                        if ($product_details = $this->purchase_orders_model->getProductByCode($csv_pr['code'])) {

                            if ($csv_pr['variant']) {
                                $item_option = $this->purchase_orders_model->getProductVariantByName($csv_pr['variant'], $product_details->id);
                                if (!$item_option) {
                                    $this->session->set_flashdata('error', lang("pr_not_found") . " ( " . $product_details->name . " - " . $csv_pr['variant'] . " ). " . lang("line_no") . " " . $rw);
                                    redirect($_SERVER["HTTP_REFERER"]);
                                }
                            } else {
                                $item_option = json_decode('{}');
                                $item_option->id = null;
                            }

                            $item_code = $csv_pr['code'];
                            $item_net_cost = $this->sma->formatDecimal($csv_pr['net_unit_cost']);
                            $item_quantity = $csv_pr['quantity'];
                            $quantity_balance = $csv_pr['quantity'];
                            $item_tax_rate = $csv_pr['item_tax_rate'];
                            $item_discount = $csv_pr['discount'];
                            $item_expiry = isset($csv_pr['expiry']) ? $this->sma->fsd($csv_pr['expiry']) : null;

                            $pr_discount = $this->siteprocurment->calculateDiscount($item_discount, $item_net_cost);
                            $pr_item_discount = $this->sma->formatDecimal(($pr_discount * $item_quantity), 4);
                            $product_discount += $pr_item_discount;

                            $tax = "";
                            $pr_item_tax = 0;
                            $unit_cost = $item_net_cost - $pr_discount;
                            $gst_data = [];
                            $tax_details = ((isset($item_tax_rate) && !empty($item_tax_rate)) ? $this->purchase_orders_model->getTaxRateByName($item_tax_rate) : $this->siteprocurment->getTaxRateByID($product_details->tax_rate));
                            if ($tax_details) {
                                $ctax = $this->siteprocurment->calculateTax($product_details, $tax_details, $unit_cost);
                                $item_tax = $ctax['amount'];
                                $tax = $ctax['tax'];
                                if ($product_details->tax_method != 1) {
                                    $item_net_cost = $unit_cost - $item_tax;
                                }
                                $pr_item_tax = $this->sma->formatDecimal($item_tax * $item_quantity, 4);
                                if ($this->Settings->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, ($this->Settings->state == $supplier_details->state), $tax_details)) {
                                    $total_cgst += $gst_data['cgst'];
                                    $total_sgst += $gst_data['sgst'];
                                    $total_igst += $gst_data['igst'];
                                }
                            }

                            $product_tax += $pr_item_tax;
                            $subtotal = $this->sma->formatDecimal(((($item_net_cost * $item_quantity) + $pr_item_tax) - $pr_item_discount), 4);
                            $unit = $this->siteprocurment->getUnitByID($product_details->unit);
                            $product = array(
                                'product_id' => $product_details->id,
                                'product_code' => $item_code,
                                'product_name' => $product_details->name,
                                'option_id' => $item_option->id,
                                'net_unit_cost' => $item_net_cost,
                                'quantity' => $item_quantity,
                                'product_unit_id' => $product_details->unit,
                                'product_unit_code' => $unit->code,
                                'unit_quantity' => $item_quantity,
                                'quantity_balance' => $quantity_balance,
                                'warehouse_id' => $warehouse_id,
                                'item_tax' => $pr_item_tax,
                                'tax_rate_id' => $tax_details ? $tax_details->id : null,
                                'tax' => $tax,
                                'discount' => $item_discount,
                                'item_discount' => $pr_item_discount,
                                'expiry' => $item_expiry,
                                'subtotal' => $subtotal,
                                'date' => date('Y-m-d', strtotime($date)),
                                'status' => $status,
                                'unit_cost' => $this->sma->formatDecimal(($item_net_cost + $item_tax), 4),
                                'real_unit_cost' => $this->sma->formatDecimal(($item_net_cost + $item_tax + $pr_discount), 4),
                            );

                            $products[] = ($product+$gst_data);
                            $total += $this->sma->formatDecimal(($item_net_cost * $item_quantity), 4);

                        } else {
                            $this->session->set_flashdata('error', $this->lang->line("pr_not_found") . " ( " . $csv_pr['code'] . " ). " . $this->lang->line("line_no") . " " . $rw);
                            redirect($_SERVER["HTTP_REFERER"]);
                        }
                        $rw++;
                    }

                }
            }

            $order_discount = $this->siteprocurment->calculateDiscount($this->input->post('discount'), ($total + $product_tax));
            $total_discount = $this->sma->formatDecimal(($order_discount + $product_discount), 4);
            $order_tax = $this->siteprocurment->calculateOrderTax($this->input->post('order_tax'), ($total + $product_tax - $total_discount));
            $total_tax = $this->sma->formatDecimal(($product_tax + $order_tax), 4);
            $grand_total = $this->sma->formatDecimal(($total + $total_tax + $this->sma->formatDecimal($shipping) - $total_discount), 4);
            $data = array('reference_no' => $reference,
                'date' => $date,
                'supplier_id' => $supplier_id,
                'supplier' => $supplier,
                'warehouse_id' => $warehouse_id,
                'note' => $note,
                'total' => $total,
                'product_discount' => $product_discount,
                'order_discount_id' => $this->input->post('discount'),
                'order_discount' => $order_discount,
                'total_discount' => $total_discount,
                'product_tax' => $product_tax,
                'order_tax_id' => $this->input->post('order_tax'),
                'order_tax' => $order_tax,
                'total_tax' => $total_tax,
                'shipping' => $this->sma->formatDecimal($shipping),
                'grand_total' => $grand_total,
                'status' => $status,
                'created_by' => $this->session->userdata('username'),
            );
            if ($this->Settings->indian_gst) {
                $data['cgst'] = $total_cgst;
                $data['sgst'] = $total_sgst;
                $data['igst'] = $total_igst;
            }

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

            // $this->sma->print_arrays($data, $products);
        }

        if ($this->form_validation->run() == true && $this->purchase_orders_model->addPurchase($data, $products)) {

            $this->session->set_flashdata('message', $this->lang->line("purchase_orders_added"));
            admin_redirect("procurment/purchase_orders");
        } else {

            $data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['warehouses'] = $this->siteprocurment->getAllWarehouses();
            $this->data['tax_rates'] = $this->siteprocurment->getAllTaxRates();
            $this->data['ponumber'] = ''; // $this->siteprocurment->getReference('po');

            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('procurment/purchase_orders'), 'page' => lang('purchase_orders')), array('link' => '#', 'page' => lang('add_purchase_orders_by_csv')));
            $meta = array('page_title' => lang('add_purchase_orders_by_csv'), 'bc' => $bc);
            $this->page_construct('procurment/purchase_orders/purchase_orders__orderby_csv', $meta, $this->data);

        }
    }

    /* --------------------------------------------------------------------------- */

   public function delete($id = null)
    {
        //$this->sma->checkPermissions(null, true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        if ($this->purchase_orders_model->deletePurchase_orders($id)) {

            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(array('error' => 0, 'msg' => lang("purchase_orders_deleted")));
            }
            $this->session->set_flashdata('message', lang('purchase_orders_deleted'));
            admin_redirect('procurment/welcome');
        }
    }
    
    /* --------------------------------------------------------------------------- */

    public function suggestions()
    {
        $term = $this->input->get('term', true);
        $supplier_id = $this->input->get('supplier_id', true);

        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('procurment/welcome') . "'; }, 10);</script>");
        }

        $analyzed = $this->sma->analyze_term($term);
        $sr = $analyzed['term'];
        $option_id = $analyzed['option_id'];

        $rows = $this->siteprocurment->getProductNames($sr);
		
        if ($rows) {
            $c = str_replace(".", "", microtime(true));
            $r = 0;
            foreach ($rows as $row) {
                $option = false;
                $row->item_tax_method = $row->tax_method;
                $options = $this->purchase_orders_model->getProductOptions($row->id);
                if ($options) {
                    $opt = $option_id && $r == 0 ? $this->purchase_orders_model->getProductOptionByID($option_id) : current($options);
                    if (!$option_id || $r > 0) {
                        $option_id = $opt->id;
                    }
                } else {
                    $opt = json_decode('{}');
                    $opt->cost = 0;
                    $option_id = FALSE;
                }
                $row->option = $option_id;
                $row->supplier_part_no = '';
                if ($opt->cost != 0) {
                    $row->cost = $opt->cost;
                }
                $row->cost = $supplier_id ? $this->getSupplierCost($supplier_id, $row) : $row->cost;
                $row->unit_cost = $row->cost;
                $row->real_unit_cost = $row->cost;                
                $row->base_quantity = 1;
                $row->base_unit = $row->unit;
                $row->base_unit_cost = $row->cost;                
                $row->unit = $row->purchase_unit ? $row->purchase_unit : $row->unit;
                $row->new_entry = 1;
                $row->expiry = '';
                $row->qty = 1;
                $row->quantity_balance = '';

                $row->item_discount_percent = '0';
                $row->item_discount_amt = '0';
                $row->item_bill_discount = '0';
                $row->item_tax_rate = '0';
                $row->item_selling_price = '0';
                $row->discount = '0';
                unset($row->details, $row->product_details, $row->price, $row->file, $row->supplier1price, $row->supplier2price, $row->supplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);

                $units = $this->siteprocurment->getUnitsByBUID($row->base_unit);
                $tax_rate = $this->siteprocurment->getTaxRateByID($row->tax_rate);

                $pr[] = array('id' => ($c + $r), 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")",
                    'row' => $row, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options);
                $r++;
            }
            $this->sma->send_json($pr);
        } else {
            $this->sma->send_json(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
        }
    }

    /* -------------------------------------------------------------------------------- */

    public function purchase_orders_actions()
    {
        if (!$this->Owner && !$this->GP['bulk_actions']) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {

                    //$this->sma->checkPermissions('delete');
                    foreach ($_POST['val'] as $id) {
                        $this->purchase_orders_model->deletePurchase_orders($id);
                    }
                    $this->session->set_flashdata('message', $this->lang->line("purchase_orders_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);

                } elseif ($this->input->post('form_action') == 'combine') {

                    $html = $this->combine_pdf($_POST['val']);

                } elseif ($this->input->post('form_action') == 'export_excel') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('purchase_orders'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference_no'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('supplier'));
                    $this->excel->getActiveSheet()->SetCellValue('F1', lang('status'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('grand_total'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $purchase_orders = $this->purchase_orders_model->getPurchase_ordersByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($purchase_orders->date));
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $purchase_orders->reference_no);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $purchase_orders->supplier);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $purchase_orders->status);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $this->sma->formatMoney($purchase_orders->grand_total));
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'purchase_orders_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', $this->lang->line("no_purchase_orders_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    /* -------------------------------------------------------------------------------- */

    public function payments($id = null)
    {
        //$this->sma->checkPermissions(false, true);

        $this->data['payments'] = $this->purchase_orders_model->getPurchasePayments($id);
        $this->data['inv'] = $this->purchase_orders_model->getPurchase_ordersByID($id);
        $this->load->view($this->theme . 'purchase_orders/payments', $this->data);
    }

    public function payment_note($id = null)
    {
        //$this->sma->checkPermissions('payments', true);
        $payment = $this->purchase_orders_model->getPaymentByID($id);
        $inv = $this->purchase_orders_model->getPurchase_ordersByID($payment->purchase_id);
        $this->data['supplier'] = $this->siteprocurment->getCompanyByID($inv->supplier_id);
        $this->data['warehouse'] = $this->siteprocurment->getWarehouseByID($inv->warehouse_id);
        $this->data['inv'] = $inv;
        $this->data['payment'] = $payment;
        $this->data['page_title'] = $this->lang->line("payment_note");

        $this->load->view($this->theme . 'purchase_orders/payment_note', $this->data);
    }

    public function email_payment($id = null)
    {
        //$this->sma->checkPermissions('payments', true);
        $payment = $this->purchase_orders_model->getPaymentByID($id);
        $inv = $this->purchase_orders_model->getPurchase_ordersByID($payment->purchase_id);
        $supplier = $this->siteprocurment->getCompanyByID($inv->supplier_id);
        $this->data['inv'] = $inv;
        $this->data['payment'] = $payment;
        if ( ! $supplier->email) {
            $this->sma->send_json(array('msg' => lang("update_supplier_email")));
        }
        $this->data['supplier'] =$supplier;
        $this->data['warehouse'] = $this->siteprocurment->getWarehouseByID($inv->warehouse_id);
        $this->data['inv'] = $inv;
        $this->data['payment'] = $payment;
        $this->data['page_title'] = lang("payment_note");
        $html = $this->load->view($this->theme . 'purchase_orders/payment_note', $this->data, TRUE);

        $html = str_replace(array('<i class="fa fa-2x">&times;</i>', 'modal-', '<p>&nbsp;</p>', '<p style="border-bottom: 1px solid #666;">&nbsp;</p>', '<p>'.lang("stamp_sign").'</p>'), '', $html);
        $html = preg_replace("/<img[^>]+\>/i", '', $html);
        // $html = '<div style="border:1px solid #DDD; padding:10px; margin:10px 0;">'.$html.'</div>';

        $this->load->library('parser');
        $parse_data = array(
            'stylesheet' => '<link href="'.$this->data['assets'].'styles/helpers/bootstrap.min.css" rel="stylesheet"/>',
            'name' => $supplier->company && $supplier->company != '-' ? $supplier->company :  $supplier->name,
            'email' => $supplier->email,
            'heading' => lang('payment_note').'<hr>',
            'msg' => $html,
            'site_link' => base_url(),
            'site_name' => $this->Settings->site_name,
            'logo' => '<img src="' . base_url('assets/uploads/logos/' . $this->Settings->logo) . '" alt="' . $this->Settings->site_name . '"/>'
        );
        $msg = file_get_contents('./themes/' . $this->Settings->theme . '/admin/views/email_templates/email_con.html');
        $message = $this->parser->parse_string($msg, $parse_data);
        $subject = lang('payment_note') . ' - ' . $this->Settings->site_name;

        if ($this->sma->send_email($supplier->email, $subject, $message)) {
            $this->sma->send_json(array('msg' => lang("email_sent")));
        } else {
            $this->sma->send_json(array('msg' => lang("email_failed")));
        }
    }

    public function add_payment($id = null)
    {
        //$this->sma->checkPermissions('payments', true);
        $this->load->helper('security');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $purchase = $this->purchase_orders_model->getPurchase_ordersByID($id);
        if ($purchase->payment_status == 'paid' && $purchase->grand_total == $purchase->paid) {
            $this->session->set_flashdata('error', lang("purchase_already_paid"));
            $this->sma->md();
        }

        //$this->form_validation->set_rules('reference_no', lang("reference_no"), 'required');
        $this->form_validation->set_rules('amount-paid', lang("amount"), 'required');
        $this->form_validation->set_rules('paid_by', lang("paid_by"), 'required');
        $this->form_validation->set_rules('userfile', lang("attachment"), 'xss_clean');
        if ($this->form_validation->run() == true) {
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $payment = array(
                'date' => $date,
                'purchase_id' => $this->input->post('purchase_id'),
                'reference_no' => $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->siteprocurment->getReference('ppay'),
                'amount' => $this->input->post('amount-paid'),
                'paid_by' => $this->input->post('paid_by'),
                'cheque_no' => $this->input->post('cheque_no'),
                'cc_no' => $this->input->post('pcc_no'),
                'cc_holder' => $this->input->post('pcc_holder'),
                'cc_month' => $this->input->post('pcc_month'),
                'cc_year' => $this->input->post('pcc_year'),
                'cc_type' => $this->input->post('pcc_type'),
                'note' => $this->sma->clear_tags($this->input->post('note')),
                'created_by' => $this->session->userdata('user_id'),
                'type' => 'approved',
            );

            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = $this->digital_upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = false;
                $config['encrypt_name'] = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
                $payment['attachment'] = $photo;
            }

            //$this->sma->print_arrays($payment);

        } elseif ($this->input->post('add_payment')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }

        if ($this->form_validation->run() == true && $this->purchase_orders_model->addPayment($payment)) {
            $this->session->set_flashdata('message', lang("payment_added"));
            redirect($_SERVER["HTTP_REFERER"]);
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['inv'] = $purchase;
            $this->data['payment_ref'] = ''; //$this->siteprocurment->getReference('ppay');
            $this->data['modal_js'] = $this->siteprocurment->modal_js();

            $this->load->view($this->theme . 'purchase_orders/add_payment', $this->data);
        }
    }

    public function edit_payment($id = null)
    {
        //$this->sma->checkPermissions('edit', true);
        $this->load->helper('security');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->form_validation->set_rules('reference_no', lang("reference_no"), 'required');
        $this->form_validation->set_rules('amount-paid', lang("amount"), 'required');
        $this->form_validation->set_rules('paid_by', lang("paid_by"), 'required');
        $this->form_validation->set_rules('userfile', lang("attachment"), 'xss_clean');
        if ($this->form_validation->run() == true) {
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $payment = array(
                'date' => $date,
                'purchase_id' => $this->input->post('purchase_id'),
                'reference_no' => $this->input->post('reference_no'),
                'amount' => $this->input->post('amount-paid'),
                'paid_by' => $this->input->post('paid_by'),
                'cheque_no' => $this->input->post('cheque_no'),
                'cc_no' => $this->input->post('pcc_no'),
                'cc_holder' => $this->input->post('pcc_holder'),
                'cc_month' => $this->input->post('pcc_month'),
                'cc_year' => $this->input->post('pcc_year'),
                'cc_type' => $this->input->post('pcc_type'),
                'note' => $this->sma->clear_tags($this->input->post('note')),
            );

            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = $this->digital_upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = false;
                $config['encrypt_name'] = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
                $payment['attachment'] = $photo;
            }

            //$this->sma->print_arrays($payment);

        } elseif ($this->input->post('edit_payment')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }

        if ($this->form_validation->run() == true && $this->purchase_orders_model->updatePayment($id, $payment)) {
            $this->session->set_flashdata('message', lang("payment_updated"));
            admin_redirect("procurment/purchase_orders");
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['payment'] = $this->purchase_orders_model->getPaymentByID($id);
            $this->data['modal_js'] = $this->siteprocurment->modal_js();

            $this->load->view($this->theme . 'purchase_orders/edit_payment', $this->data);
        }
    }

    public function delete_payment($id = null)
    {
        //$this->sma->checkPermissions('delete', true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->purchase_orders_model->deletePayment($id)) {
            //echo lang("payment_deleted");
            $this->session->set_flashdata('message', lang("payment_deleted"));
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    /* -------------------------------------------------------------------------------- */

    public function expenses($id = null)
    {
        //$this->sma->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('expenses')));
        $meta = array('page_title' => lang('expenses'), 'bc' => $bc);
        $this->page_construct('procurment/purchase_orders/expenses', $meta, $this->data);
    }

    public function getExpenses()
    {
        //$this->sma->checkPermissions('expenses');

        $detail_link = anchor('admin/procurment/purchase_orders/expense_note/$1', '<i class="fa fa-file-text-o"></i> ' . lang('expense_note'), 'data-toggle="modal" data-target="#myModal2"');
        $edit_link = anchor('admin/procurment/purchase_orders/edit_expense/$1', '<i class="fa fa-edit"></i> ' . lang('edit_expense'), 'data-toggle="modal" data-target="#myModal"');
        //$attachment_link = '<a href="'.base_url('assets/uploads/$1').'" target="_blank"><i class="fa fa-chain"></i></a>';
        $delete_link = "<a href='#' class='po' title='<b>" . $this->lang->line("delete_expense") . "</b>' data-content=\"<p>"
        . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('procurment/purchase_orders/delete_expense/$1') . "'>"
        . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
        . lang('delete_expense') . "</a>";
        $action = '<div class="text-center"><div class="btn-group text-left">'
        . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
        . lang('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
            <li>' . $detail_link . '</li>
            <li>' . $edit_link . '</li>
            <li>' . $delete_link . '</li>
        </ul>
    </div></div>';

        $this->load->library('datatables');

        $this->datatables
            ->select($this->db->dbprefix('expenses') . ".id as id, date, reference, {$this->db->dbprefix('expense_categories')}.name as category, amount, note, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as user, attachment", false)
            ->from('expenses')
            ->join('users', 'users.id=expenses.created_by', 'left')
            ->join('expense_categories', 'expense_categories.id=expenses.category_id', 'left')
            ->group_by('expenses.id');

        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $this->datatables->where('created_by', $this->session->userdata('user_id'));
        }
        //$this->datatables->edit_column("attachment", $attachment_link, "attachment");
        $this->datatables->add_column("Actions", $action, "id");
        echo $this->datatables->generate();
    }

    public function expense_note($id = null)
    {
        $expense = $this->purchase_orders_model->getExpenseByID($id);
        $this->data['user'] = $this->siteprocurment->getUser($expense->created_by);
        $this->data['category'] = $expense->category_id ? $this->purchase_orders_model->getExpenseCategoryByID($expense->category_id) : NULL;
        $this->data['warehouse'] = $expense->warehouse_id ? $this->siteprocurment->getWarehouseByID($expense->warehouse_id) : NULL;
        $this->data['expense'] = $expense;
        $this->data['page_title'] = $this->lang->line("expense_note");
        $this->load->view($this->theme . 'purchase_orders/expense_note', $this->data);
    }

    public function add_expense()
    {
        //$this->sma->checkPermissions('expenses', true);
        $this->load->helper('security');

        //$this->form_validation->set_rules('reference', lang("reference"), 'required');
        $this->form_validation->set_rules('amount', lang("amount"), 'required');
        $this->form_validation->set_rules('userfile', lang("attachment"), 'xss_clean');
        if ($this->form_validation->run() == true) {
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $data = array(
                'date' => $date,
                'reference' => $this->input->post('reference') ? $this->input->post('reference') : $this->siteprocurment->getReference('ex'),
                'amount' => $this->input->post('amount'),
                'created_by' => $this->session->userdata('user_id'),
                'note' => $this->input->post('note', true),
                'category_id' => $this->input->post('category', true),
                'warehouse_id' => $this->input->post('warehouse', true),
            );

            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = $this->upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = false;
                $config['encrypt_name'] = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
                $data['attachment'] = $photo;
            }

            //$this->sma->print_arrays($data);

        } elseif ($this->input->post('add_expense')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }

        if ($this->form_validation->run() == true && $this->purchase_orders_model->addExpense($data)) {
            $this->session->set_flashdata('message', lang("expense_added"));
            redirect($_SERVER["HTTP_REFERER"]);
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['exnumber'] = ''; //$this->siteprocurment->getReference('ex');
            $this->data['warehouses'] = $this->siteprocurment->getAllWarehouses();
            $this->data['categories'] = $this->purchase_orders_model->getExpenseCategories();
            $this->data['modal_js'] = $this->siteprocurment->modal_js();
            $this->load->view($this->theme . 'purchase_orders/add_expense', $this->data);
        }
    }

    public function edit_expense($id = null)
    {
        //$this->sma->checkPermissions('edit', true);
        $this->load->helper('security');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->form_validation->set_rules('reference', lang("reference"), 'required');
        $this->form_validation->set_rules('amount', lang("amount"), 'required');
        $this->form_validation->set_rules('userfile', lang("attachment"), 'xss_clean');
        if ($this->form_validation->run() == true) {
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $data = array(
                'date' => $date,
                'reference' => $this->input->post('reference'),
                'amount' => $this->input->post('amount'),
                'note' => $this->input->post('note', true),
                'category_id' => $this->input->post('category', true),
                'warehouse_id' => $this->input->post('warehouse', true),
            );
            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = $this->upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = false;
                $config['encrypt_name'] = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
                $data['attachment'] = $photo;
            }

            //$this->sma->print_arrays($data);

        } elseif ($this->input->post('edit_expense')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }

        if ($this->form_validation->run() == true && $this->purchase_orders_model->updateExpense($id, $data)) {
            $this->session->set_flashdata('message', lang("expense_updated"));
            admin_redirect("procurment/purchase_orders/expenses");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['expense'] = $this->purchase_orders_model->getExpenseByID($id);
            $this->data['warehouses'] = $this->siteprocurment->getAllWarehouses();
            $this->data['modal_js'] = $this->siteprocurment->modal_js();
            $this->data['categories'] = $this->purchase_orders_model->getExpenseCategories();
            $this->load->view($this->theme . 'purchase_orders/edit_expense', $this->data);
        }
    }

    public function delete_expense($id = null)
    {
        //$this->sma->checkPermissions('delete', true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $expense = $this->purchase_orders_model->getExpenseByID($id);
        if ($this->purchase_orders_model->deleteExpense($id)) {
            if ($expense->attachment) {
                unlink($this->upload_path . $expense->attachment);
            }
            $this->sma->send_json(array('error' => 0, 'msg' => lang("expense_deleted")));
        }
    }

    public function expense_actions()
    {
        if (!$this->Owner && !$this->GP['bulk_actions']) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    //$this->sma->checkPermissions('delete');
                    foreach ($_POST['val'] as $id) {
                        $this->purchase_orders_model->deleteExpense($id);
                    }
                    $this->session->set_flashdata('message', $this->lang->line("expenses_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                if ($this->input->post('form_action') == 'export_excel') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('expenses'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('amount'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('note'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('created_by'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $expense = $this->purchase_orders_model->getExpenseByID($id);
                        $user = $this->siteprocurment->getUser($expense->created_by);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($expense->date));
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $expense->reference);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $this->sma->formatMoney($expense->amount));
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $expense->note);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $user->first_name . ' ' . $user->last_name);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(35);
                    $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'expenses_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', $this->lang->line("no_expense_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    public function view_return($id = null)
    {
        //$this->sma->checkPermissions('return_purchase_orders');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv = $this->purchase_orders_model->getReturnByID($id);
        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($inv->created_by);
        }
        $this->data['barcode'] = "<img src='" . admin_url('procurment/products/gen_barcode/' . $inv->reference_no) . "' alt='" . $inv->reference_no . "' class='pull-left' />";
        $this->data['supplier'] = $this->siteprocurment->getCompanyByID($inv->supplier_id);
        $this->data['payments'] = $this->purchase_orders_model->getPaymentsForPurchase($id);
        $this->data['user'] = $this->siteprocurment->getUser($inv->created_by);
        $this->data['warehouse'] = $this->siteprocurment->getWarehouseByID($inv->warehouse_id);
        $this->data['inv'] = $inv;
        $this->data['rows'] = $this->purchase_orders_model->getAllReturnItems($id);
        $this->data['purchase'] = $this->purchase_orders_model->getPurchase_ordersByID($inv->purchase_id);
        $this->load->view($this->theme.'purchase_orders/view_return', $this->data);
    }

    public function return_purchase($id = null)
    {
        //$this->sma->checkPermissions('return_purchase_orders');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $purchase = $this->purchase_orders_model->getPurchase_ordersByID($id);
        if ($purchase->return_id) {
            $this->session->set_flashdata('error', lang("purchase_already_returned"));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $this->form_validation->set_rules('return_surcharge', lang("return_surcharge"), 'required');

        if ($this->form_validation->run() == true) {

            $reference = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->siteprocurment->getReference('rep');
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }

            $return_surcharge = $this->input->post('return_surcharge') ? $this->input->post('return_surcharge') : 0;
            $note = $this->sma->clear_tags($this->input->post('note'));
            $supplier_details = $this->siteprocurment->getCompanyByID($purchase->supplier_id);

            $total = 0;
            $product_tax = 0;
            $product_discount = 0;
            $gst_data = [];
            $total_cgst = $total_sgst = $total_igst = 0;
            $i = isset($_POST['product']) ? sizeof($_POST['product']) : 0;
            for ($r = 0; $r < $i; $r++) {
                $item_id = $_POST['product_id'][$r];
                $item_code = $_POST['product'][$r];
                $purchase_item_id = $_POST['purchase_item_id'][$r];
                $item_option = isset($_POST['product_option'][$r]) && !empty($_POST['product_option'][$r]) && $_POST['product_option'][$r] != 'false' ? $_POST['product_option'][$r] : null;
                $real_unit_cost = $this->sma->formatDecimal($_POST['real_unit_cost'][$r]);
                $unit_cost = $this->sma->formatDecimal($_POST['unit_cost'][$r]);
                $item_unit_quantity = (0-$_POST['quantity'][$r]);
                $item_expiry = isset($_POST['expiry'][$r]) ? $_POST['expiry'][$r] : '';
                $item_tax_rate = isset($_POST['product_tax'][$r]) ? $_POST['product_tax'][$r] : null;
                $item_discount = isset($_POST['product_discount'][$r]) ? $_POST['product_discount'][$r] : null;
                $item_unit = $_POST['product_unit'][$r];
                $item_quantity = (0-$_POST['product_base_quantity'][$r]);

                if (isset($item_code) && isset($real_unit_cost) && isset($unit_cost) && isset($item_quantity)) {
                    $product_details = $this->purchase_orders_model->getProductByCode($item_code);

                    $item_type = $product_details->type;
                    $item_name = $product_details->name;
                    $pr_discount = $this->siteprocurment->calculateDiscount($item_discount, $unit_cost);
                    $unit_cost = $this->sma->formatDecimal($unit_cost - $pr_discount);
                    $pr_item_discount = $this->sma->formatDecimal(($pr_discount * $item_unit_quantity), 4);
                    $product_discount += $pr_item_discount;
                    $item_net_cost = $unit_cost;
                    $pr_item_tax = $item_tax = 0;
                    $tax = "";

                    if (isset($item_tax_rate) && $item_tax_rate != 0) {

                        $tax_details = $this->siteprocurment->getTaxRateByID($item_tax_rate);
                        $ctax = $this->siteprocurment->calculateTax($product_details, $tax_details, $unit_cost);
                        $item_tax = $ctax['amount'];
                        $tax = $ctax['tax'];
                        if ($product_details->tax_method != 1) {
                            $item_net_cost = $unit_cost - $item_tax;
                        }
                        $pr_item_tax = $this->sma->formatDecimal($item_tax * $item_unit_quantity, 4);
                        if ($this->Settings->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, ($this->Settings->state == $supplier_details->state), $tax_details)) {
                            $total_cgst += $gst_data['cgst'];
                            $total_sgst += $gst_data['sgst'];
                            $total_igst += $gst_data['igst'];
                        }
                    }

                    $product_tax += $pr_item_tax;
                    $subtotal = $this->sma->formatDecimal((($item_net_cost * $item_unit_quantity) + $pr_item_tax), 4);
                    $unit = $this->siteprocurment->getUnitByID($item_unit);

                    $product = array(
                        'product_id' => $item_id,
                        'product_code' => $item_code,
                        'product_name' => $item_name,
                        'option_id' => $item_option,
                        'net_unit_cost' => $item_net_cost,
                        'unit_cost' => $this->sma->formatDecimal($item_net_cost + $item_tax),
                        'quantity' => $item_quantity,
                        'product_unit_id' => $item_unit,
                        'product_unit_code' => $unit->code,
                        'unit_quantity' => $item_unit_quantity,
                        'quantity_balance' => $item_quantity,
                        'warehouse_id' => $purchase->warehouse_id,
                        'item_tax' => $pr_item_tax,
                        'tax_rate_id' => $item_tax_rate,
                        'tax' => $tax,
                        'discount' => $item_discount,
                        'item_discount' => $pr_item_discount,
                        'subtotal' => $this->sma->formatDecimal($subtotal),
                        'real_unit_cost' => $real_unit_cost,
                        'purchase_item_id' => $purchase_item_id,
                        'status' => 'received',
                    );

                    $products[] = ($product+$gst_data);
                    $total += $this->sma->formatDecimal(($item_net_cost * $item_unit_quantity), 4);
                }
            }
            if (empty($products)) {
                $this->form_validation->set_rules('product', lang("order_items"), 'required');
            } else {
                krsort($products);
            }

            $order_discount = $this->siteprocurment->calculateDiscount($this->input->post('discount') ? $this->input->post('order_discount') : null, ($total + $product_tax));
            $total_discount = $this->sma->formatDecimal(($order_discount + $product_discount), 4);
            $order_tax = $this->siteprocurment->calculateOrderTax($this->input->post('order_tax'), ($total + $product_tax - $total_discount));
            $total_tax = $this->sma->formatDecimal(($product_tax + $order_tax), 4);
            $grand_total = $this->sma->formatDecimal(($total + $total_tax + $this->sma->formatDecimal($return_surcharge) - $order_discount), 4);
            if($this->siteprocurment->GETaccessModules('')){
				$approved_by = $this->session->userdata('user_id');
			}
			if($status == 'process'){
				$un = $this->siteprocurment->getUsersnotificationWithoutSales();
				foreach($un as $un_row)
				$notification = array(
					'user_id' => $un_row->user_id,
					'group_id' => $un_row->group_id,
					'title' => 'Purchases Request',
					'message' => 'The new purchase request has been created. REF No:'.$reference.', Date:'.$date,
					'created_by' => $this->session->userdata('user_id'),
					'created_on' => date('Y-m-d H:i:s'),
				);	
				$this->siteprocurment->insertNotification($notification);
			}
            $data = array('date' => $date,
                'purchase_id' => $id,
                'reference_no' => $purchase->reference_no,
                'supplier_id' => $purchase->supplier_id,
                'supplier' => $purchase->supplier,
                'warehouse_id' => $purchase->warehouse_id,
                'note' => $note,
                'total' => $total,
                'product_discount' => $product_discount,
                'order_discount_id' => ($this->input->post('discount') ? $this->input->post('order_discount') : null),
                'order_discount' => $order_discount,
                'total_discount' => $total_discount,
                'product_tax' => $product_tax,
                'order_tax_id' => $this->input->post('order_tax'),
                'order_tax' => $order_tax,
                'total_tax' => $total_tax,
                'surcharge' => $this->sma->formatDecimal($return_surcharge),
                'grand_total' => $grand_total,
                'created_by' => $this->session->userdata('user_id'),
                'return_purchase_ref' => $reference,
                'status' => 'returned',
                'payment_status' => $purchase->payment_status == 'paid' ? 'due' : 'process',
            );
            if ($this->Settings->indian_gst) {
                $data['cgst'] = $total_cgst;
                $data['sgst'] = $total_sgst;
                $data['igst'] = $total_igst;
            }

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

            // $this->sma->print_arrays($data, $products);
        }

        if ($this->form_validation->run() == true && $this->purchase_orders_model->addPurchase($data, $products)) {
            $this->session->set_flashdata('message', lang("return_purchase_added"));
            admin_redirect("procurment/purchase_orders");
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['inv'] = $purchase;
            if ($this->data['inv']->status != 'received' && $this->data['inv']->status != 'partial') {
                $this->session->set_flashdata('error', lang("purchase_status_x_received"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
            if ($this->Settings->disable_editing) {
                if ($this->data['inv']->date <= date('Y-m-d', strtotime('-'.$this->Settings->disable_editing.' days'))) {
                    $this->session->set_flashdata('error', sprintf(lang("purchase_x_edited_older_than_x_days"), $this->Settings->disable_editing));
                    redirect($_SERVER["HTTP_REFERER"]);
                }
            }
            $inv_items = $this->purchase_orders_model->getAllPurchaseItems($id);
             krsort($inv_items);
            $c = rand(100000, 9999999);
            foreach ($inv_items as $item) {
                $row = $this->siteprocurment->getProductByID($item->product_id);
                $row->expiry = (($item->expiry && $item->expiry != '0000-00-00') ? $this->sma->hrsd($item->expiry) : '');
                $row->base_quantity = $item->quantity;
                $row->base_unit = $row->unit ? $row->unit : $item->product_unit_id;
                $row->base_unit_cost = $row->cost ? $row->cost : $item->unit_cost;
                $row->unit = $item->product_unit_id;
                $row->qty = $item->unit_quantity;
                $row->oqty = $item->unit_quantity;
                $row->purchase_item_id = $item->id;
                $row->supplier_part_no = $item->supplier_part_no;
                $row->received = $item->quantity_received ? $item->quantity_received : $item->quantity;
                $row->quantity_balance = $item->quantity_balance + ($item->quantity-$row->received);
                $row->discount = $item->discount ? $item->discount : '0';
                $options = $this->purchase_orders_model->getProductOptions($row->id);
                $row->option = !empty($item->option_id) ? $item->option_id : '';
                $row->real_unit_cost = $item->real_unit_cost;
                $row->cost = $this->sma->formatDecimal($item->net_unit_cost + ($item->item_discount / $item->quantity));
                $row->tax_rate = $item->tax_rate_id;
                unset($row->details, $row->product_details, $row->price, $row->file, $row->product_group_id);
                $units = $this->siteprocurment->getUnitsByBUID($row->base_unit);
                $tax_rate = $this->siteprocurment->getTaxRateByID($row->tax_rate);
                $ri = $this->Settings->item_addition ? $row->id : $c;

                $pr[$ri] = array('id' => $c, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $row, 'units' => $units, 'tax_rate' => $tax_rate, 'options' => $options);

                $c++;
            }

            $this->data['inv_items'] = json_encode($pr);
            $this->data['id'] = $id;
            $this->data['reference'] = '';
            $this->data['tax_rates'] = $this->siteprocurment->getAllTaxRates();
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('procurment/purchase_orders'), 'page' => lang('purchase_orders')), array('link' => '#', 'page' => lang('return_purchase')));
            $meta = array('page_title' => lang('return_purchase'), 'bc' => $bc);
            $this->page_construct('procurment/purchase_orders/return_purchase', $meta, $this->data);
        }
    }

    public function getSupplierCost($supplier_id, $product)
    {
        switch ($supplier_id) {
            case $product->supplier1:
                $cost =  $product->supplier1price > 0 ? $product->supplier1price : $product->cost;
                break;
            case $product->supplier2:
                $cost =  $product->supplier2price > 0 ? $product->supplier2price : $product->cost;
                break;
            case $product->supplier3:
                $cost =  $product->supplier3price > 0 ? $product->supplier3price : $product->cost;
                break;
            case $product->supplier4:
                $cost =  $product->supplier4price > 0 ? $product->supplier4price : $product->cost;
                break;
            case $product->supplier5:
                $cost =  $product->supplier5price > 0 ? $product->supplier5price : $product->cost;
                break;
            default:
                $cost = $product->cost;
        }
        return $cost;
    }

    public function update_status($id)
    {

        $this->form_validation->set_rules('status', lang("status"), 'required');

        if ($this->form_validation->run() == true) {
            $status = $this->input->post('status');
            $note = $this->sma->clear_tags($this->input->post('note'));
        } elseif ($this->input->post('update')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'sales');
        }

        if ($this->form_validation->run() == true && $this->purchase_orders_model->updateStatus($id, $status, $note)) {
            $this->session->set_flashdata('message', lang('status_updated'));
            admin_redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'sales');
        } else {

            $this->data['inv'] = $this->purchase_orders_model->getPurchase_ordersByID($id);
            $this->data['returned'] = FALSE;
            if ($this->data['inv']->status == 'returned' || $this->data['inv']->return_id) {
                $this->data['returned'] = TRUE;
            }
            $this->data['modal_js'] = $this->siteprocurment->modal_js();
            $this->load->view($this->theme.'purchase_orders/update_status', $this->data);

        }
    }

}
