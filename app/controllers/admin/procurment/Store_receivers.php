<?php defined('BASEPATH') or exit('No direct script access allowed');

class Store_receivers extends MY_Controller
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
        $this->lang->admin_load('procurment/store_receivers', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('procurment/store_receivers_model');
        $this->digital_upload_path = 'files/';
        $this->upload_path = 'assets/uploads/';
        $this->thumbs_path = 'assets/uploads/thumbs/';
        $this->image_types = 'gif|jpg|jpeg|png|tif';
        $this->digital_file_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
        $this->allowed_file_size = '1024';
        $this->data['logo'] = true;
		
		$this->Muser_id = $this->session->userdata('user_id');
		$this->Maccess_id = 8;

    }
	
	public function store_receivers_list(){
		$poref =  $this->input->get('poref');
		
		$data['store_receivers'] = $this->store_receivers_model->getRequestByID($poref);
		
		$inv_items = $this->store_receivers_model->getAllRequestItems($poref);
		
		 krsort($inv_items);
		$c = rand(100000, 9999999);
		foreach ($inv_items as $item) {
			
			
			
			$row = $this->siteprocurment->getProductByID($item->product_id);
			$row->expiry = (($item->expiry && $item->expiry != '0000-00-00') ? $this->sma->hrsd($item->expiry) : '');
			$row->mfg = (($item->mfg && $item->mfg != '0000-00-00') ? $this->sma->hrsd($item->mfg) : '');
			
			$row->batch_no = $item->batch_no ? $item->batch_no : '';
			
			//$current_quantity = $this->store_receivers_model->getAvailableQTY($item->product_id, $data['store_receivers']->to_store_id);
			//$pending_quantity = $this->store_receivers_model->checkPendingQTY($item->product_id, $item->quantity, $data['store_receivers']->id);
			
			$row->current_quantity = $item->available_quantity;
			$row->pending_quantity = $item->pending_quantity;
			$row->transfer_quantity = $item->transfer_quantity;
						
			$row->base_quantity = $item->quantity;
			$row->base_unit = $row->unit ? $row->unit : $item->product_unit_id;
			$row->base_unit_cost = $row->cost ? $row->cost : $item->unit_cost;
			$row->unit = $item->product_unit_id;
			$row->qty = $item->unit_quantity;
			$row->oqty = $item->quantity;
			$row->supplier_part_no = $item->supplier_part_no;
			$row->received = $item->quantity_received ? $item->quantity_received : $item->quantity;
			$row->quantity_balance = $item->quantity_balance + ($item->quantity-$row->received);
			$row->discount = $item->discount ? $item->discount : '0';
			$options = $this->store_receivers_model->getProductOptions($row->id);
			$row->option = $item->option_id;
                $row->real_unit_cost = $item->real_unit_price;
                $row->cost = $this->sma->formatDecimal($item->net_unit_price + ($item->item_discount / $item->quantity));
				
                $row->tax_rate = $item->tax_rate_id;
				 $row->tax_method = $item->item_tax_method;
                unset($row->details, $row->product_details, $row->price, $row->file, $row->product_group_id);
                $units = $this->siteprocurment->getUnitsByBUID($row->base_unit);
                $tax_rate = $this->siteprocurment->getTaxRateByID($row->tax_rate);
			$ri = $this->Settings->item_addition ? $row->id : $row->id;
		
			$pr[$ri] = array('id' => $c, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")",
				'row' => $row, 'tax_rate' => $row->tax_rate, 'units' => $units, 'options' => $options);
			$c++;
		}
		
		$data['store_receiversitem'] = $pr;
		
		
		
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
		$data = $this->store_receivers_model->getSupplierdetails($supplier_id);
		
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

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('store_receivers')));
        $meta = array('page_title' => lang('store_receivers'), 'bc' => $bc);
        $this->page_construct('procurment/store_receivers/index', $meta, $this->data);

    }

    
	
	public function getStore_receivers($warehouse_id = null)
    { 

               
        $detail_link = anchor('admin/procurment/store_receivers/view/$1', '<i class="fa fa-file-text-o"></i> ' . lang('store_receivers_details'));
        $payments_link = anchor('admin/procurment/store_receivers/payments/$1', '<i class="fa fa-money"></i> ' . lang('view_payments'), 'data-toggle="modal" data-target="#myModal"');
        $add_payment_link = anchor('admin/procurment/store_receivers/add_payment/$1', '<i class="fa fa-money"></i> ' . lang('add_payment'), 'data-toggle="modal" data-target="#myModal"');
        $email_link = anchor('admin/procurment/store_receivers/email/$1', '<i class="fa fa-envelope"></i> ' . lang('email_store_receivers'), 'data-toggle="modal" data-target="#myModal"');
        $edit_link = anchor('admin/procurment/store_receivers/edit/$1', '<i class="fa fa-edit"></i> ' . lang('edit_store_receivers'));
        $pdf_link = anchor('admin/procurment/store_receivers/pdf/$1', '<i class="fa fa-file-pdf-o"></i> ' . lang('download_pdf'));
        $print_barcode = anchor('admin/procurment/products/print_barcodes/?purchase=$1', '<i class="fa fa-print"></i> ' . lang('print_barcodes'));
        $return_link = anchor('admin/procurment/store_receivers/return_purchase/$1', '<i class="fa fa-angle-double-left"></i> ' . lang('return_purchase'));
        $delete_link = "<a href='#' class='po' title='<b>" . $this->lang->line("delete_quotation") . "</b>' data-content=\"<p>"
        . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('procurment/store_receivers/delete/$1') . "'>"
        . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
        . lang('delete_store_receivers') . "</a>";
        /*$action = '<div class="text-center"><div class="btn-group text-left">'
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
            <li>' . $delete_link . '</li>
        </ul>
    </div></div>';
        //$action = '<div class="text-center">' . $detail_link . ' ' . $edit_link . ' ' . $email_link . ' ' . $delete_link . '</div>';
// echo "string";exit;
        $this->load->library('datatables');
        if ($warehouse_id) {        
        // echo "fg";exit;    
		$this->datatables
			 ->select("pro_store_receivers.id, pro_store_receivers.date, pro_store_receivers.reference_no, pro_store_receivers.req_reference_no, f.name as from_name, t.name as to_name, SUM(sti.transfer_quantity) as transfer_quantity, pro_store_receivers.status")
                ->from('pro_store_receivers')
				->join('pro_stores f', 'f.id = pro_store_receivers.from_store_id', 'left')
				->join('pro_store_receiver_items sti', 'sti.store_receiver_id = pro_store_receivers.id', 'left')
				->join('pro_stores t', 't.id = pro_store_receivers.to_store_id', 'left')
				 ->where('pro_store_receivers.warehouse_id', $warehouse_id);
           /* $this->datatables
                ->select("st.id, DATE_FORMAT(st.date, '%Y-%m-%d %T') as date, st.reference_no, st.req_reference_no, st.supplier, st.total, st.total_discount, st.total_tax,  SUM(sti.transfer_quantity) as transfer_quantity, st.status")
                ->from('store_receivers st')
				->join('store_receiver_items sti', 'sti.store_receiver_id = st.id', 'left')
                ->where('st.warehouse_id', $warehouse_id)->group_by('st.id');*/
        } else {
            // echo "sdsd";exit;
           $this->datatables
			 ->select("pro_store_receivers.id, pro_store_receivers.date, pro_store_receivers.reference_no, pro_store_receivers.req_reference_no, f.name as from_name, t.name as to_name, SUM(sti.transfer_quantity) as transfer_quantity, pro_store_receivers.status")
                ->from('pro_store_receivers')
				->join('pro_stores f', 'f.id = pro_store_receivers.from_store_id', 'left')
				->join('pro_store_receiver_items sti', 'sti.store_receiver_id = pro_store_receivers.id', 'left')
				->join('pro_stores t', 't.id = pro_store_receivers.to_store_id', 'left');
                
        }
        // $this->datatables->where('status !=', 'returned');
        /*if (!$this->Customer && !$this->Supplier && !$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $this->datatables->where('pro_store_receivers.created_by', $this->session->userdata('user_id'));
        } elseif ($this->Supplier) {
            $this->datatables->where('pro_store_receivers.customer_id', $this->session->userdata('user_id'));
        }*/
		$this->datatables->group_by('pro_store_receivers.id');
        $this->datatables->add_column("Actions", $action, "pro_store_receivers.id");

        echo $this->datatables->generate();
    }

    /* ----------------------------------------------------------------------------- */

    public function modal_view($store_receivers_id = null)
    {
        //$this->sma->checkPermissions('index', true);

        if ($this->input->get('id')) {
            $store_receivers_id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $po = $this->store_receivers_model->getStore_receiversByID($store_receivers_id);
        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($po->created_by, true);
        }
        $this->data['rows'] = $this->store_receivers_model->getAllStore_receiversItems($store_receivers_id);
        $this->data['supplier'] = $this->siteprocurment->getCompanyByID($po->customer_id);
        $this->data['warehouse'] = $this->siteprocurment->getWarehouseByID($po->warehouse_id);
        $this->data['inv'] = $po;
        $this->data['payments'] = $this->store_receivers_model->getPaymentsForPurchase($store_receivers_id);
        $this->data['created_by'] = $this->siteprocurment->getUser($po->created_by);
        $this->data['updated_by'] = $po->updated_by ? $this->siteprocurment->getUser($po->updated_by) : null;
        // $this->data['return_purchase'] = $po->return_id ? $this->store_receivers_model->getStore_receiversByID($po->return_id) : NULL;
        // $this->data['return_rows'] = $po->return_id ? $this->store_receivers_model->getAllStore_receiversItems($po->return_id) : NULL;

        $this->load->view($this->theme . 'store_receivers/modal_view', $this->data);

    }

    public function view($store_receivers_id = null)
    {
		
        //$this->sma->checkPermissions('index');

        if ($this->input->get('id')) {
             $store_receivers_id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $po = $this->store_receivers_model->getStore_receiversByID($store_receivers_id);
		
        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($po->created_by);
        }
        $this->data['rows'] = $this->store_receivers_model->getAllStore_receiversItems($store_receivers_id);
        $this->data['supplier'] = $this->siteprocurment->getCompanyOrderByID($po->customer_id);
        $this->data['warehouse'] = $this->siteprocurment->getWarehouseOrderByID($po->warehouse_id);
        $this->data['inv'] = $po;
        //$this->data['payments'] = $this->store_receivers_model->getPaymentsForStore_receivers($store_receivers_id);
        $this->data['created_by'] = $this->siteprocurment->getUser($po->created_by);
        $this->data['updated_by'] = $po->updated_by ? $this->siteprocurment->getUser($po->updated_by) : null;
        $this->data['return_purchase'] = $po->return_id ? $this->store_receivers_model->getStore_receiversByID($po->return_id) : NULL;
        $this->data['return_rows'] = $po->return_id ? $this->store_receivers_model->getAllStore_receiversItems($po->return_id) : NULL;

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('procurment/store_receivers'), 'page' => lang('store_receivers')), array('link' => '#', 'page' => lang('view')));
        $meta = array('page_title' => lang('view_store_receivers_details'), 'bc' => $bc);
        $this->page_construct('procurment/store_receivers/view', $meta, $this->data);

    }

    /* ----------------------------------------------------------------------------- */

