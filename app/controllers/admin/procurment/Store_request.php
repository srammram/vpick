<?php defined('BASEPATH') or exit('No direct script access allowed');

class Store_request extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        if ($this->Supplier) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $this->lang->admin_load('procurment/store_request', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('procurment/store_request_model');
        $this->digital_upload_path = 'assets/uploads/procurment/store_request/';
        $this->digital_file_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
        $this->allowed_file_size = '1024';
	if (!file_exists($this->digital_upload_path)) {
		mkdir($this->digital_upload_path, 0777, true);
	}
        $this->data['logo'] = true;
		
		$this->Muser_id = $this->session->userdata('user_id');
		$this->Maccess_id = 8;

    }

    public function index($warehouse_id = null)
    {
      //  //$this->sma->checkPermissions();

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

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('store_indent_request')));
        $meta = array('page_title' => lang('store_indent_request'), 'bc' => $bc);
        $this->page_construct('procurment/store_request/index', $meta, $this->data);

    }

    public function getStore_request($warehouse_id = null)
    {
        //$this->sma->checkPermissions('index');

        if ((!$this->Owner || !$this->Admin) && !$warehouse_id) {
            $user = $this->siteprocurment->getUser();
            $warehouse_id = $user->warehouse_id;
        }
	$view_link = '<a href="'.admin_url('procurment/store_request/view/$1').'" data-toggle="modal" data-target="#myModal"><i class="fa fa-edit"></i>'.lang('view_store_request').'</a>';
       
        $detail_link = anchor('admin/procurment/store_request/view/$1/', '<i class="fa fa-file-text-o"></i> ' . lang('store_request_details'));
        $email_link = anchor('admin/procurment/store_request/email/$1', '<i class="fa fa-envelope"></i> ' . lang('email_store_request'), 'data-toggle="modal" data-target="#myModal"');
		
		
		$edit_link = anchor('admin/procurment/store_request/edit/$1', '<i class="fa fa-edit"></i> ' . lang('edit_store_request'));
		
       // $convert_link = anchor('admin/procurment/sales/add/$1', '<i class="fa fa-heart"></i> ' . lang('create_sale'));
       // $pc_link = anchor('admin/procurment/purchases/add/$1', '<i class="fa fa-star"></i> ' . lang('create_purchase'));
        $pdf_link = anchor('admin/procurment/store_request/pdf/$1', '<i class="fa fa-file-pdf-o"></i> ' . lang('download_pdf'));
        $delete_link = "<a href='#' class='po' title='<b>" . $this->lang->line("delete_store_request") . "</b>' data-content=\"<p>"
        . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('procurment/store_request/delete/$1') . "'>"
        . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
        . lang('delete_store_request') . "</a>";
		
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

        $this->load->library('datatables');
        if ($warehouse_id) {
            $this->datatables
                ->select("pro_store_request.id, pro_store_request.date, pro_store_request.reference_no,   pro_store_request.status, pro_store_request.attachment")
				
                ->from('pro_store_request')
                ->where('pro_store_request.warehouse_id', $warehouse_id);
        } else {
            $this->datatables
                ->select("pro_store_request.id, pro_store_request.date, pro_store_request.reference_no, f.name as from_name, t.name as to_name, pro_store_request.status, pro_store_request.attachment as attachment")
                ->from('pro_store_request')
				->join('warehouses f', 'f.id = pro_store_request.from_store_id', 'left')
				->join('warehouses t', 't.id = pro_store_request.to_store_id', 'left');
        }
        /*if (!$this->Customer && !$this->Supplier && !$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $this->datatables->where('pro_store_request.created_by', $this->session->userdata('user_id'));
        } elseif ($this->Customer) {
            $this->datatables->where('pro_store_request.customer_id', $this->session->userdata('user_id'));
        }*/
	$this->datatables->edit_column('attachment', '$1__$2', $this->digital_upload_path.', attachment');
        $this->datatables->add_column("Actions", $action, "pro_store_request.id,pro_store_request.status");
        echo $this->datatables->generate();
    }

    public function modal_view($store_request_id = null)
    {
        //$this->sma->checkPermissions('index', true);

        if ($this->input->get('id')) {
            $store_request_id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv = $this->store_request_model->getStore_requestByID($store_request_id);
        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($inv->created_by, true);
        }
        $this->data['rows'] = $this->store_request_model->getAllStore_requestItems($store_request_id);
        $this->data['customer'] = $this->siteprocurment->getCompanyByID($inv->customer_id);
        $this->data['biller'] = $this->siteprocurment->getCompanyByID($inv->biller_id);
        $this->data['created_by'] = $this->siteprocurment->getUser($inv->created_by);
        $this->data['updated_by'] = $inv->updated_by ? $this->siteprocurment->getUser($inv->updated_by) : null;
        $this->data['warehouse'] = $this->siteprocurment->getWarehouseByID($inv->warehouse_id);
        $this->data['inv'] = $inv;

        $this->load->view($this->theme . 'store_request/modal_view', $this->data);

    }

    public function view($store_request_id = null)
    {
	$id = $store_request_id;
	$this->data['store_req'] = $this->store_request_model->getStore_requestByID($id);
            $inv_items = $this->store_request_model->getAllStore_requestItems($id);
            /*echo "<pre>";
             print_r($inv_items);die;*/
             krsort($inv_items);
            $c = rand(100000, 9999999);
            foreach ($inv_items as $item) {
                $row = $this->siteprocurment->getRecipeByID($item->product_id);
                if (!$row) {
                    $row = json_decode('{}');
                    $row->tax_method = 0;
                } else {
                    unset($row->details, $row->cost, $row->supplier1price, $row->supplier2price, $row->supplier3price, $row->supplier4price, $row->supplier5price);
                }
                $row->quantity = 0;
                $pis = $this->siteprocurment->getPurchasedItems($item->product_id, $item->warehouse_id, $item->option_id);
                if ($pis) {
                    foreach ($pis as $pi) {
                        $row->quantity += $pi->quantity_balance;
                    }
                }
                $row->id = $item->product_id;
                $row->code = $item->product_code;
                $row->name = $item->product_name;
                $row->type = $item->product_type;
                $row->base_quantity = $item->quantity;
                $row->base_unit = $row->unit ? $row->unit : $item->product_unit_id;
                $row->base_unit_price = $row->price ? $row->price : $item->unit_price;
                $row->unit = $item->product_unit_id;
                $row->qty = $item->quantity;
                $row->discount = $item->discount ? $item->discount : '0';
                $row->price = $this->sma->formatDecimal($item->net_unit_price + $this->sma->formatDecimal($item->item_discount / $item->quantity));
                $row->unit_price = $row->tax_method ? $item->unit_price + $this->sma->formatDecimal($item->item_discount / $item->quantity) + $this->sma->formatDecimal($item->item_tax / $item->quantity) : $item->unit_price + ($item->item_discount / $item->quantity);
                $row->real_unit_price = $item->real_unit_price;
                $row->tax_rate = $item->tax_rate_id;
                $row->option = $item->option_id;
                $options = $this->store_request_model->getProductOptions($row->id, $item->warehouse_id);

                if ($options) {
                    $option_quantity = 0;
                    foreach ($options as $option) {
                        $pis = $this->siteprocurment->getPurchasedItems($row->id, $item->warehouse_id, $item->option_id);
                        if ($pis) {
                            foreach ($pis as $pi) {
                                $option_quantity += $pi->quantity_balance;
                            }
                        }
                        if ($option->quantity > $option_quantity) {
                            $option->quantity = $option_quantity;
                        }
                    }
                }

                $combo_items = false;
                if ($row->type == 'combo') {
                    $combo_items = $this->store_request_model->getProductComboItems($row->id, $item->warehouse_id);
                    foreach ($combo_items as $combo_item) {
                        $combo_item->quantity = $combo_item->qty * $item->quantity;
                    }
                }
                $units = $this->siteprocurment->getUnitsByBUID($row->base_unit);
                $tax_rate = $this->siteprocurment->getTaxRateByID($row->tax_rate);
                $ri = $this->Settings->item_addition ? $row->id : $c;

                $pr[$ri] = array('id' => $c, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $item, 'combo_items' => $combo_items, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options);
                $c++;
            }
            $this->data['store_req_items'] = $pr;
            $this->data['id'] = $id;
	    $this->data['stores'] = $this->siteprocurment->getAllStores();
	    $this->data['all_stores'] = $this->siteprocurment->getAllWarehouses();
            //$this->data['currencies'] = $this->siteprocurment->getAllCurrencies();
            $this->data['billers'] = ($this->Owner || $this->Admin || !$this->session->userdata('biller_id')) ? $this->siteprocurment->getAllCompanies('biller') : null;
            $this->data['tax_rates'] = $this->siteprocurment->getAllTaxRates();
            $this->data['warehouses'] = ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) ? $this->siteprocurment->getAllWarehouses() : null;

            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('procurment/store_request'), 'page' => lang('store_request')), array('link' => '#', 'page' => lang('view_store_request')));
            $meta = array('page_title' => lang('view_store_request'), 'bc' => $bc);
        $this->load->view($this->theme . 'procurment/store_request/view',  $this->data);

    }

    public function pdf($store_request_id = null, $view = null, $save_bufffer = null)
    {
        //$this->sma->checkPermissions();

        if ($this->input->get('id')) {
            $store_request_id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv = $this->store_request_model->getStore_requestByID($store_request_id);
        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($inv->created_by);
        }
        $this->data['rows'] = $this->store_request_model->getAllStore_requestItems($store_request_id);
        $this->data['customer'] = $this->siteprocurment->getCompanyByID($inv->customer_id);
        $this->data['biller'] = $this->siteprocurment->getCompanyByID($inv->biller_id);
        $this->data['created_by'] = $this->siteprocurment->getUser($inv->created_by);
        $this->data['warehouse'] = $this->siteprocurment->getWarehouseByID($inv->warehouse_id);
        $this->data['inv'] = $inv;
        $name = $this->lang->line("store_request") . "_" . str_replace('/', '_', $inv->reference_no) . ".pdf";
        $html = $this->load->view($this->theme . 'store_request/pdf', $this->data, true);
        if (! $this->Settings->barcode_img) {
            $html = preg_replace("'\<\?xml(.*)\?\>'", '', $html);
        }
        if ($view) {
            $this->load->view($this->theme . 'store_request/pdf', $this->data);
        } elseif ($save_bufffer) {
            return $this->sma->generate_pdf($html, $name, $save_bufffer);
        } else {
            $this->sma->generate_pdf($html, $name);
        }
    }

    public function combine_pdf($store_request_id)
    {
        //$this->sma->checkPermissions('pdf');

        foreach ($store_request_id as $store_request_id) {

            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
            $inv = $this->store_request_model->getStore_requestByID($store_request_id);
            if (!$this->session->userdata('view_right')) {
                $this->sma->view_rights($inv->created_by);
            }
            $this->data['rows'] = $this->store_request_model->getAllStore_requestItems($store_request_id);
            $this->data['customer'] = $this->siteprocurment->getCompanyByID($inv->customer_id);
            $this->data['biller'] = $this->siteprocurment->getCompanyByID($inv->biller_id);
            $this->data['user'] = $this->siteprocurment->getUser($inv->created_by);
            $this->data['warehouse'] = $this->siteprocurment->getWarehouseByID($inv->warehouse_id);
            $this->data['inv'] = $inv;

            $html[] = array(
                'content' => $this->load->view($this->theme . 'store_request/pdf', $this->data, true),
                'footer' => '',
            );
        }

        $name = lang("store_request") . ".pdf";
        $this->sma->generate_pdf($html, $name);

    }

    public function email($store_request_id = null)
    {
        //$this->sma->checkPermissions(false, true);

        if ($this->input->get('id')) {
            $store_request_id = $this->input->get('id');
        }
        $inv = $this->store_request_model->getStore_requestByID($store_request_id);
        $this->form_validation->set_rules('to', $this->lang->line("to") . " " . $this->lang->line("email"), 'trim|required|valid_email');
        $this->form_validation->set_rules('subject', $this->lang->line("subject"), 'trim|required');
        $this->form_validation->set_rules('cc', $this->lang->line("cc"), 'trim|valid_emails');
        $this->form_validation->set_rules('bcc', $this->lang->line("bcc"), 'trim|valid_emails');
        $this->form_validation->set_rules('note', $this->lang->line("message"), 'trim');

        if ($this->form_validation->run() == true) {
            if (!$this->session->userdata('view_right')) {
                $this->sma->view_rights($inv->created_by);
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
            $customer = $this->siteprocurment->getCompanyByID($inv->customer_id);
            $biller = $this->siteprocurment->getCompanyByID($inv->biller_id);
            $this->load->library('parser');
            $parse_data = array(
                'reference_number' => $inv->reference_no,
                'contact_person' => $customer->name,
                'company' => $customer->company,
                'site_link' => base_url(),
                'site_name' => $this->Settings->site_name,
                'logo' => '<img src="' . base_url() . 'assets/uploads/logos/' . $biller->logo . '" alt="' . ($biller->company != '-' ? $biller->company : $biller->name) . '"/>',
            );
            $msg = $this->input->post('note');
            $message = $this->parser->parse_string($msg, $parse_data);
            $attachment = $this->pdf($store_request_id, null, 'S');

            try {
                if ($this->sma->send_email($to, $subject, $message, null, null, $attachment, $cc, $bcc)) {
                    delete_files($attachment);
                    $this->db->update('store_request', array('status' => 'approved'), array('id' => $store_request_id));
                    $this->session->set_flashdata('message', $this->lang->line("email_sent"));
                    admin_redirect("procurment/store_request");
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

            if (file_exists('./themes/' . $this->Settings->theme . '/admin/views/email_templates/store_request.html')) {
                $store_request_temp = file_get_contents('themes/' . $this->Settings->theme . '/admin/views/email_templates/store_request.html');
            } else {
                $store_request_temp = file_get_contents('./themes/default/admin/views/email_templates/store_request.html');
            }

            $this->data['subject'] = array('name' => 'subject',
                'id' => 'subject',
                'type' => 'text',
                'value' => $this->form_validation->set_value('subject', lang('store_request').' (' . $inv->reference_no . ') '.lang('from').' '.$this->Settings->site_name),
            );
            $this->data['note'] = array('name' => 'note',
                'id' => 'note',
                'type' => 'text',
                'value' => $this->form_validation->set_value('note', $store_request_temp),
            );
            $this->data['customer'] = $this->siteprocurment->getCompanyByID($inv->customer_id);

            $this->data['id'] = $store_request_id;
            $this->data['modal_js'] = $this->siteprocurment->modal_js();
            $this->load->view($this->theme . 'store_request/email', $this->data);

        }
    }

    public function add()
    {
        ////$this->sma->checkPermissions();

        $this->form_validation->set_message('is_natural_no_zero', $this->lang->line("no_zero_required"));
		 $this->form_validation->set_rules('from_store_id', $this->lang->line("from_store"), 'required');
		  $this->form_validation->set_rules('to_store_id', $this->lang->line("to_store"), 'required');
		$this->form_validation->set_rules('request_type', $this->lang->line("request_type"), 'required');
        if ($this->form_validation->run() == true) {
	    $n = $this->siteprocurment->lastidStoreRequest();
            $reference = 'SR'.str_pad($n + 1, 5, 0, STR_PAD_LEFT);
	    $date = date('Y-m-d H:i:s');
            $warehouse_id = $this->input->post('warehouse');
			$from_store_id = $this->input->post('from_store_id');
			$to_store_id = $this->input->post('to_store_id');
            $supplier_id = $this->input->post('supplier');
			$biller_id = $this->input->post('biller');
            $status = $this->input->post('status');
            
			
            $biller_details = $this->siteprocurment->getCompanyByID($biller_id);
            $biller = $biller_details->company != '-' ? $biller_details->company : $biller_details->name;
            if ($supplier_id) {
                $supplier_details = $this->siteprocurment->getCompanyByID($supplier_id);
                $supplier = $supplier_details->company != '-' ? $supplier_details->company : $supplier_details->name;
            } else {
                $supplier = NULL;
            }
            $note = $this->sma->clear_tags($this->input->post('note'));

            $total = 0;
            $product_tax = 0;
            $product_discount = 0;
            $gst_data = [];
            $total_cgst = $total_sgst = $total_igst = 0;
            $i = isset($_POST['product_code']) ? sizeof($_POST['product_code']) : 0;
            for ($r = 0; $r < $i; $r++) {
		$store_id = $from_store_id;
                $item_id = $_POST['product_id'][$r];
                $item_type = $_POST['product_type'][$r];
                $item_code = $_POST['product_code'][$r];
                $item_name = $_POST['product_name'][$r];
                //$item_option = isset($_POST['product_option'][$r]) && $_POST['product_option'][$r] != 'false' ? $_POST['product_option'][$r] : null;
                //$real_unit_price = $this->sma->formatDecimal($_POST['real_unit_price'][$r]);
               // $unit_price = $this->sma->formatDecimal($_POST['unit_price'][$r]);
               $item_unit_quantity = $_POST['quantity'][$r];
               // $item_tax_rate = isset($_POST['product_tax'][$r]) ? $_POST['product_tax'][$r] : null;
               // $item_discount = isset($_POST['product_discount'][$r]) ? $_POST['product_discount'][$r] : null;
                $item_unit = $_POST['product_unit'][$r];
                $item_quantity = $_POST['product_base_quantity'][$r];

               if (!empty($item_code)) {
                    $product_details = $item_type != 'manual' ? $this->store_request_model->getProductByCode($item_code) : null;
                    // $unit_price = $real_unit_price;
                   // $pr_discount = $this->siteprocurment->calculateDiscount($item_discount, $unit_price);
                  //  $unit_price = $this->sma->formatDecimal($unit_price - $pr_discount);
                   // $item_net_price = $unit_price;
                  //  $pr_item_discount = $this->sma->formatDecimal($pr_discount * $item_unit_quantity);
                  //  $product_discount += $pr_item_discount;
                  //  $pr_item_tax = $item_tax = 0;
                  //  $tax = "";

                   

                    $products[] = array(
                        'product_id' => $item_id,
                        'store_id' => $store_id,
                        'product_code' => $item_code,
                        'product_name' => $item_name,
                        'product_type' => $item_type,
                        'quantity' => $item_quantity,
						'unit_quantity' => $item_quantity,
                        'product_unit_id' => $item_unit,
                       // 'product_unit_code' => $unit->code,
                        'warehouse_id' => $warehouse_id,
                    );

                }
            }
            if (empty($products)) {
                $this->form_validation->set_rules('product', lang("order_items"), 'required');
            } else {
                $products;
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
            $data = array(
				'reference_no' => $reference,
				'date' => $date,
				'request_type' => $this->input->post('request_type'),
                
				'from_store_id' => $from_store_id,
				'to_store_id' => $to_store_id,
                'warehouse_id' => $warehouse_id,
                'note' => $note,
                'status' => $status,
                'created_by' => $this->session->userdata('user_id'),
				'approved_by' => $approved_by ? $approved_by : 0,
				'approved_on' => $date,
				'is_create' => date('Y-m-d H:i:s'),
                'hash' => hash('sha256', microtime() . mt_rand()),
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

            //$this->sma->print_arrays($data, $products);die;
        }

        if ($this->form_validation->run() == true && $this->store_request_model->addStore_request($data, $products)) {
            $this->session->set_userdata('remove_quls', 1);
            $this->session->set_flashdata('message', $this->lang->line("store_request_added"));
            admin_redirect('procurment/store_request');
        } else {
            $this->data['stores'] = $this->siteprocurment->getAllStores();
			$this->data['all_stores'] = $this->siteprocurment->getAllWarehouses();
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['billers'] = ($this->Owner || $this->Admin || !$this->session->userdata('biller_id')) ? $this->siteprocurment->getAllCompanies('biller') : null;
            //$this->data['currencies'] = $this->siteprocurment->getAllCurrencies();
            $this->data['warehouses'] = ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) ? $this->siteprocurment->getAllWarehouses() : null;
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('procurment/store_request'), 'page' => lang('store_request')), array('link' => '#', 'page' => lang('add_store_request')));
            $meta = array('page_title' => lang('add_store_request'), 'bc' => $bc);
            $this->page_construct('procurment/store_request/add', $meta, $this->data);
        }
    }

    public function edit($id = null)
    {
        ////$this->sma->checkPermissions();

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $inv = $this->store_request_model->getStore_requestByID($id);
		
				
		
		//if ($inv->status == 'approved' || $inv->status == 'completed') {
		//	$this->session->set_flashdata('error', lang("Do not allowed edit option"));
		//	admin_redirect("procurment/store_request");
		//}	
		
        
        $this->form_validation->set_message('is_natural_no_zero', $this->lang->line("no_zero_required"));
		
		 $this->form_validation->set_rules('request_type', $this->lang->line("request_type"), 'required');
        $this->form_validation->set_rules('warehouse', $this->lang->line("warehouse"), 'required');
		 // $this->form_validation->set_rules('to_store_id', $this->lang->line("to_store"), 'required');
        //$this->form_validation->set_rules('note', $this->lang->line("note"), 'xss_clean');

        if ($this->form_validation->run() == true) {

            
            $warehouse_id = $this->input->post('warehouse');
            $biller_id = $this->input->post('biller');
			$from_store_id = $this->input->post('from_store_id');
			$to_store_id = $this->input->post('to_store_id');
            $supplier_id = $this->input->post('supplier');
            $status = $this->input->post('status');
            $shipping = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
            $biller_details = $this->siteprocurment->getCompanyByID($biller_id);
            $biller = $biller_details->company != '-' ? $biller_details->company : $biller_details->name;
            if ($supplier_id) {
                $supplier_details = $this->siteprocurment->getCompanyByID($supplier_id);
                $supplier = $supplier_details->company != '-' ? $supplier_details->company : $supplier_details->name;
            } else {
                $supplier = NULL;
            }
            $note = $this->sma->clear_tags($this->input->post('note'));

            $total = 0;
            $product_tax = 0;
            $product_discount = 0;
            $gst_data = [];
            $total_cgst = $total_sgst = $total_igst = 0;
            $i = isset($_POST['product_code']) ? sizeof($_POST['product_code']) : 0;
           	for ($r = 0; $r < $i; $r++) {
                $item_id = $_POST['product_id'][$r];
                $item_type = $_POST['product_type'][$r];
                $item_code = $_POST['product_code'][$r];
                $item_name = $_POST['product_name'][$r];
                //$item_option = isset($_POST['product_option'][$r]) && $_POST['product_option'][$r] != 'false' ? $_POST['product_option'][$r] : null;
                //$real_unit_price = $this->sma->formatDecimal($_POST['real_unit_price'][$r]);
               // $unit_price = $this->sma->formatDecimal($_POST['unit_price'][$r]);
               $item_unit_quantity = $_POST['quantity'][$r];
               // $item_tax_rate = isset($_POST['product_tax'][$r]) ? $_POST['product_tax'][$r] : null;
               // $item_discount = isset($_POST['product_discount'][$r]) ? $_POST['product_discount'][$r] : null;
                $item_unit = $_POST['product_unit'][$r];
                $item_quantity = $_POST['product_base_quantity'][$r];


               if (!empty($item_code)) {
                    $product_details = $item_type != 'manual' ? $this->store_request_model->getProductByCode($item_code) : null;
                    // $unit_price = $real_unit_price;
                   // $pr_discount = $this->siteprocurment->calculateDiscount($item_discount, $unit_price);
                  //  $unit_price = $this->sma->formatDecimal($unit_price - $pr_discount);
                   // $item_net_price = $unit_price;
                  //  $pr_item_discount = $this->sma->formatDecimal($pr_discount * $item_unit_quantity);
                  //  $product_discount += $pr_item_discount;
                  //  $pr_item_tax = $item_tax = 0;
                  //  $tax = "";

                   

                    $products[] = array(
                        'product_id' => $item_id,
                        'product_code' => $item_code,
                        'product_name' => $item_name,
                        'product_type' => $item_type,
                        'quantity' => $item_unit_quantity,
						'unit_quantity' => $item_quantity,
                        'product_unit_id' => $item_unit,
                        //'product_unit_code' => $unit->code,
                        'warehouse_id' => $warehouse_id,
                    );

                }
            }
            if (empty($products)) {
                $this->form_validation->set_rules('product', lang("order_items"), 'required');
            } else {
                $products;
            }

            $data = array(
				'request_type' => $this->input->post('request_type'),
               // 'from_store_id' => $from_store_id,
                //'to_store_id' => $to_store_id,
                'warehouse_id' => $warehouse_id,
                'note' => $note,
                'status' => $status,
                'updated_by' => $this->session->userdata('user_id'),
				'is_update' => date('Y-m-d H:i:s'),
                'hash' => hash('sha256', microtime() . mt_rand()),
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
		@unlink($this->digital_upload_path.$inv->attachment);
            }

            //$this->sma->print_arrays($data, $products);die;
        }
        /*echo "<pre>";
print_r($this->input->post());die;*/
        if ($this->form_validation->run() == true && $this->store_request_model->updateStore_request($id, $data, $products)) {
            $this->session->set_userdata('remove_quls', 1);
            $this->session->set_flashdata('message', $this->lang->line("store_request_added"));
            admin_redirect('procurment/store_request');
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['inv'] = $this->store_request_model->getStore_requestByID($id);
            $inv_items = $this->store_request_model->getAllStore_requestItems($id);
            /*echo "<pre>";
             print_r($inv_items);die;*/
             krsort($inv_items);
            $c = rand(100000, 9999999);
            foreach ($inv_items as $item) {
                $row = $this->siteprocurment->getRecipeByID($item->product_id);
                if (!$row) {
                    $row = json_decode('{}');
                    $row->tax_method = 0;
                } else {
                    unset($row->details, $row->cost, $row->supplier1price, $row->supplier2price, $row->supplier3price, $row->supplier4price, $row->supplier5price);
                }
                $row->quantity = 0;
                $pis = $this->siteprocurment->getPurchasedItems($item->product_id, $item->warehouse_id, $item->option_id);
                if ($pis) {
                    foreach ($pis as $pi) {
                        $row->quantity += $pi->quantity_balance;
                    }
                }
                $row->id = $item->product_id;
                $row->code = $item->product_code;
                $row->name = $item->product_name;
                $row->type = $item->product_type;
                $row->base_quantity = $item->quantity;
                $row->base_unit = $row->unit ? $row->unit : $item->product_unit_id;
                $row->base_unit_price = $row->price ? $row->price : $item->unit_price;
                $row->unit = $item->product_unit_id;
                $row->qty = $item->quantity;
                $row->discount = $item->discount ? $item->discount : '0';
               // $row->price = $this->sma->formatDecimal($item->net_unit_price + $this->sma->formatDecimal($item->item_discount / $item->quantity));
                //$row->unit_price = $row->tax_method ? $item->unit_price + $this->sma->formatDecimal($item->item_discount / $item->quantity) + $this->sma->formatDecimal($item->item_tax / $item->quantity) : $item->unit_price + ($item->item_discount / $item->quantity);
                $row->real_unit_price = $item->real_unit_price;
                $row->tax_rate = $item->tax_rate_id;
                $row->option = $item->option_id;
                $options = $this->store_request_model->getProductOptions($row->id, $item->warehouse_id);

                if ($options) {
                    $option_quantity = 0;
                    foreach ($options as $option) {
                        $pis = $this->siteprocurment->getPurchasedItems($row->id, $item->warehouse_id, $item->option_id);
                        if ($pis) {
                            foreach ($pis as $pi) {
                                $option_quantity += $pi->quantity_balance;
                            }
                        }
                        if ($option->quantity > $option_quantity) {
                            $option->quantity = $option_quantity;
                        }
                    }
                }

                $combo_items = false;
                if ($row->type == 'combo') {
                    $combo_items = $this->store_request_model->getProductComboItems($row->id, $item->warehouse_id);
                    foreach ($combo_items as $combo_item) {
                        $combo_item->quantity = $combo_item->qty * $item->quantity;
                    }
                }
                $units = $this->siteprocurment->getUnitsByBUID($row->base_unit);
                $tax_rate = $this->siteprocurment->getTaxRateByID($row->tax_rate);
                $ri = $this->Settings->item_addition ? $row->id : $c;

                $pr[$ri] = array('id' => $c, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $row, 'combo_items' => $combo_items, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options);
                $c++;
            }
            $this->data['inv_items'] = json_encode($pr);
            $this->data['id'] = $id;
			$this->data['stores'] = $this->siteprocurment->getAllStores();
			$this->data['all_stores'] = $this->siteprocurment->getAllWarehouses();
            //$this->data['currencies'] = $this->siteprocurment->getAllCurrencies();
            $this->data['billers'] = ($this->Owner || $this->Admin || !$this->session->userdata('biller_id')) ? $this->siteprocurment->getAllCompanies('biller') : null;
            $this->data['tax_rates'] = $this->siteprocurment->getAllTaxRates();
            $this->data['warehouses'] = ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) ? $this->siteprocurment->getAllWarehouses() : null;

            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('procurment/store_request'), 'page' => lang('store_request')), array('link' => '#', 'page' => lang('edit_store_request')));
            $meta = array('page_title' => lang('edit_store_request'), 'bc' => $bc);
            $this->page_construct('procurment/store_request/edit', $meta, $this->data);
        }
    }

    public function delete($id = null)
    {
        //$this->sma->checkPermissions(NULL, true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->store_request_model->deleteStore_request($id)) {
            if ($this->input->is_ajax_store_request()) {
                $this->sma->send_json(array('error' => 0, 'msg' => lang("store_request_deleted")));
            }
            $this->session->set_flashdata('message', lang('store_request_deleted'));
            admin_redirect('procurment/welcome');
        }
    }

    public function suggestions()
    {
        $term = $this->input->get('term', true);
        $warehouse_id = $this->input->get('warehouse_id', true);
        $supplier_id = $this->input->get('supplier_id', true);

        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('procurment/welcome') . "'; }, 10);</script>");
        }

        $analyzed = $this->sma->analyze_term($term);
        $sr = $analyzed['term'];
        $option_id = $analyzed['option_id'];
        $warehouse = $this->siteprocurment->getWarehouseByID($warehouse_id);
       // $customer = $this->siteprocurment->getCompanyByID($customer_id);
       // $customer_group = $this->siteprocurment->getCustomerGroupByID($customer->customer_group_id);
        $rows = $this->siteprocurment->getProductNames($sr);
        if ($rows) {
            $c = str_replace(".", "", microtime(true));
            $r = 0;
            foreach ($rows as $row) {
                unset($row->cost, $row->details, $row->product_details, $row->image, $row->barcode_symbology, $row->cf1, $row->cf2, $row->cf3, $row->cf4, $row->cf5, $row->cf6, $row->supplier1price, $row->supplier2price, $row->cfsupplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1, $row->supplier2, $row->supplier3, $row->supplier4, $row->supplier5, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);
                $option = false;
                $row->quantity = 0;
                $row->item_tax_method = $row->tax_method;
                $row->qty = 1;
                $row->discount = '0';
                $options = $this->store_request_model->getProductOptions($row->id, $warehouse_id);
                if ($options) {
                    $opt = $option_id && $r == 0 ? $this->store_request_model->getProductOptionByID($option_id) : $options[0];
                    if (!$option_id || $r > 0) {
                        $option_id = $opt->id;
                    }
                } else {
                    $opt = json_decode('{}');
                    $opt->price = 0;
                    $option_id = FALSE;
                }
                $row->option = $option_id;
                $pis = $this->siteprocurment->getPurchasedItems($row->id, $warehouse_id, $row->option);
                if ($pis) {
                    foreach ($pis as $pi) {
                        $row->quantity += $pi->quantity_balance;
                    }
                }
                if ($options) {
                    $option_quantity = 0;
                    foreach ($options as $option) {
                        $pis = $this->siteprocurment->getPurchasedItems($row->id, $warehouse_id, $row->option);
                        if ($pis) {
                            foreach ($pis as $pi) {
                                $option_quantity += $pi->quantity_balance;
                            }
                        }
                        if ($option->quantity > $option_quantity) {
                            $option->quantity = $option_quantity;
                        }
                    }
                }
                if ($row->promotion) {
                    $row->price = $row->promo_price;
                } elseif ($customer->price_group_id) {
                    if ($pr_group_price = $this->siteprocurment->getProductGroupPrice($row->id, $customer->price_group_id)) {
                        $row->price = $pr_group_price->price;
                    }
                } elseif ($warehouse->price_group_id) {
                    if ($pr_group_price = $this->siteprocurment->getProductGroupPrice($row->id, $warehouse->price_group_id)) {
                        $row->price = $pr_group_price->price;
                    }
                }
                $row->price = $row->price + (($row->price * $customer_group->percent) / 100);
                $row->real_unit_price = $row->price;
                $row->base_quantity = 1;
                $row->base_unit = $row->unit;
                $row->base_unit_price = $row->price;
                $row->unit = $row->sale_unit ? $row->sale_unit : $row->unit;
                $combo_items = false;
                if ($row->type == 'combo') {
                    $combo_items = $this->store_request_model->getProductComboItems($row->id, $warehouse_id);
                }
                $units = $this->siteprocurment->getUnitsByBUID($row->base_unit);
                $tax_rate = $this->siteprocurment->getTaxRateByID($row->tax_rate);

                $pr[] = array('id' => ($c + $r), 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'category' => $row->category_id,
                    'row' => $row, 'combo_items' => $combo_items, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options);
                $r++;
            }
            $this->sma->send_json($pr);
        } else {
            $this->sma->send_json(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
        }
    }

    public function store_request_actions()
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
                        $this->store_request_model->deleteStore_request($id);
                    }
                    $this->session->set_flashdata('message', $this->lang->line("store_request_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);

                } elseif ($this->input->post('form_action') == 'combine') {

                    $html = $this->combine_pdf($_POST['val']);

                } elseif ($this->input->post('form_action') == 'export_excel') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('store_request'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference_no'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('biller'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('customer'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('total'));
                    $this->excel->getActiveSheet()->SetCellValue('F1', lang('status'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $qu = $this->store_request_model->getStore_requestByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($qu->date));
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $qu->reference_no);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $qu->biller);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $qu->customer);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $qu->total);
                        $this->excel->getActiveSheet()->SetCellValue('F' . $row, $qu->status);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'quotations_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', $this->lang->line("no_store_request_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
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

        if ($this->form_validation->run() == true && $this->store_request_model->updateStatus($id, $status, $note)) {
            $this->session->set_flashdata('message', lang('status_updated'));
            admin_redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'sales');
        } else {

            $this->data['inv'] = $this->store_request_model->getStore_requestByID($id);
            $this->data['modal_js'] = $this->siteprocurment->modal_js();
            $this->load->view($this->theme.'store_request/update_status', $this->data);

        }
    }

}