//generate pdf and force to download

    public function pdf($store_receivers_id = null, $view = null, $save_bufffer = null)
    {
        //$this->sma->checkPermissions();

        if ($this->input->get('id')) {
            $store_receivers_id = $this->input->get('id');
        }

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $po = $this->store_receivers_model->getStore_receiversByID($store_receivers_id);

        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($po->created_by);
        }
        $this->data['rows'] = $this->store_receivers_model->getAllStore_receiversItems($store_receivers_id);
        $this->data['supplier'] = $this->siteprocurment->getCompanyByID($po->customer_id);
        $this->data['warehouse'] = $this->siteprocurment->getWarehouseByID($po->warehouse_id);
        $this->data['created_by'] = $this->siteprocurment->getUser($po->created_by);
        $this->data['inv'] = $po;
        $this->data['return_purchase'] = $po->return_id ? $this->store_receivers_model->getStore_receiversByID($po->return_id) : NULL;
        $this->data['return_rows'] = $po->return_id ? $this->store_receivers_model->getAllStore_receiversItems($po->return_id) : NULL;
        $name = $this->lang->line("pro_store_receivers") . "_" . str_replace('/', '_', $po->reference_no) . ".pdf";
        $html = $this->load->view($this->theme . 'store_receivers/pdf', $this->data, true);
        if (! $this->Settings->barcode_img) {
            $html = preg_replace("'\<\?xml(.*)\?\>'", '', $html);
        }
        if ($view) {
            $this->load->view($this->theme . 'store_receivers/pdf', $this->data);
        } elseif ($save_bufffer) {
            return $this->sma->generate_pdf($html, $name, $save_bufffer);
        } else {
            $this->sma->generate_pdf($html, $name);
        }

    }

    public function combine_pdf($store_receivers_id)
    {
        //$this->sma->checkPermissions('pdf');

        foreach ($store_receivers_id as $store_receivers_id) {

            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
            $inv = $this->store_receivers_model->getStore_receiversByID($store_receivers_id);
            if (!$this->session->userdata('view_right')) {
                $this->sma->view_rights($inv->created_by);
            }
            $this->data['rows'] = $this->store_receivers_model->getAllStore_receiversItems($store_receivers_id);
            $this->data['supplier'] = $this->siteprocurment->getCompanyByID($inv->supplier_id);
            $this->data['warehouse'] = $this->siteprocurment->getWarehouseByID($inv->warehouse_id);
            $this->data['created_by'] = $this->siteprocurment->getUser($inv->created_by);
            $this->data['inv'] = $inv;
            $this->data['return_purchase'] = $inv->return_id ? $this->store_receivers_model->getStore_receiversByID($inv->return_id) : NULL;
            $this->data['return_rows'] = $inv->return_id ? $this->store_receivers_model->getAllStore_receiversItems($inv->return_id) : NULL;
            $inv_html = $this->load->view($this->theme . 'store_receivers/pdf', $this->data, true);
            if (! $this->Settings->barcode_img) {
                $inv_html = preg_replace("'\<\?xml(.*)\?\>'", '', $inv_html);
            }
            $html[] = array(
                'content' => $inv_html,
                'footer' => '',
            );
        }

        $name = lang("pro_store_receivers") . ".pdf";
        $this->sma->generate_pdf($html, $name);

    }

    public function email($store_receivers_id = null)
    {

        //$this->sma->checkPermissions(false, true);

        if ($this->input->get('id')) {
            $store_receivers_id = $this->input->get('id');
        }
        $po = $this->store_receivers_model->getStore_receiversByID($store_receivers_id);
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
            $attachment = $this->pdf($store_receivers_id, null, 'S');

            try {
                if ($this->sma->send_email($to, $subject, $message, null, null, $attachment, $cc, $bcc)) {
                    delete_files($attachment);
                    $this->db->update('store_receivers', array('status' => 'ordered'), array('id' => $store_receivers_id));
                    $this->session->set_flashdata('message', $this->lang->line("email_sent"));
                    admin_redirect("procurment/store_receivers");
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

            if (file_exists('./themes/' . $this->Settings->theme . '/admin/views/email_templates/store_receivers.html')) {
                $store_receivers_temp = file_get_contents('themes/' . $this->Settings->theme . '/admin/views/email_templates/store_receivers.html');
            } else {
                $store_receivers_temp = file_get_contents('./themes/default/admin/views/email_templates/store_receivers.html');
            }
            $this->data['subject'] = array('name' => 'subject',
                'id' => 'subject',
                'type' => 'text',
                'value' => $this->form_validation->set_value('subject', lang('store_receivers').' (' . $po->reference_no . ') '.lang('from').' ' . $this->Settings->site_name),
            );
            $this->data['note'] = array('name' => 'note',
                'id' => 'note',
                'type' => 'text',
                'value' => $this->form_validation->set_value('note', $store_receivers_temp),
            );
            $this->data['supplier'] = $this->siteprocurment->getCompanyByID($po->customer_id);

            $this->data['id'] = $store_receivers_id;
            $this->data['modal_js'] = $this->siteprocurment->modal_js();
            $this->load->view($this->theme . 'store_receivers/email', $this->data);

        }
    }

    /* -------------------------------------------------------------------------------------------------------------------------------- */

    public function add($store_receivers_id = null)
    {
        //$this->sma->checkPermissions();

       // $this->form_validation->set_message('is_natural_no_zero', $this->lang->line("no_zero_required"));
        $this->form_validation->set_rules('warehouse', $this->lang->line("warehouse"), 'required|is_natural_no_zero');
        //$this->form_validation->set_rules('supplier', $this->lang->line("supplier"), 'required');
		$this->form_validation->set_rules('requestnumber', $this->lang->line("requestnumber"), 'required');
		/*$this->form_validation->set_rules('invoice_no', $this->lang->line("invoice_no"), 'required');
		$this->form_validation->set_rules('store_receivers_no', $this->lang->line("pro_store_receivers_no"), 'required');*/

        $this->session->unset_userdata('csrf_token');
        if ($this->form_validation->run() == true) { 
		
			         
            
			$reference = 'STOREREC'.date('YmdHis');            
			$date = date('Y-m-d H:i:s');
			
            $warehouse_id = $this->input->post('warehouse');
            $supplier_id = $this->input->post('supplier');
            $status = $this->input->post('status');
            $shipping = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
            $supplier_details = $this->siteprocurment->getCompanyByID($supplier_id);
            $supplier = $supplier_details->company != '-'  ? $supplier_details->company : $supplier_details->name;
            $note = $this->sma->clear_tags($this->input->post('note'));
            // $payment_term = $this->input->post('payment_term');
             //$due_date = $payment_term ? date('Y-m-d', strtotime('+' . ' days', strtotime($date))) : null;

            $total = 0;
            $product_tax = 0;
            $product_discount = 0;
            $i = sizeof($_POST['product']);
            $gst_data = [];
            $total_cgst = $total_sgst = $total_igst = 0;
            for ($r = 0; $r < $i; $r++) {
				
				
				
                $item_code = $_POST['product'][$r];
                $item_net_cost = $this->sma->formatDecimal($_POST['net_cost'][$r]);
                $unit_cost = $this->sma->formatDecimal($_POST['unit_cost'][$r]);
				$unit_cost_new = $this->sma->formatDecimal($_POST['unit_cost'][$r]);
                $real_unit_cost = $this->sma->formatDecimal($_POST['real_unit_cost'][$r]);
                $item_unit_quantity = $_POST['quantity'][$r];
				
				 $item_available_quantity = $_POST['available_qty'][$r];
				 $item_transfer_quantity = $_POST['transfer_qty'][$r];
				 $item_pending_quantity = $_POST['pending_qty'][$r];
				
				if($item_pending_quantity == 0){
					$check_quantity[] = 0;
				}else{
					$check_quantity[] = 1;
				}

                $item_option = isset($_POST['product_option'][$r]) && !empty($_POST['product_option'][$r]) && $_POST['product_option'][$r] != 'false' ? $_POST['product_option'][$r] : null;               
                $item_tax_rate = isset($_POST['product_tax'][$r]) ? $_POST['product_tax'][$r] : null;
                $item_discount = isset($_POST['product_discount'][$r]) ? $_POST['product_discount'][$r] : null;
				$item_tax_method = isset($_POST['tax1'][$r]) ? $_POST['tax1'][$r] : null;
                // $item_expiry = (isset($_POST['expiry'][$r]) && !empty($_POST['expiry'][$r])) ? $this->sma->fsd($_POST['expiry'][$r]) : null;
                $supplier_part_no = (isset($_POST['part_no'][$r]) && !empty($_POST['part_no'][$r])) ? $_POST['part_no'][$r] : null;
                $item_unit = $_POST['product_unit'][$r];
                $item_quantity = $_POST['quantity'][$r];

                if (isset($item_code) && isset($item_quantity)) {
                    $product_details = $this->store_receivers_model->getProductByCode($item_code);
					
					
					if($this->input->post('request_type') == 'new'){
						$from_store = $this->store_receivers_model->getStoreMasterProductID($product_details->id, $this->input->post('to_store_id'));
						$to_current_qty = $this->store_receivers_model->getCurrentQuantityID($product_details->id, $this->input->post('from_store_id'));
						$from_current_qty = $this->store_receivers_model->getCurrentQuantityID($product_details->id, $this->input->post('to_store_id'));
						
						$transfer_quantity[$product_details->id] = $item_transfer_quantity;
						
						$sum = 0;
						foreach($from_store as $from_store_row){
						
						
						if($transfer_quantity[$product_details->id] > 0 ){
							
							$transfer_quantity[$product_details->id] = $from_store_row->quantity - $transfer_quantity[$product_details->id];
							
							if($transfer_quantity[$product_details->id] < 0 ){
								$from_qty = $from_store_row->quantity;
								$to_qty = $from_store_row->quantity;
								$sum+= $to_qty;
								if($to_current_qty > 0){
									$tcurrent_quantity = $to_current_qty + $to_qty;
								}else{
									$tcurrent_quantity = $sum;
								}
								
								$stock[] = array('transacton_type' => 'IN', 'product_id' => $from_store_row->product_id, 'current_quantity' => $tcurrent_quantity, 'store_id' => $this->input->post('from_store_id'), 'type' => 'transfer', 'quantity' => $to_qty, 'purchase_invoice_id' => $from_store_row->purchase_invoice_id, 'purchase_batch_no' => $from_store_row->purchase_batch_no, 'created_on' => date('Y-m-d H:i:s'), 'created_by' =>  $this->session->userdata('user_id'));
								
								if($from_current_qty > 0){
									$fcurrent_quantity = $from_current_qty - $from_qty;
								}else{
									$fcurrent_quantity = 0;
								}
								
								$stock[] = array('transacton_type' => 'OUT', 'product_id' => $from_store_row->product_id,  'current_quantity' => $fcurrent_quantity, 'store_id' => $this->input->post('to_store_id'), 'type' => 'transfer', 'quantity' => $from_qty, 'purchase_invoice_id' => $from_store_row->purchase_invoice_id, 'purchase_batch_no' => $from_store_row->purchase_batch_no, 'created_on' => date('Y-m-d H:i:s'), 'created_by' =>  $this->session->userdata('user_id'));
								
								$stockupdate[$from_store_row->id] =  array('status' => 1);
								
								$transfer_quantity[$product_details->id] = abs($transfer_quantity[$product_details->id]);
							}else{
																
								$from_qty = $from_store_row->quantity - $transfer_quantity[$product_details->id];
								$to_qty = $from_store_row->quantity - $transfer_quantity[$product_details->id];
								$sum+= $to_qty;
								if($to_current_qty > 0){
									$tcurrent_quantity = $to_current_qty + $to_qty;
								}else{
									$tcurrent_quantity = $sum;
								}
								
								$stock[] = array('transacton_type' => 'IN', 'product_id' => $from_store_row->product_id, 'current_quantity' => $tcurrent_quantity, 'store_id' => $this->input->post('from_store_id'), 'type' => 'transfer', 'quantity' => $to_qty, 'purchase_invoice_id' => $from_store_row->purchase_invoice_id, 'purchase_batch_no' => $from_store_row->purchase_batch_no, 'created_on' => date('Y-m-d H:i:s'), 'created_by' =>  $this->session->userdata('user_id'));
								
								if($from_current_qty > 0){
									$fcurrent_quantity = $from_current_qty - $from_qty;
								}else{
									$fcurrent_quantity = 0;
								}
								
								$stock[] = array('transacton_type' => 'OUT', 'product_id' => $from_store_row->product_id, 'current_quantity' => $fcurrent_quantity, 'store_id' => $this->input->post('to_store_id'), 'type' => 'transfer', 'quantity' => $from_qty, 'purchase_invoice_id' => $from_store_row->purchase_invoice_id, 'purchase_batch_no' => $from_store_row->purchase_batch_no, 'created_on' => date('Y-m-d H:i:s'), 'created_by' =>  $this->session->userdata('user_id'));
								
								$stockupdate[$from_store_row->id] =  array('status' => 1);
								
								break; return;	
							}
							
						}
						
					}
					}elseif($this->input->post('request_type') == 'return'){
						
						$from_store = $this->store_receivers_model->getStoreMasterProductID($product_details->id, $this->input->post('from_store_id'));
						$to_current_qty = $this->store_receivers_model->getCurrentQuantityID($product_details->id, $this->input->post('to_store_id'));
						$from_current_qty = $this->store_receivers_model->getCurrentQuantityID($product_details->id, $this->input->post('from_store_id'));
						
						$transfer_quantity[$product_details->id] = $item_transfer_quantity;
						
						$sum = 0;
						foreach($from_store as $from_store_row){
							
							
							if($transfer_quantity[$product_details->id] > 0 ){
								
								$transfer_quantity[$product_details->id] = $from_store_row->quantity - $transfer_quantity[$product_details->id];
								
								if($transfer_quantity[$product_details->id] < 0 ){
									$from_qty = $from_store_row->quantity;
									$to_qty = $from_store_row->quantity;
									$sum+= $to_qty;
									if($to_current_qty > 0){
										$tcurrent_quantity = $to_current_qty + $to_qty;
									}else{
										$tcurrent_quantity = $sum;
									}
									
									$stock[] = array('transacton_type' => 'IN', 'product_id' => $from_store_row->product_id, 'current_quantity' => $tcurrent_quantity, 'store_id' => $this->input->post('to_store_id'), 'type' => 'return', 'quantity' => $to_qty, 'purchase_invoice_id' => $from_store_row->purchase_invoice_id, 'purchase_batch_no' => $from_store_row->purchase_batch_no, 'created_on' => date('Y-m-d H:i:s'), 'created_by' =>  $this->session->userdata('user_id'));
									
									if($from_current_qty > 0){
										$fcurrent_quantity = $from_current_qty - $from_qty;
									}else{
										$fcurrent_quantity = 0;
									}
									
									$stock[] = array('transacton_type' => 'OUT', 'product_id' => $from_store_row->product_id,  'current_quantity' => $fcurrent_quantity, 'store_id' => $this->input->post('from_store_id'), 'type' => 'return', 'quantity' => $from_qty, 'purchase_invoice_id' => $from_store_row->purchase_invoice_id, 'purchase_batch_no' => $from_store_row->purchase_batch_no, 'created_on' => date('Y-m-d H:i:s'), 'created_by' =>  $this->session->userdata('user_id'));
									
									$stockupdate[$from_store_row->id] =  array('status' => 1);
									
									$transfer_quantity[$product_details->id] = abs($transfer_quantity[$product_details->id]);
								}else{
																	
									$from_qty = $from_store_row->quantity - $transfer_quantity[$product_details->id];
									$to_qty = $from_store_row->quantity - $transfer_quantity[$product_details->id];
									$sum+= $to_qty;
									if($to_current_qty > 0){
										$tcurrent_quantity = $to_current_qty + $to_qty;
									}else{
										$tcurrent_quantity = $sum;
									}
									
									$stock[] = array('transacton_type' => 'IN', 'product_id' => $from_store_row->product_id, 'current_quantity' => $tcurrent_quantity, 'store_id' => $this->input->post('to_store_id'), 'type' => 'return', 'quantity' => $to_qty, 'purchase_invoice_id' => $from_store_row->purchase_invoice_id, 'purchase_batch_no' => $from_store_row->purchase_batch_no, 'created_on' => date('Y-m-d H:i:s'), 'created_by' =>  $this->session->userdata('user_id'));
									
									if($from_current_qty > 0){
										$fcurrent_quantity = $from_current_qty - $from_qty;
									}else{
										$fcurrent_quantity = 0;
									}
									
									$stock[] = array('transacton_type' => 'OUT', 'product_id' => $from_store_row->product_id, 'current_quantity' => $fcurrent_quantity, 'store_id' => $this->input->post('from_store_id'), 'type' => 'return', 'quantity' => $from_qty, 'purchase_invoice_id' => $from_store_row->purchase_invoice_id, 'purchase_batch_no' => $from_store_row->purchase_batch_no, 'created_on' => date('Y-m-d H:i:s'), 'created_by' =>  $this->session->userdata('user_id'));
									
									$stockupdate[$from_store_row->id] =  array('status' => 1);
									
									break; return;	
								}
								
							}
							
						}
					}
					
					
                    // if ($item_expiry) {
                    //     $today = date('Y-m-d');
                    //     if ($item_expiry <= $today) {
                    //         $this->session->set_flashdata('error', lang('product_expiry_date_issue') . ' (' . $product_details->name . ')');
                    //         redirect($_SERVER["HTTP_REFERER"]);
                    //     }
                    // }
                    // $unit_cost = $real_unit_cost;
                    $pr_discount = $this->siteprocurment->calculateDiscount($item_discount, $unit_cost);
                    $unit_cost = $this->sma->formatDecimal($unit_cost - $pr_discount);
					
                    $item_net_cost = $unit_cost;
                    $pr_item_discount = $this->sma->formatDecimal($pr_discount * $item_unit_quantity);
                    $product_discount += $pr_item_discount;
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
                    $subtotal = (($item_net_cost * $item_unit_quantity) + $pr_item_tax);
                    $unit = $this->siteprocurment->getUnitByID($item_unit);
/*common_store_receiver_items*/
                    $product = array(
                        'product_id' => $product_details->id,
                        'product_code' => $item_code,
                        'unit_price' => $unit_cost_new,
                        'product_name' => $product_details->name,
                        'option_id' => null,
                         'net_unit_price' => $item_net_cost,
                        'real_unit_price' => $unit_cost_new,
                        'quantity' => $item_quantity,
						'available_quantity' => $item_available_quantity,
						'transfer_quantity' => $item_transfer_quantity,
						'pending_quantity' => $item_pending_quantity,
                        'product_unit_id' => $item_unit,
                        'product_unit_code' => $unit->code,
                        'unit_quantity' => $item_unit_quantity,                       
                        'warehouse_id' => $warehouse_id,
                        'item_tax' => $pr_item_tax,                        
                        'tax' => $tax,
						'tax_rate_id' => $item_tax_rate,
                        'discount' => $item_discount,
                        'item_discount' => $pr_item_discount,
                        'subtotal' => $this->sma->formatDecimal($subtotal),                        
                         //'real_unit_price' => $real_unit_cost,                      
                        // 'status' => $status,                        
                    );

                    $products[] = $product;
                    // echo "<pre>";
                    // print_r($this->input->post());
                    // echo "</pre>";
                    // echo "<pre>";
                    // print_r($products);exit;
                    // echo "</pre>";
                    $total += $this->sma->formatDecimal(($item_net_cost * $item_unit_quantity), 4);
                }
            }
			
            if (empty($products)) {
                $this->form_validation->set_rules('product', lang("order_items"), 'required');
            } else {
                krsort($products);
            }

            $order_discount = $this->siteprocurment->calculateDiscount($this->input->post('discount'), ($total + $product_tax));
            $total_discount = $this->sma->formatDecimal(($order_discount + $product_discount), 4);
            $order_tax = $this->siteprocurment->calculateOrderTax($this->input->post('order_tax'), ($total + $product_tax - $total_discount));
            $total_tax = $this->sma->formatDecimal(($product_tax + $order_tax), 4);
            $grand_total = $this->sma->formatDecimal(($total + $total_tax + $this->sma->formatDecimal($shipping) - $order_discount), 4);
			
			$join_ref_no = $this->store_receivers_model->getReqBYID($this->input->post('requestnumber'));
            
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
					'reference_no' => $reference,
				'request_type' => $this->input->post('request_type'),
                //'supplier_id' => $supplier_id,
               // 'supplier' => $supplier,
			    'from_store_id' => $this->input->post('from_store_id'),
				'to_store_id' => $this->input->post('to_store_id'),
                'warehouse_id' => $warehouse_id,
				//'invoice_no' => $this->input->post('invoice_no'),
				//'store_receivers_details' => $this->input->post('store_receivers_details') ? $this->input->post('store_receivers_details') : '',
				//'store_receivers_no' => $this->input->post('store_receivers_no'),
				//'store_receivers_date' => $this->input->post('store_receivers_date'),
				//'store_receivers_expected_date' => $this->input->post('store_receivers_expected_date'),
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
                'status' => $status ? $status : '',
                'created_by' => $this->session->userdata('user_id'),
				'approved_by' => $approved_by ? $approved_by : 0,
				'approved_on' => $date,
				'requestnumber' => $this->input->post('requestnumber'),
				'requestdate' => $join_ref_no->date,
				'req_reference_no' => $join_ref_no->reference_no
               //  'payment_term' => $payment_term ? $payment_term : 0 ,
                // 'due_date' => $due_date,
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
			
			if($this->input->post('requestnumber') != ''){
				
				
				$store_receivers_array = array(
					'status' => 'completed',
				);
				
			}
			/*echo $this->input->post('request_type');
			echo '<pre>';
			print_r($stock);
			print_r($stockupdate);
			die;*/
           //$this->sma->print_arrays($data, $products, $store_receivers_array, $this->input->post('requestnumber'), $rstatus);
		   //die;
        }
		
		
		 
        if ($this->form_validation->run() == true && $this->store_receivers_model->addStore_receivers($data, $products, $store_receivers_array, $stock, $stockupdate, $this->input->post('requestnumber'))) {
            
            $this->session->set_userdata('remove_pols', 1);
            $this->session->set_flashdata('message', $this->lang->line("pro_store_receivers_added"));
            admin_redirect('procurment/store_receivers');
        } else {
			
			
            if ($store_receivers_id) {
                $this->data['store_receivers'] = $this->store_receivers_model->getStore_receiversByID($store_receivers_id);
                $supplier_id = $this->data['store_receivers']->supplier_id;
                $items = $this->store_receivers_model->getAllStore_receiversItems($store_receivers_id);
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
                            $options = $this->store_receivers_model->getProductOptions($crow->id);
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
                        $options = $this->store_receivers_model->getProductOptions($row->id);

                        $units = $this->siteprocurment->getUnitsByBUID($row->base_unit);
                        $tax_rate = $this->siteprocurment->getTaxRateByID($row->tax_rate);
                        $ri = $this->Settings->item_addition ? $row->id : $row->id;

                        $pr[$ri] = array('id' => $c, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")",
                            'row' => $row, 'tax_rate' => $row->tax_rate, 'units' => $units, 'options' => $options);
                        $c++;
                    }
                }
                $this->data['store_receivers_items'] = json_encode($pr);
            }

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['store_receivers_id'] = $store_receivers_id;
           $this->data['suppliers'] = $this->siteprocurment->getAllCompanies('supplier');
            $this->data['categories'] = $this->siteprocurment->getAllCategories();
           $this->data['tax_rates'] = $this->siteprocurment->getAllTaxRates();
            $this->data['warehouses'] = $this->siteprocurment->getAllWarehouses();
			$this->data['ref_requestnumber'] = @$_GET['ref'];
			 $this->data['requestnumber'] = $this->siteprocurment->getAllSTORETRANSNUMBER();
			$this->data['stores'] = $this->siteprocurment->getAllStores();
           $this->data['ponumber'] = ''; //$this->siteprocurment->getReference('po');
            $this->load->helper('string');
            $value = random_string('alnum', 20);
            $this->session->set_userdata('user_csrf', $value);
            $this->data['csrf'] = $this->session->userdata('user_csrf');
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('procurment/store_receivers'), 'page' => lang('store_receivers')), array('link' => '#', 'page' => lang('add_store_receivers')));
            $meta = array('page_title' => lang('add_store_receivers'), 'bc' => $bc);
            $this->page_construct('procurment/store_receivers/add', $meta, $this->data);
        }
    }

    /* ------------------------------------------------------------------------------------- */

    public function edit($id = null)
    {
        ////$this->sma->checkPermissions();

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
		
		
        $inv = $this->store_receivers_model->getStore_receiversByID($id);
        if ( $inv->status == 'completed') {
			$this->session->set_flashdata('error', lang("Do not allowed edit option"));
			admin_redirect("procurment/store_receivers");
		}
       /* if ($inv->status == 'returned' || $inv->return_id || $inv->return_purchase_ref) {
            $this->session->set_flashdata('error', lang('purchase_x_action'));
            admin_redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
        }*/
        /*if (!$this->session->userdata('edit_right')) {
            $this->sma->view_rights($inv->created_by);
        }*/
        // $this->form_validation->set_message('is_natural_no_zero', $this->lang->line("no_zero_required"));
        //$this->form_validation->set_rules('reference_no', $this->lang->line("ref_no"), 'required');
       $this->form_validation->set_rules('warehouse', $this->lang->line("warehouse"), 'required|is_natural_no_zero');
        //$this->form_validation->set_rules('supplier', $this->lang->line("supplier"), 'required');
		//$this->form_validation->set_rules('requestnumber', $this->lang->line("requestnumber"), 'required');
		
        $this->session->unset_userdata('csrf_token');
        if ($this->form_validation->run() == true) {

            $reference = $this->input->post('reference_no');
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = $inv->date;
            }
            $warehouse_id = $this->input->post('warehouse');           
            $supplier_id = $this->input->post('supplier');
            $status = $this->input->post('status');
            $shipping = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
            $supplier_details = $this->siteprocurment->getCompanyByID($supplier_id);
            $supplier = $supplier_details->company != '-'  ? $supplier_details->company : $supplier_details->name;
            $note = $this->sma->clear_tags($this->input->post('note'));
             $payment_term = $this->input->post('payment_term');
             $due_date = $payment_term ? date('Y-m-d', strtotime('+' . ' days', strtotime($date))) : null;

            $total = 0;
            $product_tax = 0;
            $product_discount = 0;
            $partial = false;
            $i = sizeof($_POST['product']);
            $gst_data = [];
            $total_cgst = $total_sgst = $total_igst = 0;
            for ($r = 0; $r < $i; $r++) {
                $item_code = $_POST['product'][$r];
                $item_net_cost = $this->sma->formatDecimal($_POST['net_cost'][$r]);
                $unit_cost = $this->sma->formatDecimal($_POST['unit_cost'][$r]);
				$unit_cost_new = $this->sma->formatDecimal($_POST['unit_cost'][$r]);
                $real_unit_cost = $this->sma->formatDecimal($_POST['real_unit_cost'][$r]);
                $item_unit_quantity = $_POST['quantity'][$r];
				
				$item_available_quantity = $_POST['available_qty'][$r];
				 $item_transfer_quantity = $_POST['transfer_qty'][$r];
				 $item_pending_quantity = $_POST['pending_qty'][$r];
				 
                // $quantity_received = $_POST['received_base_quantity'][$r];
                $item_option = isset($_POST['product_option'][$r]) && $_POST['product_option'][$r] != 'false' ? $_POST['product_option'][$r] : null;
                $item_tax_rate = isset($_POST['product_tax'][$r]) ? $_POST['product_tax'][$r] : null;
				$item_tax_method = isset($_POST['tax1'][$r]) ? $_POST['tax1'][$r] : 0;
                $item_discount = isset($_POST['product_discount'][$r]) ? $_POST['product_discount'][$r] : null;
				
                // $item_expiry = (isset($_POST['expiry'][$r]) && !empty($_POST['expiry'][$r])) ? $this->sma->fsd($_POST['expiry'][$r]) : null;
				// $item_mfg = (isset($_POST['mfg'][$r]) && !empty($_POST['mfg'][$r])) ? $this->sma->fsd($_POST['mfg'][$r]) : null;
				// $item_batch_no = isset($_POST['batch_no'][$r]) ? $_POST['batch_no'][$r] : null;
				
                // $supplier_part_no = (isset($_POST['part_no'][$r]) && !empty($_POST['part_no'][$r])) ? $_POST['part_no'][$r] : null;
                // $quantity_balance = $_POST['quantity_balance'][$r];
                // $ordered_quantity = $_POST['ordered_quantity'][$r];
                $item_unit = $_POST['product_unit'][$r];
                $item_quantity = $_POST['product_base_quantity'][$r];

                // if ($status == 'received' || $status == 'partial') {
                //     if ($quantity_received < $item_quantity) {
                //         $partial = 'partial';
                //     } elseif ($quantity_received > $item_quantity) {
                //         $this->session->set_flashdata('error', lang("received_more_than_ordered"));
                //         redirect($_SERVER["HTTP_REFERER"]);
                //     }
                //     $balance_qty =  $quantity_received - ($ordered_quantity - $quantity_balance);
                // } else {
                //     $balance_qty = $item_quantity;
                //     $quantity_received = $item_quantity;
                // }
                if (isset($item_code) && isset($item_quantity)) {
                    $product_details = $this->store_receivers_model->getProductByCode($item_code);
					
					if($inv->request_type == 'new'){
						$from_store = $this->store_receivers_model->getStoreMasterProductID($product_details->id, $inv->to_store_id);
						$to_current_qty = $this->store_receivers_model->getCurrentQuantityID($product_details->id, $inv->from_store_id);
						$from_current_qty = $this->store_receivers_model->getCurrentQuantityID($product_details->id, $inv->to_store_id);
						
						$transfer_quantity[$product_details->id] = $item_transfer_quantity;
						
						$sum = 0;
						foreach($from_store as $from_store_row){
							
							
							if($transfer_quantity[$product_details->id] > 0 ){
								
								$transfer_quantity[$product_details->id] = $from_store_row->quantity - $transfer_quantity[$product_details->id];
								
								if($transfer_quantity[$product_details->id] < 0 ){
									$from_qty = $from_store_row->quantity;
									$to_qty = $from_store_row->quantity;
									$sum+= $to_qty;
									if($to_current_qty > 0){
										$to_current_qty = $to_current_qty + $to_qty;
									}else{
										$to_current_qty = $sum;
									}
									
									$stock[] = array('transacton_type' => 'IN', 'product_id' => $from_store_row->product_id, 'current_quantity' => $to_current_qty, 'store_id' => $inv->from_store_id, 'type' => 'transfer', 'quantity' => $to_qty, 'purchase_invoice_id' => $from_store_row->purchase_invoice_id, 'purchase_batch_no' => $from_store_row->purchase_batch_no, 'created_on' => date('Y-m-d H:i:s'), 'created_by' =>  $this->session->userdata('user_id'));
									
									if($from_current_qty > 0){
										$from_current_qty = $from_current_qty - $from_qty;
									}else{
										$from_current_qty = 0;
									}
									
									$stock[] = array('transacton_type' => 'OUT', 'product_id' => $from_store_row->product_id,  'current_quantity' => $from_current_qty, 'store_id' => $inv->to_store_id, 'type' => 'transfer', 'quantity' => $from_qty, 'purchase_invoice_id' => $from_store_row->purchase_invoice_id, 'purchase_batch_no' => $from_store_row->purchase_batch_no, 'created_on' => date('Y-m-d H:i:s'), 'created_by' =>  $this->session->userdata('user_id'));
									
									$stockupdate[$from_store_row->id] =  array('status' => 1);
									
									$transfer_quantity[$product_details->id] = abs($transfer_quantity[$product_details->id]);
								}else{
																	
									$from_qty = $from_store_row->quantity - $transfer_quantity[$product_details->id];
									$to_qty = $from_store_row->quantity - $transfer_quantity[$product_details->id];
									$sum+= $to_qty;
									if($to_current_qty > 0){
										$to_current_qty = $to_current_qty + $to_qty;
									}else{
										$to_current_qty = $sum;
									}
									
									$stock[] = array('transacton_type' => 'IN', 'product_id' => $from_store_row->product_id, 'current_quantity' => $to_current_qty, 'store_id' => $inv->from_store_id, 'type' => 'transfer', 'quantity' => $to_qty, 'purchase_invoice_id' => $from_store_row->purchase_invoice_id, 'purchase_batch_no' => $from_store_row->purchase_batch_no, 'created_on' => date('Y-m-d H:i:s'), 'created_by' =>  $this->session->userdata('user_id'));
									
									if($from_current_qty > 0){
										$from_current_qty = $from_current_qty - $from_qty;
									}else{
										$from_current_qty = 0;
									}
									
									$stock[] = array('transacton_type' => 'OUT', 'product_id' => $from_store_row->product_id, 'current_quantity' => $from_current_qty, 'store_id' => $inv->to_store_id, 'type' => 'transfer', 'quantity' => $from_qty, 'purchase_invoice_id' => $from_store_row->purchase_invoice_id, 'purchase_batch_no' => $from_store_row->purchase_batch_no, 'created_on' => date('Y-m-d H:i:s'), 'created_by' =>  $this->session->userdata('user_id'));
									
									$stockupdate[$from_store_row->id] =  array('status' => 1);
									
									break; return;	
								}
								
							}
							
						}
					}elseif($inv->request_type == 'return'){
						$from_store = $this->store_receivers_model->getStoreMasterProductID($product_details->id, $inv->from_store_id);
						$to_current_qty = $this->store_receivers_model->getCurrentQuantityID($product_details->id, $inv->to_store_id);
						echo $from_current_qty = $this->store_receivers_model->getCurrentQuantityID($product_details->id, $inv->from_store_id);
						
						$transfer_quantity[$product_details->id] = $item_transfer_quantity;
						
						$sum = 0;
						foreach($from_store as $from_store_row){
							
							
							if($transfer_quantity[$product_details->id] > 0 ){
								 $transfer_quantity[$product_details->id];
								 $from_store_row->quantity;
								$transfer_quantity[$product_details->id] = $from_store_row->quantity - $transfer_quantity[$product_details->id];
								
								if($transfer_quantity[$product_details->id] < 0 ){
									$from_qty = $from_store_row->quantity;
									$to_qty = $from_store_row->quantity;
									$sum+= $to_qty;
									if($to_current_qty > 0){
										$to_current_qty = $to_current_qty + $to_qty;
									}else{
										$to_current_qty = $sum;
									}
									
									$stock[] = array('transacton_type' => 'IN', 'product_id' => $from_store_row->product_id, 'current_quantity' => $to_current_qty, 'store_id' => $inv->to_store_id, 'type' => 'transfer', 'quantity' => $to_qty, 'purchase_invoice_id' => $from_store_row->purchase_invoice_id, 'purchase_batch_no' => $from_store_row->purchase_batch_no, 'created_on' => date('Y-m-d H:i:s'), 'created_by' =>  $this->session->userdata('user_id'));
									
									if($from_current_qty > 0){
										$from_current_qty = $from_current_qty - $from_qty;
									}else{
										$from_current_qty = 0;
									}
									
									$stock[] = array('transacton_type' => 'OUT', 'product_id' => $from_store_row->product_id,  'current_quantity' => $from_current_qty, 'store_id' => $inv->from_store_id, 'type' => 'transfer', 'quantity' => $from_qty, 'purchase_invoice_id' => $from_store_row->purchase_invoice_id, 'purchase_batch_no' => $from_store_row->purchase_batch_no, 'created_on' => date('Y-m-d H:i:s'), 'created_by' =>  $this->session->userdata('user_id'));
									
									$stockupdate[$from_store_row->id] =  array('status' => 1);
									
									$transfer_quantity[$product_details->id] = abs($transfer_quantity[$product_details->id]);
								}else{
																	
									$from_qty = $from_store_row->quantity - $transfer_quantity[$product_details->id];
									$to_qty = $from_store_row->quantity - $transfer_quantity[$product_details->id];
									$sum+= $to_qty;
									if($to_current_qty > 0){
										$to_current_qty = $to_current_qty + $to_qty;
									}else{
										$to_current_qty = $sum;
									}
									
									$stock[] = array('transacton_type' => 'IN', 'product_id' => $from_store_row->product_id, 'current_quantity' => $to_current_qty, 'store_id' => $inv->to_store_id, 'type' => 'transfer', 'quantity' => $to_qty, 'purchase_invoice_id' => $from_store_row->purchase_invoice_id, 'purchase_batch_no' => $from_store_row->purchase_batch_no, 'created_on' => date('Y-m-d H:i:s'), 'created_by' =>  $this->session->userdata('user_id'));
									
									if($from_current_qty > 0){
										$from_current_qty = $from_current_qty - $from_qty;
									}else{
										$from_current_qty = 0;
									}
									
									$stock[] = array('transacton_type' => 'OUT', 'product_id' => $from_store_row->product_id, 'current_quantity' => $from_current_qty, 'store_id' => $inv->from_store_id, 'type' => 'transfer', 'quantity' => $from_qty, 'purchase_invoice_id' => $from_store_row->purchase_invoice_id, 'purchase_batch_no' => $from_store_row->purchase_batch_no, 'created_on' => date('Y-m-d H:i:s'), 'created_by' =>  $this->session->userdata('user_id'));
									
									$stockupdate[$from_store_row->id] =  array('status' => 1);
									
									break; return;	
								}
								
							}
							
						}
					}
                    // $unit_cost = $real_unit_cost;
                    $pr_discount = $this->siteprocurment->calculateDiscount($item_discount, $unit_cost);
                    $unit_cost = $this->sma->formatDecimal($unit_cost - $pr_discount);
                    $item_net_cost = $unit_cost;
                    $pr_item_discount = $this->sma->formatDecimal($pr_discount * $item_unit_quantity);
                    $product_discount += $pr_item_discount;
                    $pr_item_tax = 0;
                    $item_tax = 0;
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
                    $subtotal = (($item_net_cost * $item_unit_quantity) + $pr_item_tax);
                    $unit = $this->siteprocurment->getUnitByID($item_unit);
                    /*update common_store_receiver_items*/
                     $item = array(
                        'product_id' => $product_details->id,
                        'product_code' => $item_code,
                        'unit_price' => $unit_cost_new,
                        'product_name' => $product_details->name,
                        'option_id' => null,
                        'net_unit_price' => $item_net_cost,
                        'real_unit_price' => $unit_cost_new,
                        'quantity' => $item_quantity,
						'available_quantity' => $item_available_quantity,
						'transfer_quantity' => $item_transfer_quantity,
						'pending_quantity' => $item_pending_quantity,
                        'product_unit_id' => $item_unit,
                        'product_unit_code' => $unit->code,
                        'unit_quantity' => $item_unit_quantity,                       
                        'warehouse_id' => $warehouse_id,
                        'item_tax' => $pr_item_tax,                        
                        'tax' => $tax,
						'tax_rate_id' => $item_tax_rate,
                        'discount' => $item_discount,
                        'item_discount' => $pr_item_discount,
                        'subtotal' => $this->sma->formatDecimal($subtotal),                          
                    );
                    $items[] = $item;
                    $total += $item_net_cost * $item_unit_quantity;
                }
            }


            if (empty($items)) {
                $this->form_validation->set_rules('product', lang("order_items"), 'required');
            } else {
                
                krsort($items);
            }

            $order_discount = $this->siteprocurment->calculateDiscount($this->input->post('discount'), ($total + $product_tax));
            $total_discount = $this->sma->formatDecimal(($order_discount + $product_discount), 4);
            $order_tax = $this->siteprocurment->calculateOrderTax($this->input->post('order_tax'), ($total + $product_tax - $total_discount));
            $total_tax = $this->sma->formatDecimal(($product_tax + $order_tax), 4);
            $grand_total = $this->sma->formatDecimal(($total + $total_tax + $this->sma->formatDecimal($shipping) - $order_discount), 4);
            /*update common_store_receivers*/
            $data = array(
				
                'warehouse_id' => $warehouse_id,
				//'invoice_no' => $this->input->post('invoice_no'),
				
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
                'status' => $status ? $status : '',
                'updated_by' => $this->session->userdata('user_id'),
				
                 //'payment_term' => $payment_term ? $payment_term : 0 ,
                // 'due_date' => $due_date ? $due_date : '',
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
				/*echo '<pre>';
			print_r($stock);
			print_r($stockupdate);
			die;*/
             //$this->sma->print_arrays($data, $items);die;
        }

        if ($this->form_validation->run() == true && $this->store_receivers_model->updateStore_receivers($id, $data, $items, $stock, $stockupdate)) {             
            $this->session->set_userdata('remove_pols', 1);
            $this->session->set_flashdata('message', $this->lang->line("pro_store_receivers_added"));
            admin_redirect('procurment/store_receivers');
        } else {
            
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['inv'] = $inv;
            /*if ($this->Settings->disable_editing) {
                if ($this->data['inv']->date <= date('Y-m-d', strtotime('-'.$this->Settings->disable_editing.' days'))) {
                    $this->session->set_flashdata('error', sprintf(lang("pro_store_receivers_x_edited_older_than_x_days"), $this->Settings->disable_editing));
                    redirect($_SERVER["HTTP_REFERER"]);
                }
            }*/
            $inv_items = $this->store_receivers_model->getAllStore_receiversItems($id);   

             krsort($inv_items);
            $c = rand(100000, 9999999);
            foreach ($inv_items as $item) {
                          
                $row = $this->siteprocurment->getProductByID($item->product_id);                   
                $row->base_quantity = $item->quantity;
                $row->base_unit = $row->unit ? $row->unit : $item->product_unit_id;
                $row->base_unit_cost = $row->cost ? $row->cost : $item->unit_price;
                $row->unit = $item->product_unit_id;
                $row->qty = $item->unit_quantity;
                $row->oqty = $item->quantity;
				
				$current_quantity = $this->store_receivers_model->getAvailableQTY($item->product_id, $inv->to_store_id);
				
				$pending_quantity = $this->store_receivers_model->checkPendingQTYEdit($item->product_id, $item->quantity, $inv->id);
			
				$row->current_quantity = $current_quantity;
				$row->pending_quantity = $pending_quantity;
				
				$row->transfer_quantity = $item->transfer_quantity;
				
			
                // $row->supplier_part_no = $item->supplier_part_no;
                // $row->received = $item->quantity_received ? $item->quantity_received : $item->quantity;
                // $row->quantity_balance = $item->quantity_balance + ($item->quantity-$row->received);
                $row->discount = $item->discount ? $item->discount : '0';
                $options = $this->store_receivers_model->getProductOptions($row->id);

                $row->option = $item->option_id;
                $row->real_unit_cost = $item->real_unit_price;
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

            $this->data['inv_items'] = json_encode($pr);
            $this->data['id'] = $id;
            $this->data['suppliers'] = $this->siteprocurment->getAllCompanies('supplier');
            $this->data['store_receivers'] = $this->store_receivers_model->getStore_receiversByID($id);
            $this->data['categories'] = $this->siteprocurment->getAllCategories();
            $this->data['tax_rates'] = $this->siteprocurment->getAllTaxRates();
            $this->data['warehouses'] = $this->siteprocurment->getAllWarehouses();
			$this->data['ref_requestnumber'] = $_GET['ref'];
			 $this->data['requestnumber'] = $this->siteprocurment->getAllSTORETRANSNUMBERedit();
			 $this->data['stores'] = $this->siteprocurment->getAllStores();
            $this->load->helper('string');
            $value = random_string('alnum', 20);
            $this->session->set_userdata('user_csrf', $value);
            $this->session->set_userdata('remove_pols', 1);
            $this->data['csrf'] = $this->session->userdata('user_csrf');
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('procurment/store_receivers'), 'page' => lang('store_receivers')), array('link' => '#', 'page' => lang('edit_store_receivers')));
            $meta = array('page_title' => lang('edit_store_receivers'), 'bc' => $bc);
          /*  echo "<pre>";
            print_r($this->data);exit;
            echo "</pre>";*/
            $this->page_construct('procurment/store_receivers/edit', $meta, $this->data);
        }
    }

    /* ----------------------------------------------------------------------------------------------------------- */

    public function store_receivers_by_csv()
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
                    admin_redirect("procurment/store_receivers/store_receivers_by_csv");
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

                        if ($product_details = $this->store_receivers_model->getProductByCode($csv_pr['code'])) {

                            if ($csv_pr['variant']) {
                                $item_option = $this->store_receivers_model->getProductVariantByName($csv_pr['variant'], $product_details->id);
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
                            $tax_details = ((isset($item_tax_rate) && !empty($item_tax_rate)) ? $this->store_receivers_model->getTaxRateByName($item_tax_rate) : $this->siteprocurment->getTaxRateByID($product_details->tax_rate));
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

        if ($this->form_validation->run() == true && $this->store_receivers_model->addPurchase($data, $products)) {

            $this->session->set_flashdata('message', $this->lang->line("pro_store_receivers_added"));
            admin_redirect("procurment/store_receivers");
        } else {

            $data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['warehouses'] = $this->siteprocurment->getAllWarehouses();
            $this->data['tax_rates'] = $this->siteprocurment->getAllTaxRates();
            $this->data['ponumber'] = ''; // $this->siteprocurment->getReference('po');

            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('procurment/store_receivers'), 'page' => lang('store_receivers')), array('link' => '#', 'page' => lang('add_store_receivers_by_csv')));
            $meta = array('page_title' => lang('add_store_receivers_by_csv'), 'bc' => $bc);
            $this->page_construct('procurment/store_receivers/store_receivers__orderby_csv', $meta, $this->data);

        }
    }

    /* --------------------------------------------------------------------------- */

   public function delete($id = null)
    {
        //$this->sma->checkPermissions(null, true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        if ($this->store_receivers_model->deleteStore_receivers($id)) {

            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(array('error' => 0, 'msg' => lang("pro_store_receivers_deleted")));
            }
            $this->session->set_flashdata('message', lang('store_receivers_deleted'));
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

        $rows = $this->store_receivers_model->getProductNames($sr);
		
        if ($rows) {
            $c = str_replace(".", "", microtime(true));
            $r = 0;
            foreach ($rows as $row) {
                $option = false;
                $row->item_tax_method = $row->tax_method;
                $options = $this->store_receivers_model->getProductOptions($row->id);
                if ($options) {
                    $opt = $option_id && $r == 0 ? $this->store_receivers_model->getProductOptionByID($option_id) : current($options);
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
                $row->real_unit_cost = $row->cost;
                $row->base_quantity = 1;
                $row->base_unit = $row->unit;
                $row->base_unit_cost = $row->cost;
                $row->unit = $row->purchase_unit ? $row->purchase_unit : $row->unit;
                $row->new_entry = 1;
                $row->expiry = '';
                $row->qty = 1;
                $row->quantity_balance = '';
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

    public function store_receivers_actions()
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
                        $this->store_receivers_model->deleteStore_receivers($id);
                    }
                    $this->session->set_flashdata('message', $this->lang->line("store_receivers_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);

                } elseif ($this->input->post('form_action') == 'combine') {

                    $html = $this->combine_pdf($_POST['val']);

                } elseif ($this->input->post('form_action') == 'export_excel') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('store_receivers'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference_no'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('supplier'));
                    $this->excel->getActiveSheet()->SetCellValue('F1', lang('status'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('grand_total'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $store_receivers = $this->store_receivers_model->getStore_receiversByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($store_receivers->date));
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $store_receivers->reference_no);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $store_receivers->supplier);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $store_receivers->status);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $this->sma->formatMoney($store_receivers->grand_total));
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'store_receivers_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', $this->lang->line("no_store_receivers_selected"));
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

        $this->data['payments'] = $this->store_receivers_model->getPurchasePayments($id);
        $this->data['inv'] = $this->store_receivers_model->getStore_receiversByID($id);
        $this->load->view($this->theme . 'store_receivers/payments', $this->data);
    }

    public function payment_note($id = null)
    {
        //$this->sma->checkPermissions('payments', true);
        $payment = $this->store_receivers_model->getPaymentByID($id);
        $inv = $this->store_receivers_model->getStore_receiversByID($payment->purchase_id);
        $this->data['supplier'] = $this->siteprocurment->getCompanyByID($inv->supplier_id);
        $this->data['warehouse'] = $this->siteprocurment->getWarehouseByID($inv->warehouse_id);
        $this->data['inv'] = $inv;
        $this->data['payment'] = $payment;
        $this->data['page_title'] = $this->lang->line("payment_note");

        $this->load->view($this->theme . 'store_receivers/payment_note', $this->data);
    }

    public function email_payment($id = null)
    {
        //$this->sma->checkPermissions('payments', true);
        $payment = $this->store_receivers_model->getPaymentByID($id);
        $inv = $this->store_receivers_model->getStore_receiversByID($payment->purchase_id);
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
        $html = $this->load->view($this->theme . 'store_receivers/payment_note', $this->data, TRUE);

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
        $purchase = $this->store_receivers_model->getStore_receiversByID($id);
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

        if ($this->form_validation->run() == true && $this->store_receivers_model->addPayment($payment)) {
            $this->session->set_flashdata('message', lang("payment_added"));
            redirect($_SERVER["HTTP_REFERER"]);
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['inv'] = $purchase;
            $this->data['payment_ref'] = ''; //$this->siteprocurment->getReference('ppay');
            $this->data['modal_js'] = $this->siteprocurment->modal_js();

            $this->load->view($this->theme . 'store_receivers/add_payment', $this->data);
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

        if ($this->form_validation->run() == true && $this->store_receivers_model->updatePayment($id, $payment)) {
            $this->session->set_flashdata('message', lang("payment_updated"));
            admin_redirect("procurment/store_receivers");
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['payment'] = $this->store_receivers_model->getPaymentByID($id);
            $this->data['modal_js'] = $this->siteprocurment->modal_js();

            $this->load->view($this->theme . 'store_receivers/edit_payment', $this->data);
        }
    }

    public function delete_payment($id = null)
    {
        //$this->sma->checkPermissions('delete', true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->store_receivers_model->deletePayment($id)) {
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
        $this->page_construct('procurment/store_receivers/expenses', $meta, $this->data);
    }

    public function getExpenses()
    {
        //$this->sma->checkPermissions('expenses');

        $detail_link = anchor('admin/procurment/store_receivers/expense_note/$1', '<i class="fa fa-file-text-o"></i> ' . lang('expense_note'), 'data-toggle="modal" data-target="#myModal2"');
        $edit_link = anchor('admin/procurment/store_receivers/edit_expense/$1', '<i class="fa fa-edit"></i> ' . lang('edit_expense'), 'data-toggle="modal" data-target="#myModal"');
        //$attachment_link = '<a href="'.base_url('assets/uploads/$1').'" target="_blank"><i class="fa fa-chain"></i></a>';
        $delete_link = "<a href='#' class='po' title='<b>" . $this->lang->line("delete_expense") . "</b>' data-content=\"<p>"
        . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('procurment/store_receivers/delete_expense/$1') . "'>"
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
        $expense = $this->store_receivers_model->getExpenseByID($id);
        $this->data['user'] = $this->siteprocurment->getUser($expense->created_by);
        $this->data['category'] = $expense->category_id ? $this->store_receivers_model->getExpenseCategoryByID($expense->category_id) : NULL;
        $this->data['warehouse'] = $expense->warehouse_id ? $this->siteprocurment->getWarehouseByID($expense->warehouse_id) : NULL;
        $this->data['expense'] = $expense;
        $this->data['page_title'] = $this->lang->line("expense_note");
        $this->load->view($this->theme . 'store_receivers/expense_note', $this->data);
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

        if ($this->form_validation->run() == true && $this->store_receivers_model->addExpense($data)) {
            $this->session->set_flashdata('message', lang("expense_added"));
            redirect($_SERVER["HTTP_REFERER"]);
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['exnumber'] = ''; //$this->siteprocurment->getReference('ex');
            $this->data['warehouses'] = $this->siteprocurment->getAllWarehouses();
            $this->data['categories'] = $this->store_receivers_model->getExpenseCategories();
            $this->data['modal_js'] = $this->siteprocurment->modal_js();
            $this->load->view($this->theme . 'store_receivers/add_expense', $this->data);
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

        if ($this->form_validation->run() == true && $this->store_receivers_model->updateExpense($id, $data)) {
            $this->session->set_flashdata('message', lang("expense_updated"));
            admin_redirect("procurment/store_receivers/expenses");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['expense'] = $this->store_receivers_model->getExpenseByID($id);
            $this->data['warehouses'] = $this->siteprocurment->getAllWarehouses();
            $this->data['modal_js'] = $this->siteprocurment->modal_js();
            $this->data['categories'] = $this->store_receivers_model->getExpenseCategories();
            $this->load->view($this->theme . 'store_receivers/edit_expense', $this->data);
        }
    }

    public function delete_expense($id = null)
    {
        //$this->sma->checkPermissions('delete', true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $expense = $this->store_receivers_model->getExpenseByID($id);
        if ($this->store_receivers_model->deleteExpense($id)) {
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
                        $this->store_receivers_model->deleteExpense($id);
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
                        $expense = $this->store_receivers_model->getExpenseByID($id);
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
        //$this->sma->checkPermissions('return_store_receivers');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv = $this->store_receivers_model->getReturnByID($id);
        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($inv->created_by);
        }
        $this->data['barcode'] = "<img src='" . admin_url('procurment/products/gen_barcode/' . $inv->reference_no) . "' alt='" . $inv->reference_no . "' class='pull-left' />";
        $this->data['supplier'] = $this->siteprocurment->getCompanyByID($inv->supplier_id);
        $this->data['payments'] = $this->store_receivers_model->getPaymentsForPurchase($id);
        $this->data['user'] = $this->siteprocurment->getUser($inv->created_by);
        $this->data['warehouse'] = $this->siteprocurment->getWarehouseByID($inv->warehouse_id);
        $this->data['inv'] = $inv;
        $this->data['rows'] = $this->store_receivers_model->getAllReturnItems($id);
        $this->data['purchase'] = $this->store_receivers_model->getStore_receiversByID($inv->purchase_id);
        $this->load->view($this->theme.'store_receivers/view_return', $this->data);
    }

    public function return_purchase($id = null)
    {
        //$this->sma->checkPermissions('return_store_receivers');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $purchase = $this->store_receivers_model->getStore_receiversByID($id);
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
                    $product_details = $this->store_receivers_model->getProductByCode($item_code);

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

        if ($this->form_validation->run() == true && $this->store_receivers_model->addPurchase($data, $products)) {
            $this->session->set_flashdata('message', lang("return_purchase_added"));
            admin_redirect("procurment/store_receivers");
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
            $inv_items = $this->store_receivers_model->getAllPurchaseItems($id);
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
                $options = $this->store_receivers_model->getProductOptions($row->id);
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
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('procurment/store_receivers'), 'page' => lang('store_receivers')), array('link' => '#', 'page' => lang('return_purchase')));
            $meta = array('page_title' => lang('return_purchase'), 'bc' => $bc);
            $this->page_construct('procurment/store_receivers/return_purchase', $meta, $this->data);
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

        if ($this->form_validation->run() == true && $this->store_receivers_model->updateStatus($id, $status, $note)) {
            $this->session->set_flashdata('message', lang('status_updated'));
            admin_redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'sales');
        } else {

            $this->data['inv'] = $this->store_receivers_model->getStore_receiversByID($id);
            $this->data['returned'] = FALSE;
            if ($this->data['inv']->status == 'returned' || $this->data['inv']->return_id) {
                $this->data['returned'] = TRUE;
            }
            $this->data['modal_js'] = $this->siteprocurment->modal_js();
            $this->load->view($this->theme.'store_receivers/update_status', $this->data);

        }
    }

}
