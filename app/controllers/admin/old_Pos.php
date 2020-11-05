<?php defined('BASEPATH') or exit('No direct script access allowed');

class Pos extends MY_Controller
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
		$this->lang->admin_load('posnew', $this->Settings->user_language);
        $this->load->admin_model('pos_model');
        $this->load->admin_model('settings_model');
        $this->load->helper('text');
		$this->load->helper('shop');
        $this->pos_settings = $this->pos_model->getSetting();
        $this->settings = $this->pos_model->getSettings();
        /*echo "<pre>";
        print_r($this->settings);die;*/
        $this->pos_settings->pin_code = $this->pos_settings->pin_code ? md5($this->pos_settings->pin_code) : NULL;
        $this->data['pos_settings'] = $this->pos_settings;
        $this->data['settings'] = $this->settings;
        $this->session->set_userdata('last_activity', now());
        $this->lang->admin_load('pos', $this->Settings->user_language);
        $this->load->library('form_validation');
		$params = array(
			'host' => PRINTER_HOST,
			'port' => PRINTER_PORT,
			'path' => ''
		);
		$this->load->library('ws',$params);
		
		
		
    }
	
	
	public function notification(){
		$response = $this->site->notification_count($this->session->userdata('group_id'), $this->session->userdata('user_id'), $this->session->userdata('warehouse_id'));		
		echo json_encode($response);
		exit;
	}
	
	public function request_bil(){
		$response = $this->site->request_count($this->session->userdata('group_id'), $this->session->userdata('user_id'), $this->session->userdata('warehouse_id'));		
		echo json_encode($response);
		exit;
	}
	
	public function nitification_clear(){
		$notification_id = $this->input->post('notification_id');		
		$response = $this->site->notification_clear($notification_id);	
		echo json_encode($response);
		exit;
	}
	
    public function sales($warehouse_id = NULL)
    {
        $this->sma->checkPermissions('index');

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        if ($this->Owner) {
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['warehouse_id'] = $warehouse_id;
            $this->data['warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : NULL;
        } else {
            $user = $this->site->getUser();
            $this->data['warehouses'] = NULL;
            $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
            $this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : NULL;
        }

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('pos'), 'page' => lang('pos')), array('link' => '#', 'page' => lang('pos_sales')));
        $meta = array('page_title' => lang('pos_sales'), 'bc' => $bc);
        $this->page_construct('pos/sales', $meta, $this->data);
    }

    public function getSales($warehouse_id = NULL)
    {
        $this->sma->checkPermissions('index');

        if ((!$this->Owner || !$this->Admin) && !$warehouse_id) {
            $user = $this->site->getUser();
            $warehouse_id = $user->warehouse_id;
        }
        $duplicate_link = anchor('admin/pos/?duplicate=$1', '<i class="fa fa-plus-square"></i> ' . lang('duplicate_sale'), 'class="duplicate_pos"');
        $detail_link = anchor('admin/pos/view/$1', '<i class="fa fa-file-text-o"></i> ' . lang('view_receipt'));
        $detail_link2 = anchor('admin/sales/modal_view/$1', '<i class="fa fa-file-text-o"></i> ' . lang('sale_details_modal'), 'data-toggle="modal" data-target="#myModal"');
        $detail_link3 = anchor('admin/sales/view/$1', '<i class="fa fa-file-text-o"></i> ' . lang('sale_details'));
        $payments_link = anchor('admin/sales/payments/$1', '<i class="fa fa-money"></i> ' . lang('view_payments'), 'data-toggle="modal" data-target="#myModal"');
        $add_payment_link = anchor('admin/pos/add_payment/$1', '<i class="fa fa-money"></i> ' . lang('add_payment'), 'data-toggle="modal" data-target="#myModal"');
        $packagink_link = anchor('admin/sales/packaging/$1', '<i class="fa fa-archive"></i> ' . lang('packaging'), 'data-toggle="modal" data-target="#myModal"');
        $add_delivery_link = anchor('admin/sales/add_delivery/$1', '<i class="fa fa-truck"></i> ' . lang('add_delivery'), 'data-toggle="modal" data-target="#myModal"');
        $email_link = anchor('admin/#', '<i class="fa fa-envelope"></i> ' . lang('email_sale'), 'class="email_receipt" data-id="$1" data-email-address="$2"');
        $edit_link = anchor('admin/sales/edit/$1', '<i class="fa fa-edit"></i> ' . lang('edit_sale'), 'class="sledit"');
        $return_link = anchor('admin/sales/return_sale/$1', '<i class="fa fa-angle-double-left"></i> ' . lang('return_sale'));
        $delete_link = "<a href='#' class='po' title='<b>" . lang("delete_sale") . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('sales/delete/$1') . "'>"
            . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
            . lang('delete_sale') . "</a>";
        $action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
            . lang('actions') . ' <span class="caret"></span></button>
            <ul class="dropdown-menu pull-right" role="menu">
                <li>' . $duplicate_link . '</li>
                <li>' . $detail_link . '</li>
                <li>' . $detail_link2 . '</li>
                <li>' . $detail_link3 . '</li>
                <li>' . $payments_link . '</li>
                <li>' . $add_payment_link . '</li>
                <li>' . $packagink_link . '</li>
                <li>' . $add_delivery_link . '</li>
                <li>' . $edit_link . '</li>
                <li>' . $email_link . '</li>
                <li>' . $return_link . '</li>
                <li>' . $delete_link . '</li>
            </ul>
        </div></div>';

        $this->load->library('datatables');
        if ($warehouse_id) {
            $this->datatables
                ->select($this->db->dbprefix('sales') . ".id as id, DATE_FORMAT(date, '%Y-%m-%d %T') as date, reference_no, biller, customer, (grand_total+COALESCE(rounding, 0)), paid, (grand_total-paid) as balance, sale_status, payment_status, companies.email as cemail")
                ->from('sales')
                ->join('companies', 'companies.id=sales.customer_id', 'left')
                ->where('warehouse_id', $warehouse_id)
                ->group_by('sales.id');
        } else {
            $this->datatables
                ->select($this->db->dbprefix('sales') . ".id as id, DATE_FORMAT(date, '%Y-%m-%d %T') as date, reference_no, biller, customer, (grand_total+COALESCE(rounding, 0)), paid, (grand_total+rounding-paid) as balance, sale_status, payment_status, companies.email as cemail")
                ->from('sales')
                ->join('companies', 'companies.id=sales.customer_id', 'left')
                ->group_by('sales.id');
        }
        $this->datatables->where('pos', 1);
        if (!$this->Customer && !$this->Supplier && !$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $this->datatables->where('created_by', $this->session->userdata('user_id'));
        } elseif ($this->Customer) {
            $this->datatables->where('customer_id', $this->session->userdata('user_id'));
        }
        $this->datatables->add_column("Actions", $action, "id, cemail")->unset_column('cemail');
        echo $this->datatables->generate();
    }

    /* ---------------------------------------------------------------------------------------------------- */

    public function index($sid = NULL)
    {
        $t = $this->sma->checkPermissions('index');
         
		$order = !empty($_GET['order']) ? $_GET['order'] : '';
		$table = !empty($_GET['table']) ? $_GET['table'] : ''; 
        $split = !empty($_GET['split']) ? $_GET['split'] : '';
		$same_customer = !empty($_GET['same_customer']) ? $_GET['same_customer'] : '';
		
        if (!$this->pos_settings->default_biller || !$this->pos_settings->default_customer || !$this->pos_settings->default_category) {
            $this->session->set_flashdata('warning', lang('please_update_settings'));
            admin_redirect('pos/settings');
        }
        
        $user_group = $this->pos_model->getUserByID($this->session->userdata('user_id'));

		$gp = $this->settings_model->getGroupPermissions($user_group->group_id);
			

        if(($this->pos_settings->open_sale_register == 1) && ( ($gp->{'pos-open_sale_register'} == 1)) ){
        	
        	$register = $this->pos_model->registerData($this->session->userdata('user_id'));
        	$register_data = array('register_id' => $register->id, 'cash_in_hand' => $register->cash_in_hand, 'register_open_time' => $register->date);
            $this->session->set_userdata($register_data);

        	if($register){
        		$register_data = 'open';
        	}
        	else{     
	            $register_data = 'none';   	
	        	
	        }
          }else{  
	            $register_data = 'disable';   	
	      } 

        $this->data['register_data'] = $register_data;
        
        /*if ($register = $this->pos_model->registerData($this->session->userdata('user_id'))) {
            $register_data = array('register_id' => $register->id, 'cash_in_hand' => $register->cash_in_hand, 'register_open_time' => $register->date);
        	
            $this->session->set_userdata($register_data);
        } else {        	
            $this->session->set_flashdata('error', lang('register_not_open'));
            admin_redirect('pos/open_register');
        }*/

        $this->data['sid'] = $this->input->get('suspend_id') ? $this->input->get('suspend_id') : $sid;
		
		
        $did = $this->input->post('delete_id') ? $this->input->post('delete_id') : NULL;
        $suspend = $this->input->post('suspend') ? TRUE : FALSE;
        $count = $this->input->post('count') ? $this->input->post('count') : NULL;

        $duplicate_sale = $this->input->get('duplicate') ? $this->input->get('duplicate') : NULL;

        //validate form input
        $this->form_validation->set_rules('customer', $this->lang->line("customer"), 'trim|required');
        $this->form_validation->set_rules('warehouse', $this->lang->line("warehouse"), 'required');
        $this->form_validation->set_rules('biller', $this->lang->line("biller"), 'required');

		
		if(!empty($order)){
			if($order == 1 && !empty($table)){
				$table_view = 'table';
			}elseif($order == 2){
				$table_view = 'pos';
			}elseif($order == 3){
				$table_view = 'pos';
			}
			
			if(isset($table_view) == 'pos'){
				
				
				if ($this->form_validation->run() == TRUE) {
		
					$date = date('Y-m-d H:i:s');
					$warehouse_id = $this->input->post('warehouse');
					$customer_id = $this->input->post('customer');
					$biller_id = $this->input->post('biller');
					$total_items = $this->input->post('total_items');
					
					$payment_term = 0;
					$due_date = date('Y-m-d', strtotime('+' . $payment_term . ' days'));
					$shipping = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
					$customer_details = $this->site->getCompanyByID($customer_id);
					$customer = $customer_details->company != '-'  ? $customer_details->company : $customer_details->name;
					$biller_details = $this->site->getCompanyByID($biller_id);
					$biller = $biller_details->company != '-' ? $biller_details->company : $biller_details->name;
					$note = $this->sma->clear_tags($this->input->post('pos_note'));
					$staff_note = $this->sma->clear_tags($this->input->post('staff_note'));
					$reference = $this->site->getReference('pos');
		
					$total = 0;
					$recipe_tax = 0;
					$recipe_discount = 0;
					$digital = FALSE;
					$gst_data = [];
					$total_cgst = $total_sgst = $total_igst = 0;
					$i = isset($_POST['recipe_code']) ? sizeof($_POST['recipe_code']) : 0;
					for ($r = 0; $r < $i; $r++) {
						
						$item_addon = isset($_POST['recipe_addon'][$r]) && $_POST['recipe_addon'][$r] != 'false' ? $_POST['recipe_addon'][$r] : NULL;
						
						$item_id = $_POST['recipe_id'][$r];
						$item_type = $_POST['recipe_type'][$r];
						$item_code = $_POST['recipe_code'][$r];
						
						$buy_id = $_POST['buy_id'][$r];
						$buy_quantity = $_POST['buy_quantity'][$r];
						$get_item = $_POST['get_item'][$r];
						$get_quantity = $_POST['get_quantity'][$r];
						$total_get_quantity = $_POST['total_get_quantity'][$r];
						
						$item_name = $_POST['recipe_name'][$r];
						$item_comment = $_POST['recipe_comment'][$r];
						$item_option = isset($_POST['recipe_option'][$r]) && $_POST['recipe_option'][$r] != 'false' ? $_POST['recipe_option'][$r] : NULL;
						$real_unit_price = $this->sma->formatDecimal($_POST['real_unit_price'][$r]);
						$unit_price = $this->sma->formatDecimal($_POST['unit_price'][$r]);
						$item_unit_quantity = $_POST['quantity'][$r];
						$item_serial = isset($_POST['serial'][$r]) ? $_POST['serial'][$r] : '';
						$item_tax_rate = isset($_POST['recipe_tax'][$r]) ? $_POST['recipe_tax'][$r] : NULL;
						$item_discount = isset($_POST['recipe_discount'][$r]) ? $_POST['recipe_discount'][$r] : NULL;
						$item_unit = $_POST['recipe_unit'][$r];
						$item_quantity = $_POST['recipe_base_quantity'][$r];
		
						if (isset($item_code) && isset($real_unit_price) && isset($unit_price) && isset($item_quantity)) {
							$recipe_details = $item_type != 'manual' ? $this->pos_model->getrecipeByCode($item_code) : NULL;
							// $unit_price = $real_unit_price;
							if ($item_type == 'digital') {
								$digital = TRUE;
							}
							$pr_discount = $this->site->calculateDiscount($item_discount, $unit_price);
							$unit_price = $this->sma->formatDecimal($unit_price - $pr_discount);
							$item_net_price = $unit_price;
							$pr_item_discount = $this->sma->formatDecimal($pr_discount * $item_unit_quantity);
							$recipe_discount += $pr_item_discount;
							$pr_item_tax = $item_tax = 0;
							$tax = "";
		
							if (isset($item_tax_rate) && $item_tax_rate != 0) {
		
								$tax_details = $this->site->getTaxRateByID($item_tax_rate);
								$ctax = $this->site->calculateTax($recipe_details, $tax_details, $unit_price);
								$item_tax = $ctax['amount'];
								$tax = $ctax['tax'];
								if (!$recipe_details || (!empty($recipe_details) && $recipe_details->tax_method != 1)) {
									$item_net_price = $unit_price - $item_tax;
								}
								$pr_item_tax = $this->sma->formatDecimal(($item_tax * $item_unit_quantity), 4);
								if ($this->Settings->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, ($biller_details->state == $customer_details->state), $tax_details)) {
									$total_cgst += $gst_data['cgst'];
									$total_sgst += $gst_data['sgst'];
									$total_igst += $gst_data['igst'];
								}
							}
		
							$recipe_tax += $pr_item_tax;
							$subtotal = (($item_net_price * $item_unit_quantity) + $pr_item_tax);
							$unit = $this->site->getUnitByID($item_unit);
		
							$recipe = array(
								'recipe_id'      => $item_id,
								'recipe_code'    => $item_code,
								'recipe_name'    => $item_name,
								'recipe_type'    => $item_type,
								'option_id'       => $item_option,
								'addon_id' 		 => $item_addon,
								'buy_id'    	=> $buy_id,
								'buy_quantity'    	=> $buy_quantity,
								'get_item'   	 => $get_item,
								'get_quantity'    => $get_quantity,
								'total_get_quantity'    => $total_get_quantity,
								'net_unit_price'  => $item_net_price,
								'unit_price'      => $this->sma->formatDecimal($item_net_price + $item_tax),
								'quantity'        => $item_quantity,
								'recipe_unit_id' => $unit ? $unit->id : NULL,
								'recipe_unit_code' => $unit ? $unit->code : NULL,
								'unit_quantity' => $item_unit_quantity,
								'warehouse_id'    => $warehouse_id,
								'item_tax'        => $pr_item_tax,
								'tax_rate_id'     => $item_tax_rate,
								'tax'             => $tax,
								'discount'        => $item_discount,
								'item_discount'   => $pr_item_discount,
								'subtotal'        => $this->sma->formatDecimal($subtotal),
								'serial_no'       => $item_serial,
								'real_unit_price' => $real_unit_price,
								'comment'         => $item_comment,
							);
		
							$recipe[] = ($recipe + $gst_data);
							$total += $this->sma->formatDecimal(($item_net_price * $item_unit_quantity), 4);
						}
					}
					
					
					if (empty($recipe)) {
						$this->form_validation->set_rules('recipe', lang("order_items"), 'required');
					} elseif ($this->pos_settings->item_order == 1) {
						krsort($recipe);
					}
		
					$order_discount = $this->site->calculateDiscount($this->input->post('discount'), ($total + $recipe_tax));
					$total_discount = $this->sma->formatDecimal(($order_discount + $recipe_discount), 4);
					$order_tax = $this->site->calculateOrderTax($this->input->post('order_tax'), ($total + $recipe_tax - $total_discount));
					$total_tax = $this->sma->formatDecimal(($recipe_tax + $order_tax), 4);
					$grand_total = $this->sma->formatDecimal(($total + $total_tax + $this->sma->formatDecimal($shipping) - $order_discount), 4);
					$rounding = 0;
					if ($this->pos_settings->rounding) {
						$round_total = $this->sma->roundNumber($grand_total, $this->pos_settings->rounding);
						$rounding = $this->sma->formatMoney($round_total - $grand_total);
					}
					$data = array('date'  => $date,
						'reference_no'      => $reference,
						'customer_id'       => $customer_id,
						'customer'          => $customer,
						'biller_id'         => $biller_id,
						'biller'            => $biller,
						'warehouse_id'      => $warehouse_id,
						'note'              => $note,
						'staff_note'        => $staff_note,
						'total'             => $total,
						'recipe_discount'  => $recipe_discount,
						'order_discount_id' => $this->input->post('discount'),
						'order_discount'    => $order_discount,
						'total_discount'    => $total_discount,
						'recipe_tax'       => $recipe_tax,
						'order_tax_id'      => $this->input->post('order_tax'),
						'order_tax'         => $order_tax,
						'total_tax'         => $total_tax,
						'shipping'          => $this->sma->formatDecimal($shipping),
						'grand_total'       => $grand_total,
						'total_items'       => $total_items,
						'sale_status'       => $sale_status,
						'payment_status'    => $payment_status,
						'payment_term'      => $payment_term,
						'rounding'          => $rounding,
						'suspend_note'      => $this->input->post('suspend_note'),
						'pos'               => 1,
						'paid'              => $this->input->post('amount-paid') ? $this->input->post('amount-paid') : 0,
						'created_by'        => $this->session->userdata('user_id'),
						'hash'              => hash('sha256', microtime() . mt_rand()),
						);
					if ($this->Settings->indian_gst) {
						$data['cgst'] = $total_cgst;
						$data['sgst'] = $total_sgst;
						$data['igst'] = $total_igst;
					}
		
					if (!$suspend) {
						$p = isset($_POST['amount']) ? sizeof($_POST['amount']) : 0;
						$paid = 0;
						for ($r = 0; $r < $p; $r++) {
							if (isset($_POST['amount'][$r]) && !empty($_POST['amount'][$r]) && isset($_POST['paid_by'][$r]) && !empty($_POST['paid_by'][$r])) {
								$amount = $this->sma->formatDecimal($_POST['balance_amount'][$r] > 0 ? $_POST['amount'][$r] - $_POST['balance_amount'][$r] : $_POST['amount'][$r]);
								if ($_POST['paid_by'][$r] == 'deposit') {
									if ( ! $this->site->check_customer_deposit($customer_id, $amount)) {
										$this->session->set_flashdata('error', lang("amount_greater_than_deposit"));
										redirect($_SERVER["HTTP_REFERER"]);
									}
								}
								if ($_POST['paid_by'][$r] == 'gift_card') {
									$gc = $this->site->getGiftCardByNO($_POST['paying_gift_card_no'][$r]);
									$amount_paying = $_POST['amount'][$r] >= $gc->balance ? $gc->balance : $_POST['amount'][$r];
									$gc_balance = $gc->balance - $amount_paying;
									$payment[] = array(
										'date'         => $date,
										// 'reference_no' => $this->site->getReference('pay'),
										'amount'       => $amount,
										'paid_by'      => $_POST['paid_by'][$r],
										'cheque_no'    => $_POST['cheque_no'][$r],
										'cc_no'        => $_POST['paying_gift_card_no'][$r],
										'cc_holder'    => $_POST['cc_holder'][$r],
										'cc_month'     => $_POST['cc_month'][$r],
										'cc_year'      => $_POST['cc_year'][$r],
										'cc_type'      => $_POST['cc_type'][$r],
										'cc_cvv2'      => $_POST['cc_cvv2'][$r],
										'created_by'   => $this->session->userdata('user_id'),
										'type'         => 'received',
										'note'         => $_POST['payment_note'][$r],
										'pos_paid'     => $_POST['amount'][$r],
										'pos_balance'  => $_POST['balance_amount'][$r],
										'gc_balance'  => $gc_balance,
										);
		
								} else {
									$payment[] = array(
										'date'         => $date,
										// 'reference_no' => $this->site->getReference('pay'),
										'amount'       => $amount,
										'paid_by'      => $_POST['paid_by'][$r],
										'cheque_no'    => $_POST['cheque_no'][$r],
										'cc_no'        => $_POST['cc_no'][$r],
										'cc_holder'    => $_POST['cc_holder'][$r],
										'cc_month'     => $_POST['cc_month'][$r],
										'cc_year'      => $_POST['cc_year'][$r],
										'cc_type'      => $_POST['cc_type'][$r],
										'cc_cvv2'      => $_POST['cc_cvv2'][$r],
										'created_by'   => $this->session->userdata('user_id'),
										'type'         => 'received',
										'note'         => $_POST['payment_note'][$r],
										'pos_paid'     => $_POST['amount'][$r],
										'pos_balance'  => $_POST['balance_amount'][$r],
										);
		
								}
		
							}
						}
					}
					if (!isset($payment) || empty($payment)) {
						$payment = array();
					}
		
					// $this->sma->print_arrays($data, $recipe, $payment);
				}
		
				if ($this->form_validation->run() == TRUE && !empty($recipe) && !empty($data)) {
					if ($suspend) {
						if ($this->pos_model->suspendSale($data, $recipe, $did)) {
							$this->session->set_userdata('remove_posls', 1);
							$this->session->set_flashdata('message', $this->lang->line("sale_suspended"));
							admin_redirect("pos");
						}
					} else {
						if ($sale = $this->pos_model->addSale($data, $recipe, $payment, $did)) {
							$this->session->set_userdata('remove_posls', 1);
							$msg = $this->lang->line("sale_added");
							if (!empty($sale['message'])) {
								foreach ($sale['message'] as $m) {
									$msg .= '<br>' . $m;
								}
							}
							$this->session->set_flashdata('message', $msg);
							$redirect_to = $this->pos_settings->after_sale_page ? "pos" : "pos/view/" . $sale['sale_id'];
							if ($this->pos_settings->auto_print) {
								if ($this->Settings->remote_printing != 1) {
									$redirect_to .= '?print='.$sale['sale_id'];
								}
							}
							admin_redirect($redirect_to);
						}
					}
				} 
				else {
					$this->data['old_sale'] = NULL;
					$this->data['oid'] = NULL;
					if ($duplicate_sale) {
						if ($old_sale = $this->pos_model->getInvoiceByID($duplicate_sale)) {
							$inv_items = $this->pos_model->getSaleItems($duplicate_sale);
							$this->data['oid'] = $duplicate_sale;
							$this->data['old_sale'] = $old_sale;
							$this->data['message'] = lang('old_sale_loaded');
							$this->data['customer'] = $this->pos_model->getCompanyByID($old_sale->customer_id);
						} else {
							$this->session->set_flashdata('error', lang("bill_x_found"));
							admin_redirect("pos");
						}
					}
					$this->data['suspend_sale'] = NULL;
					if ($sid) {
						if ($suspended_sale = $this->pos_model->getOpenBillByID($sid)) {
							$inv_items = $this->pos_model->getSuspendedSaleItems($sid);
							$this->data['sid'] = $sid;
							$this->data['suspend_sale'] = $suspended_sale;
							$this->data['message'] = lang('suspended_sale_loaded');
							$this->data['customer'] = $this->pos_model->getCompanyByID($suspended_sale->customer_id);
							$this->data['reference_note'] = $suspended_sale->suspend_note;
						} else {
							$this->session->set_flashdata('error', lang("bill_x_found"));
							admin_redirect("pos");
						}
					}
		
					if (($sid || $duplicate_sale) && $inv_items) {
							// krsort($inv_items);
							$c = rand(100000, 9999999);
							foreach ($inv_items as $item) {
								$row = $this->site->getrecipeByID($item->recipe_id);
								
								$buy = $this->site->checkBuyget($row->id);
								if(!empty($buy)){
									$row->buy_id = $buy->id;
									$row->buy_quantity = $buy->buy_quantity;
									$row->get_item = $buy->get_item;
									$row->get_quantity = $buy->get_quantity;
									$row->total_get_quantity = $buy->get_quantity;
									$total_quantity = $x_quantity % $y_quantity;
									$x_quantity = ($x_quantity - $total_quantity) / $y_quantity;
									$total_get_quantity = $x_quantity * $b_quantity;
									$row->total_get_quantity = $total_get_quantity;
									
									$row->free_recipe = $buy->free_recipe;
								}else{
									$row->buy_id = 0;
									$row->get_item = 0;
									$row->buy_quantity = 0;
									$row->get_quantity = 0;
									$row->total_get_quantity = 0;
									$row->free_recipe = '';
								}
								
								if (!$row) {
									$row = json_decode('{}');
									$row->tax_method = 0;
									$row->quantity = 0;
								} else {
									$category = $this->site->getCategoryByID($row->category_id);
									$row->category_name = $category->name;
									unset($row->cost, $row->details, $row->recipe_details, $row->image, $row->barcode_symbology, $row->cf1, $row->cf2, $row->cf3, $row->cf4, $row->cf5, $row->cf6, $row->supplier1price, $row->supplier2price, $row->cfsupplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1, $row->supplier2, $row->supplier3, $row->supplier4, $row->supplier5, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);
								}
								$pis = $this->site->getPurchasedItems($item->recipe_id, $item->warehouse_id, $item->option_id);
								if ($pis) {
									foreach ($pis as $pi) {
										$row->quantity += $pi->quantity_balance;
									}
								}
								$row->id = $item->recipe_id;
								$row->code = $item->recipe_code;
								$row->name = $item->recipe_name;
								$row->type = $item->recipe_type;
								$row->quantity += $item->quantity;
								$row->discount = $item->discount ? $item->discount : '0';
								$row->price = $this->sma->formatDecimal($item->net_unit_price + $this->sma->formatDecimal($item->item_discount / $item->quantity));
								$row->unit_price = $row->tax_method ? $item->unit_price + $this->sma->formatDecimal($item->item_discount / $item->quantity) + $this->sma->formatDecimal($item->item_tax / $item->quantity) : $item->unit_price + ($item->item_discount / $item->quantity);
								$row->real_unit_price = $item->real_unit_price;
								$row->base_quantity = $item->quantity;
								$row->base_unit = isset($row->unit) ? $row->unit : $item->recipe_unit_id;
								$row->base_unit_price = $row->price ? $row->price : $item->unit_price;
								$row->unit = $item->recipe_unit_id;
								$row->qty = $item->unit_quantity;
								$row->tax_rate = $item->tax_rate_id;
								$row->serial = $item->serial_no;
								$row->option = $item->option_id;
								$row->addon = $item->addon_id;
								$options = $this->pos_model->getrecipeOptions($row->id, $item->warehouse_id);
								$addons = $this->pos_model->getrecipeAddons($row->id);
		
								if ($options) {
									$option_quantity = 0;
									foreach ($options as $option) {
										$pis = $this->site->getPurchasedItems($row->id, $item->warehouse_id, $item->option_id);
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
		
								$row->comment = isset($item->comment) ? $item->comment : '';
								$row->ordered = 1;
								$combo_items = false;
								if ($row->type == 'combo') {
									$combo_items = $this->pos_model->getrecipeComboItems($row->id, $item->warehouse_id);
								}
								$units = $this->site->getUnitsByBUID($row->base_unit);
								$tax_rate = $this->site->getTaxRateByID($row->tax_rate);
								$ri = $this->Settings->item_addition ? $row->id : $c;
		
								$pr[$ri] = array('id' => $c, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")",
										'row' => $row, 'combo_items' => $combo_items, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options, 'addons' => $addons);
								$c++;
							}
		
							$this->data['items'] = json_encode($pr);
		
					} else {
						$this->data['customer'] = $this->pos_model->getCompanyByID($this->pos_settings->default_customer);
						$this->data['reference_note'] = NULL;
					}
		
					$this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
					$this->data['message'] = isset($this->data['message']) ? $this->data['message'] : $this->session->flashdata('message');
		
					// $this->data['biller'] = $this->site->getCompanyByID($this->pos_settings->default_biller);
					$this->data['billers'] = $this->site->getAllCompanies('biller');
					$this->data['sales_types'] = $this->site->getAllSalestype();
					$this->data['warehouses'] = $this->site->getAllWarehouses();
					$this->data['tax_rates'] = $this->site->getAllTaxRates();
					$this->data['user'] = $this->site->getUser();
					$this->data["tcp"] = $this->pos_model->recipe_count($this->pos_settings->default_category, $this->session->userdata('warehouse_id'));
					$this->data['recipe'] = $this->ajaxrecipe($this->pos_settings->default_category, $this->session->userdata('warehouse_id'));
					$this->data['categories'] = $this->site->getAllrecipeCategories();
					$this->data['brands'] = $this->site->getAllBrands();
					$this->data['subcategories'] = $this->site->getrecipeSubCategories($this->pos_settings->default_category);
					$this->data['printer'] = $this->pos_model->getPrinterByID($this->pos_settings->printer);
					$order_printers = json_decode($this->pos_settings->order_printers);
					$printers = array();
					if (!empty($order_printers)) {
						foreach ($order_printers as $printer_id) {
							$printers[] = $this->pos_model->getPrinterByID($printer_id);
						}
					}
					$this->data['order_printers'] = $printers;
					$this->data['pos_settings'] = $this->pos_settings;
					
					$this->data['areas'] = $this->pos_model->getTablelist($this->session->userdata('warehouse_id'));
					
					$this->data['get_table'] = $table;
                    $this->data['get_order_type'] = $order;
					$this->data['get_split'] = $split;
					$this->data['same_customer'] = $same_customer;
					
					if ($this->pos_settings->after_sale_page && $saleid = $this->input->get('print', true)) {
						if ($inv = $this->pos_model->getInvoiceByID($saleid)) {
							$this->load->helper('pos');
							if (!$this->session->userdata('view_right')) {
								$this->sma->view_rights($inv->created_by, true);
							}
							$this->data['rows'] = $this->pos_model->getAllInvoiceItems($inv->id);
							$this->data['biller'] = $this->pos_model->getCompanyByID($inv->biller_id);
							$this->data['customer'] = $this->pos_model->getCompanyByID($inv->customer_id);
							$this->data['payments'] = $this->pos_model->getInvoicePayments($inv->id);
							$this->data['return_sale'] = $inv->return_id ? $this->pos_model->getInvoiceByID($inv->return_id) : NULL;
							$this->data['return_rows'] = $inv->return_id ? $this->pos_model->getAllInvoiceItems($inv->return_id) : NULL;
							$this->data['return_payments'] = $this->data['return_sale'] ? $this->pos_model->getInvoicePayments($this->data['return_sale']->id) : NULL;
							$this->data['inv'] = $inv;
							$this->data['print'] = $inv->id;
							
							$this->data['created_by'] = $this->site->getUser($inv->created_by);
							
							
						}
					}
					
				
					$this->load->view($this->theme . 'pos/add', $this->data);
				}
			
			}else{
				
							
					$this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
					$this->data['message'] = isset($this->data['message']) ? $this->data['message'] : $this->session->flashdata('message');
		
					// $this->data['biller'] = $this->site->getCompanyByID($this->pos_settings->default_biller);
					$this->data['billers'] = $this->site->getAllCompanies('biller');
					$this->data['sales_types'] = $this->site->getAllSalestype();
					$this->data['tables'] = $this->site->getAllTables();
					$this->data['warehouses'] = $this->site->getAllWarehouses();
					$this->data['tax_rates'] = $this->site->getAllTaxRates();
					$this->data['user'] = $this->site->getUser();
					$this->data["tcp"] = $this->pos_model->recipe_count($this->pos_settings->default_category, $this->session->userdata('warehouse_id'));
					$this->data['recipe'] = $this->ajaxrecipe($this->pos_settings->default_category, $this->session->userdata('warehouse_id'));
					$this->data['categories'] = $this->site->getAllrecipeCategories();
					$this->data['brands'] = $this->site->getAllBrands();
					$this->data['subcategories'] = $this->site->getrecipeSubCategories($this->pos_settings->default_category);
					
					$this->data['pos_settings'] = $this->pos_settings;
					
					$this->data['order_type'] = $order;
					$this->load->view($this->theme . 'pos/tables', $this->data);
				
			}
		
		}else{
					
					$this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
					$this->data['message'] = isset($this->data['message']) ? $this->data['message'] : $this->session->flashdata('message');
		
					// $this->data['biller'] = $this->site->getCompanyByID($this->pos_settings->default_biller);
					$this->data['billers'] = $this->site->getAllCompanies('biller');
					$this->data['sales_types'] = $this->site->getAllSalestype();
					$this->data['tables'] = $this->site->getAllTables();
					$this->data['warehouses'] = $this->site->getAllWarehouses();
					$this->data['tax_rates'] = $this->site->getAllTaxRates();
					$this->data['user'] = $this->site->getUser();
					$this->data["tcp"] = $this->pos_model->recipe_count($this->pos_settings->default_category, $this->session->userdata('warehouse_id'));
					$this->data['recipe'] = $this->ajaxrecipe($this->pos_settings->default_category, $this->session->userdata('warehouse_id'));
					$this->data['categories'] = $this->site->getAllrecipeCategories();
					$this->data['brands'] = $this->site->getAllBrands();
					$this->data['subcategories'] = $this->site->getrecipeSubCategories($this->pos_settings->default_category);
					
					$this->data['pos_settings'] = $this->pos_settings;
					$this->data['group'] = $this->session->userdata('group_id');
					
					
					$this->load->view($this->theme . 'pos/pos_type', $this->data);
		}
		
    }
	
	public function ajax_tables(){
		$this->data['areas'] = $this->pos_model->getTablelist($this->session->userdata('warehouse_id'));
		$this->load->view($this->theme . 'pos/tables_ajax', $this->data);	
	}
	
	public function sent_to_kitchen($sid = NULL)
    {
		
		//validate form input
        $this->form_validation->set_rules('customer', $this->lang->line("customer"), 'trim|required');
        $this->form_validation->set_rules('warehouse', $this->lang->line("warehouse"), 'required');
        $this->form_validation->set_rules('biller', $this->lang->line("biller"), 'required');

        if ($this->form_validation->run() == TRUE) {
			
			
			
            $date = date('Y-m-d H:i:s');
            $warehouse_id = $this->input->post('warehouse');
            $customer_id = $this->input->post('customer');
            $biller_id = $this->input->post('biller');
            $total_items = $this->input->post('total_items');
            
            $payment_term = 0;
            $due_date = date('Y-m-d', strtotime('+' . $payment_term . ' days'));
            $shipping = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
            $customer_details = $this->site->getCompanyByID($customer_id);
            $customer = $customer_details->name;
            $biller_details = $this->site->getCompanyByID($biller_id);
            $biller = $biller_details->name;
            $note = $this->sma->clear_tags($this->input->post('pos_note'));
            $staff_note = $this->sma->clear_tags($this->input->post('staff_note'));
            $reference = 'ORDER'.date('YmdHis');
			$split_id = $this->input->post('split_id') ? $this->input->post('split_id') : 'SPILT'.date('YmdHis');

            $total = 0;
            $recipe_tax = 0;
            $recipe_discount = 0;
            $digital = FALSE;
            $gst_data = [];
            $total_cgst = $total_sgst = $total_igst = 0;
            $i = isset($_POST['recipe_code']) ? sizeof($_POST['recipe_code']) : 0;
			
			
            for ($r = 0; $r < $i; $r++) {
                $item_id = $_POST['recipe_id'][$r];
                $item_type = $_POST['recipe_type'][$r];
                $item_code = $_POST['recipe_code'][$r];
                $item_name = $_POST['recipe_name'][$r];
				
				 $buy_id = $_POST['buy_id'][$r];
				 $buy_quantity = $_POST['buy_quantity'][$r];
				 $kitchen_type_id = $_POST['kitchen_type_id'][$r];
				  $get_item = $_POST['get_item'][$r];
				   $get_quantity = $_POST['get_quantity'][$r];
				    $total_get_quantity = $_POST['total_get_quantity'][$r];
				
                $item_comment = $_POST['recipe_comment'][$r];
				//$item_addon = isset($_POST['recipe_addon'][$r]) && $_POST['recipe_addon'][$r] != 'false' ? $_POST['recipe_addon'][$r] : NULL;
				$item_addon = (!is_object($_POST['recipe_addon'][$r])) ? $_POST['recipe_addon'][$r] : NULL;
                $item_option = isset($_POST['recipe_option'][$r]) && $_POST['recipe_option'][$r] != 'false' ? $_POST['recipe_option'][$r] : NULL;
                $real_unit_price = $this->sma->formatDecimal($_POST['real_unit_price'][$r]);
                $unit_price = $this->sma->formatDecimal($_POST['unit_price'][$r]);
                $item_unit_quantity = $_POST['quantity'][$r];
                $item_serial = isset($_POST['serial'][$r]) ? $_POST['serial'][$r] : '';
                $item_tax_rate = isset($_POST['recipe_tax'][$r]) ? $_POST['recipe_tax'][$r] : NULL;
                $item_discount = isset($_POST['recipe_discount'][$r]) ? $_POST['recipe_discount'][$r] : NULL;
                $item_unit = $_POST['recipe_unit'][$r];
                $item_quantity = $_POST['recipe_base_quantity'][$r];

                if (isset($item_code) && isset($real_unit_price) && isset($unit_price) && isset($item_quantity)) {
                    $recipe_details = $item_type != 'manual' ? $this->pos_model->getrecipeByCode($item_code) : NULL;
                    // $unit_price = $real_unit_price;
                    if ($item_type == 'digital') {
                        $digital = TRUE;
                    }
                    $pr_discount = $this->site->calculateDiscount($item_discount, $unit_price);
                    $unit_price = $this->sma->formatDecimal($unit_price - $pr_discount);
                    $item_net_price = $unit_price;
                    $pr_item_discount = $this->sma->formatDecimal($pr_discount * $item_unit_quantity);
                    $recipe_discount += $pr_item_discount;
                    $pr_item_tax = $item_tax = 0;
                    $tax = "";

                    if (isset($item_tax_rate) && $item_tax_rate != 0) {
						
                        $tax_details = $this->site->getTaxRateByID($item_tax_rate);
                        $ctax = $this->site->calculateTax($recipe_details, $tax_details, $unit_price);
						
                        $item_tax = $ctax['amount'];
                        $tax = $ctax['tax'];
						
                        if (!$recipe_details || (!empty($recipe_details) && $recipe_details->tax_method != 1)) {
                            $item_net_price = $unit_price - $item_tax;
                        }
                        $pr_item_tax = $this->sma->formatDecimal(($item_tax * $item_unit_quantity), 4);
                        if ($this->Settings->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, ($biller_details->state == $customer_details->state), $tax_details)) {
                            $total_cgst += $gst_data['cgst'];
                            $total_sgst += $gst_data['sgst'];
                            $total_igst += $gst_data['igst'];
                        }
                    }

                    $recipe_tax += $pr_item_tax;
                    $subtotal = (($item_net_price * $item_unit_quantity) + $pr_item_tax);
                    $unit = $this->site->getUnitByID($item_unit);

                    $recipe_item = array(
                        'recipe_id'      => $item_id,
						'item_status' 	 => 'Inprocess',
						'kitchen_type_id' => $kitchen_type_id ? $kitchen_type_id : 1,
                        'recipe_code'    => $item_code,
                        'recipe_name'    => $item_name,
						'buy_id'    => $buy_id ? $buy_id : 0,
						'buy_quantity'    => $buy_quantity ? $buy_quantity : 0,
						'get_item'    => $get_item ? $get_item : 0,
						'get_quantity'    => $get_quantity ? $get_quantity : 0,
						'total_get_quantity'    => $total_get_quantity ? $total_get_quantity : 0,
                        'recipe_type'    => $item_type,
                        'option_id'       => $item_option,
						'addon_id' 		=> $item_addon,
                        'net_unit_price'  => $item_net_price,
                        'unit_price'      => $this->sma->formatDecimal($item_net_price + $item_tax),
                        'quantity'        => $item_quantity,
                        'recipe_unit_id' => $unit ? $unit->id : NULL,
                        'recipe_unit_code' => $unit ? $unit->code : NULL,
                        'unit_quantity' => $item_unit_quantity,
                        'warehouse_id'    => $warehouse_id,
                        'item_tax'        => $pr_item_tax,
                        'tax_rate_id'     => $item_tax_rate,
                        'tax'             => $tax,
                        'discount'        => $item_discount,
                        'item_discount'   => $pr_item_discount,
                        'subtotal'        => $this->sma->formatDecimal($subtotal),
                        'serial_no'       => $item_serial,
                        'real_unit_price' => $real_unit_price,
                        'comment'         => $item_comment,
						'time_started' 	 => date('Y-m-d H:i:s'),
						'created_on' => date('Y-m-d H:i:s'),
                    );

                    $recipe[] = ($recipe_item + $gst_data);
                    $total += $this->sma->formatDecimal(($item_net_price * $item_unit_quantity), 4);
                }
            }
            if (empty($recipe)) {
                $this->form_validation->set_rules('recipe', lang("order_items"), 'required');
            } elseif ($this->pos_settings->item_order == 1) {
                krsort($recipe);
            }

            $order_discount = $this->site->calculateDiscount($this->input->post('discount'), ($total + $recipe_tax));
            $total_discount = $this->sma->formatDecimal(($order_discount + $recipe_discount), 4);
            $order_tax = $this->site->calculateOrderTax($this->input->post('order_tax'), ($total + $recipe_tax - $total_discount));
            $total_tax = $this->sma->formatDecimal(($recipe_tax + $order_tax), 4);
            $grand_total = $this->sma->formatDecimal(($total + $total_tax + $this->sma->formatDecimal($shipping) - $order_discount), 4);
            $rounding = 0;
            if ($this->pos_settings->rounding) {
                $round_total = $this->sma->roundNumber($grand_total, $this->pos_settings->rounding);
                $rounding = $this->sma->formatMoney($round_total - $grand_total);
            }
            $data = array('date'  => $date,
                'reference_no'      => $reference,
				'table_id'			=> !empty($this->input->post('table_list_id'))  ? $this->input->post('table_list_id')  : 0 ,
				'seats_id'			=> !empty($this->input->post('table_list_id')) ? $this->input->post('seats_id') : 0,
				'split_id'			=> $split_id,
				'order_type' 		=> $this->input->post('order_type_id'),
				'order_status' 		=> 'Open',
                'customer_id'       => $customer_id,
                'customer'          => $customer,
                'biller_id'         => $biller_id,
                'biller'            => $biller,
                'warehouse_id'      => $warehouse_id,
                'note'              => $note,
                'staff_note'        => $staff_note,
                'total'             => $total,
                'recipe_discount'  => $recipe_discount,
                'order_discount_id' => $this->input->post('discount'),
                'order_discount'    => $order_discount,
                'total_discount'    => $total_discount,
                'recipe_tax'       => $recipe_tax,
                'order_tax_id'      => $this->input->post('order_tax'),
                'order_tax'         => $order_tax,
                'total_tax'         => $total_tax,
                'shipping'          => $this->sma->formatDecimal($shipping),
                'grand_total'       => $grand_total,
                'total_items'       => $total_items,
                /*'sale_status'       => $sale_status,
                'payment_status'    => $payment_status,*/
                'payment_term'      => $payment_term,
                'rounding'          => $rounding,
                'suspend_note'      => $this->input->post('suspend_note'),
                'pos'               => 1,
                'paid'              => $this->input->post('amount-paid') ? $this->input->post('amount-paid') : 0,
                'created_by'        => $this->session->userdata('user_id'),
                'hash'              => hash('sha256', microtime() . mt_rand()),
                );
            if ($this->Settings->indian_gst) {
                $data['cgst'] = $total_cgst;
                $data['sgst'] = $total_sgst;
                $data['igst'] = $total_igst;
            }
	    if($data['table_id']!=0) $data['table_whitelisted'] = $this->pos_model->isTableWhitelisted($data['table_id']);
			
			$kitchen = array(
				'waiter_id' => $this->session->userdata('user_id'),
				'status' => 'Inprocess' 
			);
			
			if($this->session->userdata('group_id') == 5){
				$role = ' (Sale) ';
			}elseif($this->session->userdata('group_id') == 7){
				$role = ' (Waiter) ';
			}
			if($this->input->post('order_type_id') == 1){ 
				$notification_message = $this->session->userdata('username').$role.'  has been create new dine in order. it will be process sent to kitchen'; 
			}elseif($this->input->post('order_type_id') == 2){ 
				$notification_message = $this->session->userdata('username').$role.'  has been create new takeaway order. it will be process sent to kitchen'; 
			}elseif($this->input->post('order_type_id') == 3){ 
				$notification_message = $this->session->userdata('username').$role.' has been create new door delivery order. it will be process sent to kitchen'; 
			}
			
					
			
			$notification_array['from_role'] = $this->session->userdata('group_id');
			$notification_array['insert_array'] = array(
				'msg' => $notification_message,
				'type' => 'Send to kitchen',
				'table_id' => !empty($this->input->post('table_list_id'))  ? $this->input->post('table_list_id')  : 0 ,
				'user_id' => $this->session->userdata('user_id'),	
				'role_id' => KITCHEN,
				'warehouse_id' => $warehouse_id,
				'created_on' => date('Y-m-d H:m:s'),
				'is_read' => 0
			);
            // $this->sma->print_arrays($data, $recipe, $kitchen);
			
        }
		
		
        if ($this->form_validation->run() == TRUE && !empty($recipe) && !empty($data) && !empty($kitchen)) {
			
			
			if ($sale = $this->pos_model->addKitchen($data, $recipe, $kitchen, $notification_array,  $this->session->userdata('warehouse_id'), $this->session->userdata('user_id'))) {
			
			
			$this->remotePrintingKOT($sale['kitchen_data']);
				
			$this->session->set_userdata('remove_posls', 1);
				$msg = $this->lang->line("sale_added");
				if (!empty($sale['message'])) {
					foreach ($sale['message'] as $m) {
						$msg .= '<br>' . $m;
					}
				}
				$this->session->set_flashdata('message', $msg);
				admin_redirect("pos");
            }
        }else{
			admin_redirect("pos");
		}
		
    }
	
	public function remotePrintingKOT_new($kitchen_data=array()){
		if(!empty($kitchen_data)){
			
			
			
			$this->data['user'] = $this->site->getUser($kitchen_data['orders_details']->created_by); 
			$this->data['biller'] = $this->site->getCompanyOrderByID($kitchen_data['orders_details']->biller_id);
			if($kitchen_data['orders_details']->order_type == 1){
				$this->data['store_name'] = "Table : #".$kitchen_data['orders_details']->table_name;
			} elseif($kitchen_data['orders_details']->order_type == 2){
				$this->data['store_name'] = "Takeaway : #".$kitchen_data['orders_details']->reference_no;
			} else{
				$this->data['store_name'] = "Delivery : #".$kitchen_data['orders_details']->reference_no;
			}
			
			$this->data['reference_no'] = $kitchen_data['orders_details']->reference_no;
			$this->data['orders_date'] = $kitchen_data['orders_details']->date;
			$this->data['ordered_by'] = $ordered_by;
			
			$this->data['kitchens'] = $kitchen_data['kitchens'];
			
			if(!empty($kitchen_data['kitchens'])){
				
				foreach($kitchen_data['kitchens'] as $order_data){
					
					$this->data['orders'] = $this->pos_model->getorderKitchenprint($order_data->id, $kitchen_data['orders_details']->id);
					$this->data['kitchen_value'] = !empty($order_data->id) ? $order_data->id : 1;
					$this->data['reskitchen'] = $this->site->getAllResKitchen();
		
					$html = $this->load->view($this->theme . 'pos/orderkitchenprint', $this->data);
					echo  $html;
				}
			}
			
			
		}
	}
	
	public function remotePrintingKOT($kitchen_data=array()){
		if(!empty($kitchen_data)){
			$ordered_by = 'N/A';
			$user = $this->site->getUser($kitchen_data['orders_details']->created_by); 
			if($user){ 
			$ordered_by = $user->first_name.' '.$user->last_name; 
			}
			$biller = $this->site->getCompanyOrderByID($kitchen_data['orders_details']->biller_id);
			$print_header = "";
			if($kitchen_data['orders_details']->order_type == 1){
				$store_name = "Table : #".$kitchen_data['orders_details']->table_name;
			} elseif($kitchen_data['orders_details']->order_type == 2){
				$store_name = "Takeaway : #".$kitchen_data['orders_details']->reference_no;
			} else{
				$store_name = "Delivery : #".$kitchen_data['orders_details']->reference_no;
			}
			//$print_header .= $biller->company;
			//$print_header .= ', ';
			//$print_header .= $biller->address;
			$print_header .= "\n";
			$print_header .= "KOT ORDER";
			$print_header .= "\n\n";
			$print_info_common = "";
			$print_info_common .= 'Order Number';
			$print_info_common .= ' : ';
			$print_info_common .= $kitchen_data['orders_details']->reference_no;
			$print_info_common .= "\n";
			$print_info_common .= 'Date';
			$print_info_common .= ' : ';
			$print_info_common .= $kitchen_data['orders_details']->date;
			$print_info_common .= "\n";
			$print_info_common .= 'Order Person';
			$print_info_common .= ' : ';
			$print_info_common .= $ordered_by;
			$print_info_common .= "\n";
			
			
			
			if(!empty($kitchen_data['kitchens'])){
				foreach($kitchen_data['kitchens'] as $order_data){
					$print_info = ''; 
					$print_info .= $print_info_common;
					$print_info .= 'Kitchen Type';
					$print_info .= ' : ';
					$print_info .= $order_data->name;
					$print_info .= "\n-----------------------------------------------\n";
					$print_items = "";
					if(!empty($order_data->kit_o) && !empty($order_data->printers_details)){
						$i =1;
						$list = array();
						foreach($order_data->kit_o as $item_data){
							
							
							$print_items .= '';
							$print_items .= $i;
							$print_items .= ' ';
							if(!empty($item_data['khmer_recipe_image'])){
								$print_items .= $item_data['khmer_recipe_image'];
							}else{
								$print_items .= $item_data['recipe_name'];
							}
							$print_items .= "";
							$print_items .= '   X ';
							$print_items .= $item_data['quantity'];
							$print_items .= "\n";
							
							$list[] = array(
								'sno' => $i,
								'en_recipe_name' => $item_data['en_recipe_name'],
								'quantity' => $item_data['quantity'],
								'khmer_image' => $item_data['khmer_recipe_image']
							);
							$i++;
						}
						//Remote printing KOT
						$receipt = array(
							'store_name' => $store_name,
							'header' => $print_header,
							'info' => $print_info,
							'items' => $print_items,
							'itemlists' => $list
						);
						$data = array(
						'type'=>'print-receipt',
						'data'=>array(
							'printer' => $order_data->printers_details,
							'logo'=> base_url().'assets/uploads/logos/'.$biller->logo,
							'text' => $receipt,
							'cash_drawer' => ''
						)
						);
						if(!empty($this->ws->checkConnection())){//echo '<pre>';print_R($data);
						$result = $this->ws->send(json_encode($data));
						$this->ws->close();
						}
					}
				}
			}//die;
		}
	}

	public function billing(){
	    //echo "<pre>";print_r($_POST);exit;
		$order_type = !empty($_GET['order_type']) ? $_GET['order_type'] : '';
		$bill_type = !empty($_GET['bill_type']) ? $_GET['bill_type'] : '';
		$table_id = !empty($_GET['table']) ? $_GET['table'] : '';
		$split_id = !empty($_GET['splits']) ? $_GET['splits'] : '';
		$bils = !empty($_GET['bils']) ? $_GET['bils'] : '';
		
		$this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : NULL;
		
		$this->data['order_type'] = $order_type;
		$this->data['bill_type'] = $bill_type;
		$this->data['bils'] = $bils;
		$this->data['table_id'] = $table_id;
		$this->data['split_id'] = $split_id;
		$this->data['tax_rates'] = $this->site->getAllTaxRates();
		$this->data['customer_discount'] = $this->site->GetAllcostomerDiscounts();
		/*echo "<pre>";
		print_r($this->data['customer_discount']);die;*/
		
		$notification_array['customer_role'] = CUSTOMER;
		$notification_array['customer_msg'] = $this->session->userdata('username').' has been bil generator to customer';
		$notification_array['customer_type'] = 'Your bil  generator';
			
		$notification_array['from_role'] = $this->session->userdata('group_id');
		$notification_array['insert_array'] = array(
			'msg' => $this->session->userdata('username').' has been bil generator to '.$split_id,
			'type' => 'Bil generator ('.$split_id.')',
			'table_id' =>  $table_id,
			'role_id' => CASHIER,
			'user_id' => $this->session->userdata('user_id'),	
			'warehouse_id' => $this->session->userdata('warehouse_id'),
			'created_on' => date('Y-m-d H:m:s'),
			'is_read' => 0
		);
		$this->data['current_user'] = $this->pos_model->getUserByID($this->session->userdata('user_id'));
		if(!empty($table_id)){
			$item_data = $this->pos_model->getBil($table_id, $split_id, $this->session->userdata('user_id'));
		}else{
			$item_data = $this->pos_model->getBil($table_id, $split_id, $this->session->userdata('user_id'));
		}	
		foreach($item_data['items'] as $item_row){
			foreach($item_row as $item){
				$order_item_id[] = $item->id;
			}
		}	

			
		foreach($item_data['items'] as $item_row){
			foreach($item_row as $item){
				$order_item[] = $item;
			}
		}

		foreach($item_data['items'] as $orderitems){
			foreach($orderitems as $items){
			$timelog_array[] = array(
			'status' => 'Closed',
			'created_on' => date('Y-m-d H:m:s'),
			'item_id' => $items->id,
			'user_id' => $this->session->userdata('user_id'),	
			'warehouse_id' => $this->session->userdata('warehouse_id'),);
		  }
	    }	
			
		$this->data['order_item'] = $order_item;
		/*echo "<pre>";
		print_r($item_data['order']);die;*/
		foreach($item_data['order'] as $order){
			$order_data = array('sales_type_id' => $order->order_type,
				'sales_split_id' => $order->split_id,
				'sales_table_id' => $order->table_id,
				'date' => date('Y-m-d H:i:s'),
				'reference_no' => 'SALES-'.date('YmdHis'),
				'customer_id' => $order->customer_id,
				'customer' => $order->customer,
				'biller_id' => $order->biller_id,
				'biller' => $order->biller,
				'warehouse_id' => $order->warehouse_id,
				'note' => $order->note,
				'staff_note' => $order->staff_note,
				'sale_status' => 'Process',
                'hash'      => hash('sha256', microtime() . mt_rand()),
			);
			
			$notification_array['customer_id'] = $order->customer_id;
		}
		
		

		$this->data['order_data'] = $order_data;
		$postData = $this->input->post();
		$delivery_person = $this->input->post('delivery_person_id') ? $this->input->post('delivery_person_id') : 0;
		if($bill_type == 1){

			$this->form_validation->set_rules('splits', $this->lang->line("splits"), 'trim|required');
			
			if ($this->form_validation->run() == TRUE){	
				if ($this->input->post ( 'action' ) == "SINGLEBILL-SUBMIT") {
					//echo "<pre>";
					//print_r($this->input->post());die;
					if($this->input->post('bill_type'))
					{	
						for($i=1; $i<=$this->input->post('bils'); $i++){
							
						$check_discount_amount_old = $this->input->post('split['.$i.'][itemdiscounts]');
						$check_order_discount_input = $this->input->post('split['.$i.'][order_discount_input]');
						
						if(!empty($check_discount_amount_old) || !empty($check_order_discount_input)){
							$check_discount = 'YES';
						}else{
							$check_discount = '';
						}
							
						$tot_item =	$this->input->post('[split]['.$i.'][total_item]');
						$itemdis = $this->input->post('[split]['.$i.'][discount_amount]')/$tot_item;
						
										
						$billitem['bills_items'] = array();
						$bills_item['recipe_name'] = $this->input->post('split[][$i][recipe_name][$i]');					
						$splitData = array();
						foreach ($postData['split'][$i]['recipe_name'] as $key => $split) {

							$offer_dis = 0.0000;
							if($this->input->post('[split]['.$i.'][tot_dis_value]'))
							{
								$offer_dis = $this->site->calculate_Discount($this->input->post('[split]['.$i.'][tot_dis_value]'), ($postData['split'][$i]['subtotal'][$key]-$postData['split'][$i]['item_discount'][$key]),$this->input->post('[split]['.$i.'][item_dis]'));
							}
							/*314500*/
							
							if($this->input->post('[split]['.$i.'][order_discount_input]'))
							{	
								$subtotal =$postData['split'][$i]['subtotal'][$key];
								$tot_dis1 = $this->input->post('[split]['.$i.'][tot_dis1]');

								$item_dis = $postData['split'][$i]['item_dis'][$key];

								$item_discount = $postData['split'][$i]['item_discount'][$key];
if($this->Settings->customer_discount=="customer"){
    $recipe_id =  $postData['split'][$i]['recipe_id'][$key];
								/*echo $recipe_id;die;*/
								//echo $subtotal.'-'.$item_dis.'-'.$offer_dis;
								$finalAmt = $subtotal - $item_discount -$offer_dis; 
								$customer_discount_status = 'applied';
								$discountid = $this->input->post('[split]['.$i.'][order_discount_input]');
								$recipeDetails = $this->pos_model->getrecipeByID($recipe_id);
								$group_id =$recipeDetails->category_id;
								$input_dis = $this->pos_model->recipe_customer_discount_calculation($recipe_id,$group_id,$finalAmt,$discountid);
}else if($this->Settings->customer_discount=="manual"){
							   
							 $input_dis = $this->site->calculate_Discount($this->input->post('[split]['.$i.'][order_discount_input]'), (($postData['split'][$i]['subtotal'][$key]-$postData['split'][$i]['item_discount'][$key])-$offer_dis),$tot_dis1 ? $tot_dis1 : $item_dis );
}
							 
							// $input_dis = $this->input->post('[split]['.$i.'][item_input_dis]['.$key.']');
							}
							else{
								
								$input_dis = 0;
							}
							if($this->input->post('[split]['.$i.'][ptax]'))
							{
							$tax_type = $this->input->post('[split]['.$i.'][tax_type]');

							  if($tax_type != 0){

							   $itemtax = $this->site->calculateOrderTax($this->input->post('[split]['.$i.'][ptax]'), ($postData['split'][$i]['subtotal'][$key]-($offer_dis ? $offer_dis:0) -($input_dis ? $input_dis:0)-($postData['split'][$i]['item_discount'][$key])));

							   $sub_val =$postData['split'][$i]['subtotal'][$key];

							  }
							  else
							  {
							  	$default_tax = $this->site->calculateOrderTax($this->input->post('[split]['.$i.'][ptax]'), ($postData['split'][$i]['subtotal'][$key]-($offer_dis ? $offer_dis:0) -($input_dis ? $input_dis:0)-($postData['split'][$i]['item_discount'][$key])));

							  	$final_val = ($postData['split'][$i]['subtotal'][$key]-($offer_dis ? $offer_dis:0) -($input_dis ? $input_dis:0)-($postData['split'][$i]['item_discount'][$key]));

							  	$subval = $final_val/(($default_tax/$final_val)+1);

							  	$getTax = $this->site->getTaxRateByID($this->input->post('[split]['.$i.'][ptax]'));

							  	$itemtax = ($subval) * ($getTax->rate / 100);

							  	$sub_val =$postData['split'][$i]['subtotal'][$key];	
							  } 
							}else{
								$sub_val =$postData['split'][$i]['subtotal'][$key];
							}
							

							$splitData[$i][] = array(
								'recipe_name' => $split,
								'unit_price' => $postData['split'][$i]['unit_price'][$key],
								'net_unit_price' => $postData['split'][$i]['unit_price'][$key]*$postData['split'][$i]['quantity'][$key],
								'warehouse_id' => $this->session->userdata('warehouse_id'),
								'recipe_type' => $postData['split'][$i]['recipe_type'][$key],
								'quantity' => $postData['split'][$i]['quantity'][$key],
								'recipe_id' => $postData['split'][$i]['recipe_id'][$key],
								'recipe_code' => $postData['split'][$i]['recipe_code'][$key],
								'discount' => $postData['split'][$i]['item_discount_id'][$key],
								
								'item_discount' => $postData['split'][$i]['item_discount'][$key],
								'off_discount' => $offer_dis ? $offer_dis:0,
								'input_discount' => $input_dis ? $input_dis:0,
								'tax_type' => $this->input->post('[split]['.$i.'][tax_type]'), 
								'tax' => $itemtax,	
								'subtotal' => $sub_val,

								/*'subtotal' => $postData['split'][$i]['subtotal'][$key]-(($input_dis ? $input_dis:0)-($offer_dis ? $offer_dis:0)-($postData['split'][$i]['item_discount'][$key]+$itemtax)),*/
							);
						}
						if($this->input->post('[split]['.$i.'][order_discount_input]')){
						    $cus_discount_type = $this->Settings->customer_discount;
						    $cus_discount_val ='';
						    if($this->Settings->customer_discount=="customer"){
							$cus_discount_val = $this->input->post('[split]['.$i.'][order_discount_input]').'%';
						    }else if($this->Settings->customer_discount=="manual"){
							$cus_discount_val = $this->input->post('[split]['.$i.'][order_discount_input]');
						    }
						}else{
						    $cus_discount_val ='';$cus_discount_type='';
						}

						$billData[$i] = array(
									'reference_no' => $this->input->post('[split]['.$i.'][reference_no]'),
									'date' => date('Y-m-d H:i:s'),
									'customer_id' => $this->input->post('[split]['.$i.'][customer_id]'),
									'customer' => $this->input->post('[split]['.$i.'][customer]'),
									'biller' => $this->input->post('[split]['.$i.'][biller]'),
									'biller_id' => $this->input->post('[split]['.$i.'][biller_id]'),
									'total_items' => $this->input->post('[split]['.$i.'][total_item]'),
									'total' => $this->input->post('[split]['.$i.'][total_price]'),
									'total_tax' => $this->input->post('[split]['.$i.'][tax_amount]'),
									'tax_type' => $this->input->post('[split]['.$i.'][tax_type]'), 
									'tax_id' => $this->input->post('[split]['.$i.'][ptax]'),
									'total_discount' => (($this->input->post('[split]['.$i.'][itemdiscounts]'))+($this->input->post('[split]['.$i.'][offer_dis]'))+($this->input->post('[split]['.$i.'][discount_amount]'))+($this->input->post('[split]['.$i.'][off_discount]')? $this->input->post('[split]['.$i.'][off_discount]') : 0)),
									'grand_total' => $this->input->post('[split]['.$i.'][grand_total]'),
									'round_total' => $this->input->post('[split]['.$i.'][round_total]'),
									'bill_type' => $bill_type,
									'delivery_person_id' => $delivery_person,
									'order_discount_id' => $this->input->post('[split]['.$i.'][tot_dis_id]')? $this->input->post('[split]['.$i.'][tot_dis_id]') : NULL,
									'warehouse_id' => $this->session->userdata('warehouse_id'),
									'discount_type'=>$cus_discount_type,
									'discount_val'=>$cus_discount_val,
									
								);
						
						
						
						}
			/*echo "<pre>";print_r($this->input->post ());die;*/
			//echo "<pre>";
//print_r($splitData);				
//print_r($billData);	
//die;	
						$sales_total = array_column($billData, 'grand_total');
						$sales_total = array_sum($sales_total);
						
											
						 $response = $this->pos_model->InsertBill($order_data, $order_item, $billData,$splitData, $sales_total, $delivery_person,$timelog_array, $notification_array,$order_item_id);
						
						if($response == 1)
						{
							if($order_type == 1){
								admin_redirect("pos/order_table");
							}elseif($order_type == 2){
								admin_redirect("pos/order_takeaway");
							}elseif($order_type == 3){
								admin_redirect("pos/order_doordelivery");
							}							
						}									
					}
					else{

						$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
					$this->load->view($this->theme . 'pos/autosplitbil', $this->data);
					}			
				}	
			}else{
				$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
				$this->load->view($this->theme . 'pos/singlebil', $this->data);
			}
		}elseif($bill_type == 2){

			$this->form_validation->set_rules('splits', $this->lang->line("splits"), 'trim|required');
			if ($this->form_validation->run() == TRUE){
				if ($this->input->post ( 'action' ) == "AUTOSPLITBILL-SUBMIT") {	
					//echo "<pre>";
					//print_r($this->input->post());die;
					if($this->input->post('bill_type'))
					{
						$recipe_name = $this->input->post('recipe_name[]');
						$splitData = array();
						for($i=1; $i<=$this->input->post('bils'); $i++){

						$tot_item =	$this->input->post('[split]['.$i.'][total_item]');
						$itemdis = $this->input->post('[split]['.$i.'][discount_amount]')/$tot_item;

							$billitem['bills_items'] = array();
							$bills_item['recipe_name'] = $this->input->post('split[][$i][recipe_name][$i]');					
						
						foreach ($postData['split'][$i]['recipe_name'] as $key => $split) {

							$offer_dis = 0.0000;
							if($this->input->post('[split]['.$i.'][tot_dis_value]'))
							{
								$offer_dis = $this->site->calculate_Discount($this->input->post('[split]['.$i.'][tot_dis_value]'), ($postData['split'][$i]['subtotal'][$key]-$postData['split'][$i]['item_discount'][$key]),$this->input->post('[split]['.$i.'][item_dis]'));
							}
							$customer_discount_id = $this->input->post('[split]['.$i.'][order_discount_input]');
							$customer_discount_status = '';
							
							if($this->input->post('[split]['.$i.'][order_discount_input]') != 0)
							{	
								$recipe_id =  $postData['split'][$i]['recipe_id'][$key];
								/*echo $recipe_id;die;*/
								$customer_discount_status = 'applied';

								$recipeDetails = $this->pos_model->getrecipeByID($recipe_id);
								$group_id =$recipeDetails->category_id;

								$subtotal =$postData['split'][$i]['subtotal'][$key];

								$tot_dis1 = $this->input->post('[split]['.$i.'][tot_dis1]');

								$item_dis = $postData['split'][$i]['item_dis'][$key];

								$item_discount = $postData['split'][$i]['item_discount'][$key];
								$discountid = $this->input->post('[split]['.$i.'][order_discount_input]');

								$finalAmt = $subtotal - $item_discount -$offer_dis; 
								if($this->Settings->customer_discount=="customer"){
								$input_dis = $this->pos_model->recipe_customer_discount_calculation($recipe_id,$group_id,$finalAmt,$discountid);
								}else if($this->Settings->customer_discount=="manual"){
								    $input_dis = $this->site->calculate_Discount($this->input->post('[split]['.$i.'][order_discount_input]'), (($postData['split'][$i]['subtotal'][$key]-$postData['split'][$i]['item_discount'][$key])-$offer_dis),$tot_dis1 ? $tot_dis1 : $item_dis );
								}
							   /*$input_dis = $this->site->calculate_Discount($this->input->post('[split]['.$i.'][order_discount_input]'), (($postData['split'][$i]['subtotal'][$key]-$postData['split'][$i]['item_discount'][$key])-$offer_dis),$tot_dis1 ? $tot_dis1 : $item_dis );*/
							}
							else{
								$input_dis = 0;
							}							
							if($this->input->post('[split]['.$i.'][ptax]'))
							{
							 $tax_type = $this->input->post('[split]['.$i.'][tax_type]');
							 if($tax_type != 0){
							 $itemtax = $this->site->calculateOrderTax($this->input->post('[split]['.$i.'][ptax]'), ($postData['split'][$i]['subtotal'][$key]-($offer_dis ? $offer_dis:0) -($input_dis ? $input_dis:0)-($postData['split'][$i]['item_discount'][$key])));

							   $sub_val =$postData['split'][$i]['subtotal'][$key];
							 }else
							 {

							 	$default_tax = $this->site->calculateOrderTax($this->input->post('[split]['.$i.'][ptax]'), ($postData['split'][$i]['subtotal'][$key]-($offer_dis ? $offer_dis:0) -($input_dis ? $input_dis:0)-($postData['split'][$i]['item_discount'][$key])));

							  	$final_val = ($postData['split'][$i]['subtotal'][$key]-($offer_dis ? $offer_dis:0) -($input_dis ? $input_dis:0)-($postData['split'][$i]['item_discount'][$key]));

							  	$subval = $final_val/(($default_tax/$final_val)+1);

							  	$getTax = $this->site->getTaxRateByID($this->input->post('[split]['.$i.'][ptax]'));

							  	$itemtax = ($subval) * ($getTax->rate / 100);

							  	$sub_val =$postData['split'][$i]['subtotal'][$key];
							 }  	
							 
							}

							$splitData[$i][] = array(
								'recipe_name' => $split,	
								'unit_price' => $postData['split'][$i]['unit_price'][$key],
								'net_unit_price' => $postData['split'][$i]['unit_price'][$key]*$postData['split'][$i]['quantity'][$key],
								'warehouse_id' => $this->session->userdata('warehouse_id'),
								'recipe_type' => $postData['split'][$i]['recipe_type'][$key],
								'quantity' => $postData['split'][$i]['quantity'][$key],
								'recipe_id' => $postData['split'][$i]['recipe_id'][$key],
								'recipe_code' => $postData['split'][$i]['recipe_code'][$key],
								'discount' => $postData['split'][$i]['item_discount_id'][$key],

								'item_discount' => $postData['split'][$i]['item_discount'][$key],

								'off_discount' => $offer_dis ? $offer_dis:0,
								
								'input_discount' => $input_dis ? $input_dis:0,

								'tax' => $itemtax,

								'subtotal' => $sub_val,
							);
						}
						if($this->input->post('[split]['.$i.'][order_discount_input]')){
						    $cus_discount_type = $this->Settings->customer_discount;
						    $cus_discount_val ='';
						    if($this->Settings->customer_discount=="customer"){
							$cus_discount_val = $this->input->post('[split]['.$i.'][order_discount_input]').'%';
						    }else if($this->Settings->customer_discount=="manual"){
							$cus_discount_val = $this->input->post('[split]['.$i.'][order_discount_input]');
						    }
						}else{
						    $cus_discount_val ='';$cus_discount_type='';
						}
						$billData[$i] = array(
									'reference_no' => $this->input->post('[split]['.$i.'][reference_no]'),
									'date' => date('Y-m-d H:i:s'),
									'customer_id' => $this->input->post('[split]['.$i.'][customer_id]'),
									'customer' => $this->input->post('[split]['.$i.'][customer]'),
									'biller' => $this->input->post('[split]['.$i.'][biller]'),
									'biller_id' => $this->input->post('[split]['.$i.'][biller_id]'),
									'total_items' => $this->input->post('[split]['.$i.'][total_item]'),
									'total' => $this->input->post('[split]['.$i.'][total_price]'),
									'tax_type' => $this->input->post('[split]['.$i.'][tax_type]'), 
									'tax_id' => $this->input->post('[split]['.$i.'][ptax]'),
									'total_tax' => $this->input->post('[split]['.$i.'][tax_amount]'),
									'total_discount' => (($this->input->post('[split]['.$i.'][itemdiscounts]'))+($this->input->post('[split]['.$i.'][offer_dis]'))+($this->input->post('[split]['.$i.'][discount_amount]'))+($this->input->post('[split]['.$i.'][off_discount]')? $this->input->post('[split]['.$i.'][off_discount]') : 0)),
									'grand_total' => $this->input->post('[split]['.$i.'][grand_total]'),
									'round_total' => $this->input->post('[split]['.$i.'][round_total]'),
									'bill_type' => $bill_type,
									'delivery_person_id' => $delivery_person,
									'order_discount_id' => $this->input->post('[split]['.$i.'][tot_dis_id]')? $this->input->post('[split]['.$i.'][tot_dis_id]') : NULL,
									'warehouse_id' => $this->session->userdata('warehouse_id'),
									'customer_discount_id' => $customer_discount_id,
						             'customer_discount_status' => $customer_discount_status,
							     'discount_type'=>$cus_discount_type,
									'discount_val'=>$cus_discount_val,
								);
						
						}
	
						$sales_total = array_column($billData, 'grand_total');
						$sales_total = array_sum($sales_total);
/*
					
	echo "<pre>";	
	
	print_r($splitData);
print_r($billData);die;	*/			
						
						 $response = $this->pos_model->InsertBill($order_data, $order_item, $billData,$splitData, $sales_total, $delivery_person,$timelog_array,$notification_array,$order_item_id);
						
						if($response == 1)
						{
							if($order_type == 1){
								admin_redirect("pos/order_table");
							}elseif($order_type == 2){
								admin_redirect("pos/order_takeaway");
							}elseif($order_type == 3){
								admin_redirect("pos/order_doordelivery");
							}
						}
									
					  }
					else{
						$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
					$this->load->view($this->theme . 'pos/autosplitbil', $this->data);
					}
					
				}
			}else{
				$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
				$this->load->view($this->theme . 'pos/autosplitbil', $this->data);
			}

		}elseif($bill_type == 3){
			
			$this->form_validation->set_rules('splits', $this->lang->line("splits"), 'trim|required');
			if ($this->form_validation->run() == TRUE){
				
				//if ($this->input->post ( 'action' ) == "MANUALSPLITBILL-SUBMIT") {	
				//	/*echo "<pre>";
				//	print_r($this->input->post());die;*/
				//	if($this->input->post('bill_type'))
				//	{	
				//		$recipe_name = $this->input->post('recipe_name[]');
				//		$splitData = array();
				//		for($i=1; $i<=$this->input->post('bils'); $i++){
				//			$billitem['bills_items'] = array();
				//			$bills_item['recipe_name'] = $this->input->post('split[][$i][recipe_name][$i]');					
				//		
				//		foreach ($postData['split'][$i]['recipe_name'] as $key => $split) {
				//			$splitData[$i][] = array(
				//			    'recipe_name' => $split,
				//				'net_unit_price' => $postData['split'][$i]['unit_price'][$key],
				//				'warehouse_id' => $this->session->userdata('warehouse_id'),
				//				'recipe_type' => $postData['split'][$i]['recipe_type'][$key],
				//				'quantity' => $postData['split'][$i]['quantity'][$key],
				//				'recipe_id' => $postData['split'][$i]['recipe_id'][$key],
				//				'recipe_code' => $postData['split'][$i]['recipe_code'][$key],
				//				'subtotal' => $postData['split'][$i]['subtotal'][$key],
				//			);
				//		}
				//		$billData[$i] = array(
				//					'reference_no' => $this->input->post('[split]['.$i.'][reference_no]'),
				//					'date' => date('Y-m-d H:i:s'),
				//					'customer_id' => $this->input->post('[split]['.$i.'][customer_id]'),
				//					'customer' => $this->input->post('[split]['.$i.'][customer]'),
				//					'biller' => $this->input->post('[split]['.$i.'][biller]'),
				//					'biller_id' => $this->input->post('[split]['.$i.'][biller_id]'),
				//					'total_items' => $this->input->post('[split]['.$i.'][total_items]'),
				//					'total' => $this->input->post('[split]['.$i.'][total]'),
				//					'tax_type' => $this->input->post('[split]['.$i.'][tax_type]'), 
				//					'tax_id' => $this->input->post('[split]['.$i.'][ptax]'),
				//					'total_tax' => $this->input->post('[split]['.$i.'][tax_amount]'),
				//					'total_discount' => $this->input->post('[split]['.$i.'][discount_amount]'),
				//					'grand_total' => $this->input->post('[split]['.$i.'][grand_total]'),
				//					'bill_type' => $bill_type,
				//					'delivery_person_id' => $delivery_person,
				//					'order_discount_id' => $this->input->post('[split]['.$i.'][tot_dis_id]')? $this->input->post('[split]['.$i.'][tot_dis_id]') : NULL,
				//					'warehouse_id' => $this->session->userdata('warehouse_id'),
				//				);
				//		}
				//		$sales_total = array_column($billData, 'grand_total');
				//		$sales_total = array_sum($sales_total);
				//		
				//		 $response = $this->pos_model->InsertBill($order_data, $order_item, $billData,$splitData, $sales_total, $delivery_person,$timelog_array,$order_item_id);
				//		
				//		if($response == 1)
				//		{
				//			if($order_type == 1){
				//				admin_redirect("pos/order_table");
				//			}elseif($order_type == 2){
				//				admin_redirect("pos/order_takeaway");
				//			}elseif($order_type == 3){
				//				admin_redirect("pos/order_doordelivery");
				//			}
				//		}
				//					
				//	  }
				//	else{
				//		$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
				//	$this->load->view($this->theme . 'pos/autosplitbil', $this->data);
				//	}
				//	
				//}
			if ($this->input->post ( 'action' ) == "MANUALSPLITBILL-SUBMIT") {	
					//echo "<pre>";
					//print_r($this->input->post());die;
					if($this->input->post('bill_type'))
					{
						$recipe_name = $this->input->post('recipe_name[]');
						$splitData = array();
						for($i=1; $i<=$this->input->post('bils'); $i++){

						$tot_item =	$this->input->post('[split]['.$i.'][total_item]');
						$itemdis = $this->input->post('[split]['.$i.'][discount_amount]')/$tot_item;

							$billitem['bills_items'] = array();
							$bills_item['recipe_name'] = $this->input->post('split[][$i][recipe_name][$i]');					
						
						foreach ($postData['split'][$i]['recipe_name'] as $key => $split) {

							$offer_dis = 0.0000;
							if($this->input->post('[split]['.$i.'][tot_dis_value]'))
							{
								$offer_dis = $this->site->calculate_Discount($this->input->post('[split]['.$i.'][tot_dis_value]'), ($postData['split'][$i]['subtotal'][$key]-$postData['split'][$i]['item_discount'][$key]),$this->input->post('[split]['.$i.'][item_dis]'));
							}
							$customer_discount_id = $this->input->post('[split]['.$i.'][order_discount_input]');
							$customer_discount_status = '';
							
							if($this->input->post('[split]['.$i.'][order_discount_input]') != 0)
							{	
								$recipe_id =  $postData['split'][$i]['recipe_id'][$key];
								/*echo $recipe_id;die;*/
								$customer_discount_status = 'applied';

								$recipeDetails = $this->pos_model->getrecipeByID($recipe_id);
								$group_id =$recipeDetails->category_id;

								$subtotal =$postData['split'][$i]['subtotal'][$key];

								$tot_dis1 = $this->input->post('[split]['.$i.'][tot_dis1]');

								$item_dis = $postData['split'][$i]['item_dis'][$key];
								
								$item_discount = $postData['split'][$i]['item_discount'][$key];

								$discountid = $this->input->post('[split]['.$i.'][order_discount_input]');

								$finalAmt = $subtotal - $item_discount -$offer_dis; 
								if($this->Settings->customer_discount=="customer"){
								$input_dis = $this->pos_model->recipe_customer_discount_calculation($recipe_id,$group_id,$finalAmt,$discountid);
								}else if($this->Settings->customer_discount=="manual"){
								    $input_dis = $this->site->calculate_Discount($this->input->post('[split]['.$i.'][order_discount_input]'), (($postData['split'][$i]['subtotal'][$key]-$postData['split'][$i]['item_discount'][$key])-$offer_dis),$tot_dis1 ? $tot_dis1 : $item_dis );
								}
							   /*$input_dis = $this->site->calculate_Discount($this->input->post('[split]['.$i.'][order_discount_input]'), (($postData['split'][$i]['subtotal'][$key]-$postData['split'][$i]['item_discount'][$key])-$offer_dis),$tot_dis1 ? $tot_dis1 : $item_dis );*/
							}
							else{
								$input_dis = 0;
							}							
							if($this->input->post('[split]['.$i.'][ptax]'))
							{
							 $tax_type = $this->input->post('[split]['.$i.'][tax_type]');
							 if($tax_type != 0){
							 $itemtax = $this->site->calculateOrderTax($this->input->post('[split]['.$i.'][ptax]'), ($postData['split'][$i]['subtotal'][$key]-($offer_dis ? $offer_dis:0) -($input_dis ? $input_dis:0)-($postData['split'][$i]['item_discount'][$key])));

							   $sub_val =$postData['split'][$i]['subtotal'][$key];
							 }else
							 {

							 	$default_tax = $this->site->calculateOrderTax($this->input->post('[split]['.$i.'][ptax]'), ($postData['split'][$i]['subtotal'][$key]-($offer_dis ? $offer_dis:0) -($input_dis ? $input_dis:0)-($postData['split'][$i]['item_discount'][$key])));

							  	$final_val = ($postData['split'][$i]['subtotal'][$key]-($offer_dis ? $offer_dis:0) -($input_dis ? $input_dis:0)-($postData['split'][$i]['item_discount'][$key]));

							  	$subval = $final_val/(($default_tax/$final_val)+1);

							  	$getTax = $this->site->getTaxRateByID($this->input->post('[split]['.$i.'][ptax]'));

							  	$itemtax = ($subval) * ($getTax->rate / 100);

							  	$sub_val =$postData['split'][$i]['subtotal'][$key];
							 }  	
							 
							}

							$splitData[$i][] = array(
								'recipe_name' => $split,	
								'unit_price' => $postData['split'][$i]['unit_price'][$key],
								'net_unit_price' => $postData['split'][$i]['unit_price'][$key]*$postData['split'][$i]['quantity'][$key],
								'warehouse_id' => $this->session->userdata('warehouse_id'),
								'recipe_type' => $postData['split'][$i]['recipe_type'][$key],
								'quantity' => $postData['split'][$i]['quantity'][$key],
								'recipe_id' => $postData['split'][$i]['recipe_id'][$key],
								'recipe_code' => $postData['split'][$i]['recipe_code'][$key],
								'discount' => $postData['split'][$i]['item_discount_id'][$key],

								'item_discount' => $postData['split'][$i]['item_discount'][$key],

								'off_discount' => $offer_dis ? $offer_dis:0,
								
								'input_discount' => $input_dis ? $input_dis:0,

								'tax' => $itemtax,

								'subtotal' => $sub_val,
							);
						}
						if($this->input->post('[split]['.$i.'][order_discount_input]')){
						    $cus_discount_type = $this->Settings->customer_discount;
						    $cus_discount_val ='';
						    if($this->Settings->customer_discount=="customer"){
							$cus_discount_val = $this->input->post('[split]['.$i.'][order_discount_input]').'%';
						    }else if($this->Settings->customer_discount=="manual"){
							$cus_discount_val = $this->input->post('[split]['.$i.'][order_discount_input]');
						    }
						}else{
						    $cus_discount_val ='';$cus_discount_type='';
						}
						$billData[$i] = array(
									'reference_no' => $this->input->post('[split]['.$i.'][reference_no]'),
									'date' => date('Y-m-d H:i:s'),
									'customer_id' => $this->input->post('[split]['.$i.'][customer_id]'),
									'customer' => $this->input->post('[split]['.$i.'][customer]'),
									'biller' => $this->input->post('[split]['.$i.'][biller]'),
									'biller_id' => $this->input->post('[split]['.$i.'][biller_id]'),
									'total_items' => $this->input->post('[split]['.$i.'][total_item]'),
									'total' => $this->input->post('[split]['.$i.'][total_price]'),
									'tax_type' => $this->input->post('[split]['.$i.'][tax_type]'), 
									'tax_id' => $this->input->post('[split]['.$i.'][ptax]'),
									'total_tax' => $this->input->post('[split]['.$i.'][tax_amount]'),
									'total_discount' => (($this->input->post('[split]['.$i.'][itemdiscounts]'))+($this->input->post('[split]['.$i.'][offer_dis]'))+($this->input->post('[split]['.$i.'][discount_amount]'))+($this->input->post('[split]['.$i.'][off_discount]')? $this->input->post('[split]['.$i.'][off_discount]') : 0)),
									'grand_total' => $this->input->post('[split]['.$i.'][grand_total]'),
									'round_total' => $this->input->post('[split]['.$i.'][round_total]'),
									'bill_type' => $bill_type,
									'delivery_person_id' => $delivery_person,
									'order_discount_id' => $this->input->post('[split]['.$i.'][tot_dis_id]')? $this->input->post('[split]['.$i.'][tot_dis_id]') : NULL,
									'warehouse_id' => $this->session->userdata('warehouse_id'),
									'customer_discount_id' => $customer_discount_id,
						             'customer_discount_status' => $customer_discount_status,
							     'discount_type'=>$cus_discount_type,
									'discount_val'=>$cus_discount_val,
								);
						
						}
	
						$sales_total = array_column($billData, 'grand_total');
						$sales_total = array_sum($sales_total);
/*
					
	echo "<pre>";	
	
	print_r($splitData);
print_r($billData);die;	*/			
						
						 $response = $this->pos_model->InsertBill($order_data, $order_item, $billData,$splitData, $sales_total, $delivery_person,$timelog_array,$notification_array,$order_item_id);
						
						if($response == 1)
						{
							if($order_type == 1){
								admin_redirect("pos/order_table");
							}elseif($order_type == 2){
								admin_redirect("pos/order_takeaway");
							}elseif($order_type == 3){
								admin_redirect("pos/order_doordelivery");
							}
						}
									
					  }
					else{
						$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
					$this->load->view($this->theme . 'pos/manualsplitbil', $this->data);
					}
					
				}
				
			
			}else{
				$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
				$this->load->view($this->theme . 'pos/manualsplitbil', $this->data);
			}
		}
	
	}
	
	public function reports(){

		$reports_type = $this->input->get('type');
		$start = $this->input->get('fromdate'); 
		$end = $this->input->get('todate');

		if(isset($start) == true){			
			$start = date("Y-m-d", strtotime($start));
		}
		else
		{	
			$start = date('Y-m-d');
		}
		if(isset($end) == true){
		   $end = date( 'Y-m-d', strtotime($end ));			
		}
		else
		{
			$end = date('Y-m-d');			
		}		
		$dates = array(
		            'fromdate' => $start,
					'todate' => $end
		        );		
		$type = !empty($this->input->get('type')) ? $this->input->get('type') : '';		
		$date = date('Y-m-d');
		$this->data['warehouses'] = $this->site->getAllWarehouses();
		$this->data['sales_types'] = $this->site->getAllSalestype();
		$this->data['billers'] = $this->site->getAllCompanies('biller');

		if($reports_type == 1){
			/*$this->data['recipes'] = $this->pos_model->getItemSaleReports($start,$end);*/
			$this->data['recipes'] = $this->pos_model->getItemSaleReports($start,$end);
			$this->data['round'] = $this->pos_model->getRoundamount($start,$end);
			/*echo "<pre>";
			print_r($this->data['recipes']);die;*/
			$this->load->view($this->theme . 'pos/item_reports', $this->data);

		}elseif($reports_type == 2){

			$vale = $this->settings->default_currency;
			
			$this->data['row'] = $this->pos_model->getdaysummary($start,$end);

			$this->data['collection'] = $this->pos_model->getCollection($start,$end);
			$this->load->view($this->theme . 'pos/day_reports', $this->data);

		}elseif($reports_type == 3){

			$this->data['cashier'] = $this->pos_model->getCashierReport($start,$end);
			/*$this->data['collection'] = $this->pos_model->getCollection($start,$end);*/
			$this->data['dates'] = $dates;
			$this->load->view($this->theme . 'pos/cashier_reports', $this->data);

		}elseif($reports_type == 4){

			$this->data['settlement'] = $this->pos_model->getSettlementReport($start,$end);
			/*$this->data['collection'] = $this->pos_model->getCollection($start,$end);*/
			$this->data['dates'] = $dates;
			$this->load->view($this->theme . 'pos/settlement_reports', $this->data);

		}else{

			$this->data['recipes'] = $this->pos_model->getItemSaleReports($start,$end);
			$this->load->view($this->theme . 'pos/item_reports', $this->data);
			
		}
	}
	
	public function paymant(){
		
		$this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : NULL;

		$this->data['tax_rates'] = $this->site->getAllTaxRates();
		$currency = $this->site->getAllCurrencies();
		
		$postData = $this->input->post();	//print_R($postData);exit;
			if ($this->input->post ( 'action' ) == "PAYMENT-SUBMIT") {
				/*echo "<pre>";
				print_r($this->input->post ( ));die;*/
				$balance = $this->input->post('balance_amount'); 
				$dueamount = $this->input->post('due_amount'); 
				
				$total_pay = $this->input->post('total') + $this->input->post('balance_amount');
				$total = $this->input->post('total'); 
				$order_split_id = $this->input->post('order_split_id'); 

				$paid = !empty($dueamount) ? ($total - $dueamount) : $total;

				$p = isset($_POST['paid_by']) ? sizeof($_POST['paid_by']) : 0;

				$default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
				
				/*foreach($currency as $currency_row){
					
					if($default_currency_data->code == $currency_row->code){
						
						$p = isset($_POST['paid_by'.$currency_row->code.'']) ? sizeof($_POST['paid_by'.$currency_row->code.'']) : 0;
						
						$amount_.$currency_row->code = array_sum($_POST['amount_'.$currency_row->code.'']);
						
					}else{
						
						
						$amount_.$currency_row->code = array_sum($_POST['amount_'.$currency_row->code]);
					}					
				}*/
				/*print_r($amount_);					
				echo "string";die;*/
				//$amount_USD = array_sum($_POST['amount_USD']);
				for ($r = 0; $r < $p; $r++) {
				foreach($currency as $currency_row){
					
					if($default_currency_data->code == $currency_row->code){
						$multi_currency[] = array(
						
							'sale_id' => $this->input->post('sales_id'),
							'bil_id' => $this->input->post('bill_id'),
							'currency_id' => $currency_row->id,
							'currency_rate' => $currency_row->rate,
							'amount' => $_POST['amount_'.$currency_row->code][$r],
						);
							
						
					}else{
						$multi_currency[] = array(
						
							'sale_id' => $this->input->post('sales_id'),
							'bil_id' => $this->input->post('bill_id'),
							'currency_id' => $currency_row->id,
							'currency_rate' => $currency_row->rate,
							'amount' => $_POST['amount_'.$currency_row->code][$r],
						);
					}
				}
				}
				
				
				
                for ($r = 0; $r < $p; $r++) {
				
					foreach($currency as $currency_row){
						
												
						if($currency_row->rate == $default_currency_data->rate){
						
							$p = isset($_POST['amount_'.$currency_row->code][$r]) ? sizeof($_POST['amount_'.$currency_row->code]) : 0;
							/*$amount_.$currency_row->code = array_sum($_POST['amount_'.$currency_row->code]);*/
							$amount = $_POST['amount_'.$currency_row->code][$r];
							
						}else{
							
							/*$amount_.$currency_row->code = array_sum($_POST['amount_'.$currency_row->code]);*/
							$amount_exchange = $_POST['amount_'.$currency_row->code][$r];
							
						}
					}
				
				$payment[] = array(
					'date'         => date('Y-m-d H:i:s'),
					'sale_id'      => $_POST['bill_id'],
					'bill_id'      => $_POST['bill_id'],
					//'reference_no' => $this->input->post('reference_no'),
					'amount'       => $amount ? $amount:0,
					'amount_exchange'   => $amount_exchange ? $amount_exchange:0,
					'pos_paid'     => $_POST['amount_USD'][$r],
					'pos_balance'  => round($balance, 3),
					 'paid_by'     => $_POST['paid_by'][$r],
					 'cheque_no'   => $_POST['cheque_no'][$r],
					 'cc_no'       => $_POST['cc_no'][$r],
					 'cc_holder'   => $_POST['cc_holer'][$r],
					 'cc_month'    => $_POST['cc_month'][$r],
					 'cc_year'     => $_POST['cc_year'][$r],
					 'cc_type'     => $_POST['cc_type'][$r],
					 // 'cc_cvv2'   => $this->input->post('cc_cvv2'),
					 'sale_note'   => $_POST['sale_note'],
					 'staff_note'   => $_POST['staffnote'],
					 'payment_note' => $_POST['payment_note'][$r],
					 'created_by'   => $this->session->userdata('user_id'),
					 'type'         => 'received',
				);
				
			}
			
            
				$billid  = $this->input->post('bill_id');
				$salesid = $this->input->post('sales_id');
				
				$update_bill = array(
                'updated_at'            => date('Y-m-d H:i:s'),
				'created_by' 			=> $this->session->userdata('user_id'),
				'total_pay'				=> $total_pay,
				'balance' 				=> $balance,
                'paid'                  => $paid,
                'payment_status'        => 'Completed',
				'default_currency_code' => $default_currency_data->code,
				'default_currency_rate' => $default_currency_data->rate,
                );

                $sales_bill = array(
                'grand_total'           => $total,				
                'paid'                  => $paid,
                'payment_status'		=>'Paid',
				'default_currency_code' => $default_currency_data->code,
				'default_currency_rate' => $default_currency_data->rate,
                );
				
				
				$notification_array['from_role'] = $this->session->userdata('group_id');
				$notification_array['insert_array'] = array(			
					'user_id' => $this->session->userdata('user_id'),	
					'warehouse_id' => $this->session->userdata('warehouse_id'),
					'created_on' => date('Y-m-d H:m:s'),
					'is_read' => 0
				);
				
				$q = $this->db->select('*')->where('bill_id', $billid )->get('payments');
				if ($q->num_rows() > 0) {
					$response = 1;
				}else
				{
				    $updateCreditLimit['company_id'] = $postData['company_id'];
				    $updateCreditLimit['customer_type'] = $postData['customer_type'];
                  $response = $this->pos_model->Payment($update_bill,$billid,$payment,$multi_currency,$salesid,$sales_bill, $order_split_id, $notification_array,$updateCreditLimit);
				}				
                
				 if($response == 1)
					{	
						$this->data['order_item'] = $this->pos_model->getAllBillitems($billid);
						$this->data['message'] = $this->session->flashdata('message');
						
						$inv = $this->pos_model->getInvoiceByID($billid);
						$tableno = $this->pos_model->getTableNumber($billid);
						
						$this->load->helper('pos');						
						if (!$this->session->userdata('view_right')) {
				            $this->sma->view_rights($inv->created_by, true);
				        }        
						/*$this->data['rows'] = $this->pos_model->getAllInvoiceItems($billid);*/
						$this->data['billi_tems'] = $this->pos_model->getAllBillitems($billid);
						
						$biller_id = $inv->biller_id;
						$bill_id = $inv->sales_id;
						
       				    $customer_id = $inv->customer_id;
						$delivery_person_id = $inv->delivery_person_id;
						
						$this->data['inv'] = $inv;
						$this->data['tableno'] = $tableno;
						$this->data['customer'] = $this->pos_model->getCompanyByID($customer_id);
						
						if($delivery_person_id != 0){
							$this->data['delivery_person'] = $this->pos_model->getUserByID($delivery_person_id);
						}
						$this->data['created_by'] = $this->site->getUser($inv->created_by);
						$this->data['printer'] = $this->pos_model->getPrinterByID($this->pos_settings->printer);
						$this->data['biller'] = $this->pos_model->getCompanyByID($biller_id);
						
						$this->data['payments'] = $this->pos_model->getInvoicePayments($this->input->post('bill_id'));
						/*echo "<pre>";
						var_du($this->data['payments']);die;*/
						$this->data['return_sale'] = $inv->return_id ? $this->pos_model->getInvoiceByID($inv->return_id) : NULL;
						$this->data['return_rows'] = $inv->return_id ? $this->pos_model->getAllInvoiceItems($inv->return_id) : NULL;
                        $this->data['return_payments'] = $this->data['return_sale'] ? $this->pos_model->getInvoicePayments($this->data['return_sale']->id) : NULL;
                        $this->data['type'] = $this->input->post('type'); 
/*echo "<pre>";
print_r($inv);die;*/

					if (!empty($inv)) 
					   {						
						 $this->load->view($this->theme . 'pos/view_bill', $this->data);
					   }
					   else
					   {
					   	 admin_redirect("pos/order_biller");
					   }
					}
			}
			 else
			     {
				   	admin_redirect("pos/order_biller");
				 }
			
	}	
	
	public function order_table(){
		
		$this->sma->checkPermissions('index');
		$user = $this->site->getUser();
		$this->data['warehouses'] = NULL;
		$this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
		$this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : NULL;
	  	$this->data['tableid'] = !empty($this->input->get('table')) ? $this->input->get('table') : '';
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->load->view($this->theme . 'pos/ordertable', $this->data);
	}
	
	public function ajaxorder_table(){
		
		$table_id = !empty($this->input->get('table')) ? $this->input->get('table') : '';
		$this->data['tables'] = $this->pos_model->getAllTablesorder($table_id);
		$this->data['avil_tables'] = $this->site->getAvilAbleTables($table_id);
		$this->data['avil_customers'] = $this->site->getAvilAbleCustomers();
		$this->load->view($this->theme . 'pos/ordertable_ajax', $this->data);
		
	}
	
	
	
	public function tablecheck($order_type = NULL, $table_id = NULL){
		$order_type = $this->input->get('order_type');
        $table_id = $this->input->get('table_id');
		$table = $this->pos_model->checkTables($table_id,$order_type);
        $this->sma->send_json($table);
	}
	public function ajaxBildata($table_id = NULL, $split_id = NULL)
	{
        $table_id = $this->input->get('table_id');
		$split_id = $this->input->get('split_id');
		$bil = $this->pos_model->getBil($table_id, $split_id, $this->session->userdata('user_id'));
		
		foreach($bil['items'] as $bil_item){
			foreach($bil_item as $item){
				$item_data[] = $item;
				$total_subtotal[] = $item->subtotal;
			}
		}
		
		$total_items = count($item_data);
		foreach($bil['order'] as $bil_order){
			$order_data = array('sales_type_id' => $bil_order->order_type,
				'sales_split_id' => $bil_order->split_id,
				'sales_table_id' => $bil_order->table_id,
				'date' => date('Y-m-d H:i:s'),
				'reference_no' => 'SALES-'.date('YmdHis'),
				'customer_id' => $bil_order->customer_id,
				'customer' => $bil_order->customer,
				'biller_id' => $bil_order->biller_id,
				'biller' => $bil_order->biller,
				'warehouse_id' => $bil_order->warehouse_id,
				'note' => $bil_order->note,
				'staff_note' => $bil_order->staff_note,
				'total' => array_sum($total_subtotal),
				'sale_status' => 'Process',
				'total_items' => $total_items,
				'grand_total' => array_sum($total_subtotal),
                'hash'      => hash('sha256', microtime() . mt_rand()),
			);
		}
		$sales = $this->pos_model->updateNewSales($order_data, $item_data);		
        $this->sma->send_json(array('status' => $sales));	
	
	}
	
	public function ajaxOrderitemdata($order_id = NULL, $table_id = NULL, $split_id = NULL)
	{
		$order_id = $this->input->get('order_id');
        $table_id = $this->input->get('table_id');
		$split_id = $this->input->get('split_id');
        
		$order_item = $this->pos_model->getOrderitemlist($order_id, $table_id, $split_id, $this->session->userdata('user_id'));
       
	  	$item = '';
        if (!empty($order_item)) {
           $html = '<div id="recipe-list" class="dragscroll" style="height: 0px; min-height: 278px;">
					<table class="table items table-striped table-fixed table-bordered table-condensed table-hover sortable_table" id="posTable" style="margin-bottom: 0;">
						<thead>
						<tr>
							<th width="40%">recipe</th>
							<th width="15%">Price</th>
							<th width="15%">Qty</th>
							<th width="20%">Subtotal</th>
							
						</tr>
						</thead>';
						
					$html .='<tbody class="ui-sortable">';
					foreach($order_item as $item){
						$html .='<tr><td>'.$item->recipe_name.'</td><td>'.$item->unit_price.'</td><td>'.$item->quantity.'</td><td>'.$item->subtotal.'</td></tr>';
					}
					$html .='</tbody>';
					
					$html .='</table>
					<div style="clear:both;"></div>
				</div>';
			$item = $html;
        }
        $this->sma->send_json(array('order_item' => $item));
	}
	
	public function order_biller($split_id =NULL, $bill_type =NULL, $sid = NULL){

		$split_id = $this->input->get('split_id');
		$bill_type =$this->input->get('bill_type');
		$sales_type_id = $this->input->get('type');
		$this->data['type'] = $this->input->get('type');
		if($sales_type_id == 1){
			$this->data['sales_type'] = 'Dine In';
		}elseif($sales_type_id == 2){
			$this->data['sales_type'] = 'Take Away';
		}elseif($sales_type_id == 3){
			$this->data['sales_type'] = 'Door Delivery';
		}
		
		$this->data['warehouses'] = $this->site->getAllWarehouses();
		$this->data['sales_types'] = $this->site->getAllSalestype();
		$this->data['billers'] = $this->site->getAllCompanies('biller');
		/*$this->data['get_order_type'] = $order;*/
		$this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
		$this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : NULL;
		$this->data['sid'] = $this->input->get('suspend_id') ? $this->input->get('suspend_id') : $sid;

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
		$order_printers = json_decode($this->pos_settings->order_printers);
		$printers = array();
		if (!empty($order_printers)) {
			foreach ($order_printers as $printer_id) {
				$printers[] = $this->pos_model->getPrinterByID($printer_id);
			}
		}
		$this->data['order_printers'] = $printers;
        if($sales_type_id)
        {  
        	$this->data['sales'] = $this->pos_model->getAllSalesWithbiller($sales_type_id);
        	/*echo "<pre>";
        	print_r($this->data['sales']);die;*/
        }
	    $this->load->view($this->theme . 'pos/orderbiller', $this->data);
	}
	
	public function ajaxorder_billing(){
		$sales_type_id = !empty($this->input->get('type')) ? $this->input->get('type') : '';
		if($sales_type_id == 1){
			$this->data['sales_type'] = 'Dine In';
		}elseif($sales_type_id == 2){
			$this->data['sales_type'] = 'Take Away';
		}elseif($sales_type_id == 3){
			$this->data['sales_type'] = 'Door Delivery';
		}
		
		 if($sales_type_id)
		{  
			$this->data['sales'] = $this->pos_model->getAllSalesWithbiller($sales_type_id);
		}
	    $this->load->view($this->theme . 'pos/orderbiller_ajax', $this->data);
	}
	public function reprinter(){

	/*	$split_id = $this->input->get('split_id');
		$bill_type =$this->input->get('bill_type');
		$sales_type_id = $this->input->get('type');
		$this->data['type'] = $this->input->get('type');
		if($sales_type_id == 1){
			$this->data['sales_type'] = 'Dine In';
		}elseif($sales_type_id == 2){
			$this->data['sales_type'] = 'Take Away';
		}elseif($sales_type_id == 3){
			$this->data['sales_type'] = 'Door Delivery';
		}*/
		
		$this->data['warehouses'] = $this->site->getAllWarehouses();
		$this->data['sales_types'] = $this->site->getAllSalestype();
		$this->data['billers'] = $this->site->getAllCompanies('biller');
		
		$this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
		$this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : NULL;
		$this->data['sid'] = $this->input->get('suspend_id') ? $this->input->get('suspend_id') : $sid;

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
		
        /*if($sales_type_id)
        {  */
        	$this->data['sales'] = $this->pos_model->getAllBillingDatas();
        	/*echo "<pre>";
        	print_r($this->data['sales']);die;*/
        /*}*/
	    $this->load->view($this->theme . 'pos/bill_reprint', $this->data);
	}	
	public function ajaxBilleritemdata($order_id = NULL, $table_id = NULL, $split_id = NULL)
    {
        $order_id = $this->input->get('order_id');
        $table_id = $this->input->get('table_id');
        $split_id = $this->input->get('split_id');
        
        $order_item = $this->pos_model->getBilleritemlist($order_id, $table_id, $split_id, $this->session->userdata('user_id'));
       
        $item = '';
        if (!empty($order_item)) {
           $html = '<div id="recipe-list" class="dragscroll" style="height: 0px; min-height: 278px;">
                    <table class="table items table-striped table-fixed table-bordered table-condensed table-hover sortable_table" id="posTable" style="margin-bottom: 0;">
                        <thead>
                        <tr>
                            <th width="40%">recipe</th>
                            <th width="15%">Price</th>
                            <th width="15%">Qty</th>
                            <th width="20%">Subtotal</th>
                            
                        </tr>
                        </thead>';
                        
                    $html .='<tbody class="ui-sortable">';
                    foreach($order_item as $item){
                        $html .='<tr><td>'.$item->recipe_name.'</td><td>'.$item->unit_price.'</td><td>'.$item->quantity.'</td><td>'.$item->subtotal.'</td></tr>';
                    }
                    $html .='</tbody>';
                    
                    $html .='</table>
                    <div style="clear:both;"></div>
                </div>';
            $item = $html;
        }
        $this->sma->send_json(array('order_item' => $item));
    }

    public function ajaxBillCashierPrintdata($split_id = NULL)
    {
        $split_id = $this->input->get('split_id');
        
        $order_item = $this->pos_model->getBillCashierPrintdata($split_id);

        $item = '';
        if (!empty($order_item)) {
           $html = '<div id="recipe-list" class="dragscroll" style="height: 0px; min-height: 278px;">
                    <table class="table items table-striped table-fixed table-bordered table-condensed table-hover sortable_table" id="posTable" style="margin-bottom: 0;">
                        <thead>
                        <tr>
                            <th width="40%">Recipe</th>
                            <th width="15%">Price</th>
                            <th width="15%">Qty</th>
                            <th width="15%">Sub Total</th>
                        </tr>
                        </thead>';
                        
                    $html .='<tbody class="ui-sortable">';
                    foreach($order_item as $item){
                        $html .='<tr><td>'.$item->recipe_name.'</td><td>'.$item->quantity.'</td><td>'.$item->unit_price.'</td><td class="text-right">'.$item->subtotal.'</td></tr>';
                    }
                     /*$html .='<tr><td colspan="4" class="text-right">Shipping</td><td class="text-right">'.$item->shipping.'</td></tr>';
                     $html .='<tr><td colspan="4" class="text-right">Grand Total</td><td class="text-right">'.$item->grand_total.'</td></tr>';*/

                     $html .='<tr>
                                        <td class="left_td text-right" colspan="2" style="padding: 5px 10px;">'.lang("order_tax").'
                                            <a href="#" id="pptax2">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        </td>
                                        
                                        <td class="center_td">:</td>
                                    
                                        <td class="right_td text-right" style="padding: 5px 10px;font-size: 14px; font-weight:bold;">
                                            <span id="ttax2">0.00</span>
                                        </td>
                     </tr>
                     <tr>
                                    <td class="left_td text-right" colspan="2" style="padding: 5px 10px;">'.lang("discount").'
                                          
                                            <a href="#" id="ppdiscount">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            
                                    </td>
                                        
                                    <td class="center_td">:</td>
                                     
                                        <td class="text-right" style="padding: 5px 10px;font-weight:bold;" >
                                            <span id="tds">0.00</span>
                                        </td>
                    </tr> <tr style="border-top: 1px solid #e2e2e2;">
                        <td class="left_td text-right" colspan="2" style="padding: 5px 10px; border-top: 1px solid #666; border-bottom: 1px solid #333; font-weight:bold; background:#fff; color:#4b2d0a;">
                            '.lang("total_payable").'
                            <a href="#" id="pshipping">
                                <i class="fa fa-plus-square"></i>
                            </a>
                            <span id="tship"></span>
                        </td>

                        <td class="center_td">:</td>

                        <td class="right_td text-right" style="padding:5px 10px 5px 10px; font-size: 14px; border-bottom: 1px solid #333; font-weight:bold; background:#fff; color:#a76821;" >
                            <span id="gtotal">0.00</span>
                        </td>
                </tr>';


                    $html .='</tbody>';
                    
                    $html .='</table>
                    <div style="clear:both;"></div>
                </div>';
            $item = $html;
        }
        $this->sma->send_json(array('order_item' => $item));
    }
	
	
	public function order_takeaway(){
		$this->sma->checkPermissions('index');

		$user = $this->site->getUser();
		$this->data['warehouses'] = NULL;
		$this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
		$this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : NULL;
		
		
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

		
        $this->load->view($this->theme . 'pos/ordertakeaway', $this->data);
	}
	
	public function ajaxorder_takeaway(){
		$this->data['takeaway'] = $this->pos_model->getAllTakeawayorder();
		$this->load->view($this->theme . 'pos/ordertakeaway_ajax', $this->data);
	}
	
	public function order_doordelivery(){
		$this->sma->checkPermissions('index');

		$user = $this->site->getUser();
		$this->data['warehouses'] = NULL;
		$this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
		$this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : NULL;
		
		
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

		
        $this->load->view($this->theme . 'pos/orderdoordelivery', $this->data);
	}
	
	public function ajaxorder_doordelivery(){
		$this->data['doordelivery'] = $this->pos_model->getAllDoordeliveryorder();
		$this->load->view($this->theme . 'pos/orderdoordelivery_ajax', $this->data);
	}
	
	public function order_kitchen(){
		$this->sma->checkPermissions('index');

		$user = $this->site->getUser();
		
		$type = $this->input->get('type', TRUE);
		
		$this->data['kitchen_type'] = $type ? $type : 1;		
		$this->data['warehouses'] = NULL;
		$this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
		$this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : NULL;
		      	
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

		
        $this->load->view($this->theme . 'pos/orderkitchen', $this->data);
	}
	
	public function  ajaxorder_kitchen(){
		
		$kitchen_type = !empty($this->input->get('kitchen_type')) ? $this->input->get('kitchen_type') : 1;
		
		$this->data['orders'] = $this->pos_model->getAllTablesWithKitchen($kitchen_type);
		/*echo "<pre>";
		print_r($this->data['orders']);die;*/
		$this->data['kitchen_value'] = !empty($this->input->get('kitchen_type')) ? $this->input->get('kitchen_type') : 1;
		$this->data['reskitchen'] = $this->site->getAllResKitchen();
		
		$this->load->view($this->theme . 'pos/orderkitchen_ajax', $this->data);
	}
	
	public function update_order_statusfrom_kitchen($status = NULL, $order_id = NULL, $order_item_id = NULL, $order_type = NULL)
    {   
         $status = $this->input->get('status');
         $order_item_id = $this->input->get('order_item_id'); 
		 $order_id = $this->input->get('order_id');
		 $order_type = $this->input->get('order_type');

         if($status == 'Inprocess'){
            $current_status = 'Preparing';
         }elseif($status == 'Preparing' && $order_type == 1){
            $current_status = 'Ready';
         }elseif($status == 'Preparing' && ($order_type == 2 || $order_type == 3)){
			 $current_status = 'Closed';
		}else{
            $current_status = 'Inprocess';
         }
		 
		 $customer_id = $this->site->getOrderCustomer($order_id);
 
		 $notification_array['customer_role'] = CUSTOMER;
		 $notification_array['customer_msg'] =  'The item has been '.$current_status.' to chef';
		 $notification_array['customer_type'] = 'Chef '.$current_status.' Status';
		 $notification_array['customer_id'] = $customer_id;
         
		 $timelog_array = array(
			'status' => $current_status,
			'created_on' => date('Y-m-d H:m:s'),
			'user_id' => $this->session->userdata('user_id'),	
			'warehouse_id' => $this->session->userdata('warehouse_id'),
		);

				
		$notification_array['from_role'] = $this->session->userdata('group_id');
		$notification_array['insert_array'] = array(
			'type' => 'Chef '.$current_status.' Status',
			'table_id' =>  0,
			'user_id' => $this->session->userdata('user_id'),	
			'role_id' => WAITER,
			'warehouse_id' => $this->session->userdata('warehouse_id'),
			'created_on' => date('Y-m-d H:m:s'),
			'is_read' => 0
		);
		
		$notification_array['customer_role'] = CUSTOMER;
		$notification_array['customer_type'] = 'Chef '.$current_status.' Status';
		
         $result = $this->pos_model->updateKitchenstatus($notification_array, $status, $order_id, $order_item_id, $current_status, $this->session->userdata('user_id'),$timelog_array);
		
		 if($current_status == 'Closed'){
			$orders = $this->pos_model->getTableOrderCount($order_id); 
		 }
        if($result == true){
           $msg = 'success';           
        }else{
            $msg = 'error';
           
        }
         $this->sma->send_json(array('status' => $msg));
        
    }

   public function update_order_item_status($status = NULL, $order_item_id = NULL, $split_id = NULL)
    {
       
        
         $status = $this->input->get('status');
         $order_item_id = $this->input->get('order_item_id'); 
         $split_id = $this->input->get('split_id'); 
         
         if($status == 'Ready'){
            $current_status = 'Served';
         }elseif($status == 'Served'){
            $current_status = 'Closed';         
         }else{
            $current_status = 'Ready';
         }
		
		$item_id = explode(',', $order_item_id);
		
		$customer_id = $this->site->getOrderItemCustomer($item_id[0]);
 
		 $notification_array['customer_role'] = CUSTOMER;
		 $notification_array['customer_msg'] =  'The item has been '.$current_status.' to waiter';
		 $notification_array['customer_type'] = 'Waiter '.$current_status.' Status';
		 $notification_array['customer_id'] = $customer_id;
		 
		$notification_array['from_role'] = $this->session->userdata('group_id');
		$notification_array['insert_array'] = array(
			'type' => 'Waiter '.$current_status.' Status',
			'user_id' => $this->session->userdata('user_id'),	
			'role_id' => KITCHEN,
			'warehouse_id' => $this->session->userdata('warehouse_id'),
			'created_on' => date('Y-m-d H:m:s'),
			'is_read' => 0
		);
		
		$notification_array['customer_role'] = CUSTOMER;
		$notification_array['customer_type'] = 'Waiter '.$current_status.' Status';
		
		$timelog_array = array(
			'status' => $current_status,
			'created_on' => date('Y-m-d H:m:s'),
			'user_id' => $this->session->userdata('user_id'),	
			'warehouse_id' => $this->session->userdata('warehouse_id'),
		);

		
         $result = $this->pos_model->updateOrderstatus($status, $order_item_id, $current_status, $this->session->userdata('user_id'), $notification_array,$timelog_array);
         $split = $this->pos_model->getTableSplitCount($split_id);
        if($result == true){
           $msg = 'success'; 
           $status = $current_status; 

        }else{
            $msg = 'error';
            $status = 'error'; 
        }
        $this->sma->send_json(array('msg' => $msg, 'status' => $status));
         /*$this->sma->send_json(array('status' => $msg));*/
        
    }
   public function cancel_order_items($cancel_remarks = NULL, $order_item_id = NULL, $split_id = NULL)
    {        
         $cancel_remarks = $this->input->get('cancel_remarks');
         $order_item_id = $this->input->get('order_item_id');   
		 $split_id = $this->input->get('split_id');
		 $cancelQty = $this->input->get('cancelqty');//if 0    cancel all qty of tis item
		 
		 $item_data = $this->site->getOrderItem($order_item_id);
		 $customer_id = $this->site->getOrderItemCustomer($order_item_id);
		 if(!empty($split_id)){
			 $notification_msg = 'The item has been cancel to waiter';
			 $type = 'Waiter Cancel';
			 $notification_customer = 'The '.$item_data->recipe_name.' has been cancel to waiter';
		 }else{
			 $type = 'Chef Cancel';
			 $notification_msg = 'The item has been cancel to chef';
			 $notification_customer = 'The '.$item_data->recipe_name.' has been cancel to chef';
		 }
		
		$notification_array['from_role'] = $this->session->userdata('group_id');
		
		$notification_array['customer_role'] = $this->session->userdata('group_id');
		$notification_array['customer_msg'] = $notification_customer;
		$notification_array['customer_type'] = $type;
		$notification_array['customer_id'] = $customer_id;
		
		$notification_array['insert_array'] = array(
			'msg' => $notification_msg,
			'type' => $type,
			'table_id' =>  0,
			'user_id' => $this->session->userdata('user_id'),	
			'warehouse_id' => $this->session->userdata('warehouse_id'),
			'created_on' => date('Y-m-d H:m:s'),
			'is_read' => 0
		);

		$timelog_array = array(
			'status' => 'Cancel',
			'created_on' => date('Y-m-d H:m:s'),
			'item_id' => $order_item_id,
			'user_id' => $this->session->userdata('user_id'),	
			'warehouse_id' => $this->session->userdata('warehouse_id'),
		);

         $result = $this->pos_model->CancelOrdersItem($notification_array, $cancel_remarks, $order_item_id, $this->session->userdata('user_id'), $split_id,$timelog_array,$cancelQty);
         
        if($result == true){
           $msg = 'success'; 

        }else{
            $msg = 'error';
            $status = 'error'; 
        }
        $this->sma->send_json(array('msg' => $msg, 'status' => $msg));
         /*$this->sma->send_json(array('status' => $msg));*/
        
    }

 public function cancel_sale($cancel_remarks = NULL, $sale_id = NULL)
    {        
         $cancel_remarks = $this->input->get('cancel_remarks');
         $sale_id = $this->input->get('sale_id');   
		 
		$notification_array['from_role'] = $this->session->userdata('group_id');
		$notification_array['insert_array'] = array(			
			'user_id' => $this->session->userdata('user_id'),	
			'warehouse_id' => $this->session->userdata('warehouse_id'),
			'created_on' => date('Y-m-d H:m:s'),
			'is_read' => 0
		);

         $result = $this->pos_model->CancelSale($cancel_remarks, $sale_id, $this->session->userdata('user_id'), $notification_array);
        if($result == true){
           $msg = 'success'; 

        }else{
            $msg = 'error';
            $status = 'error'; 
        }
        $this->sma->send_json(array('msg' => $msg, 'status' => $msg));
    }
	public function order_item_kitchen(){
		$this->sma->checkPermissions('index');

		$user = $this->site->getUser();
		$this->data['warehouses'] = NULL;
		$this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
		$this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : NULL;
		//$this->data['tables'] = $this->pos_model->getAllTablesorder();
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        
		

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('pos'), 'page' => lang('pos')), array('link' => '#', 'page' => lang('pos_sales')));
        $meta = array('page_title' => lang('pos_sales'), 'bc' => $bc);
		
        $this->load->view($this->theme . 'pos/orderkitchen_item', $this->data);
	}
	
    public function view_bill()
    {
        $this->sma->checkPermissions('index');
        $this->data['tax_rates'] = $this->site->getAllTaxRates();
        $this->load->view($this->theme . 'pos/view_bill', $this->data);
    }
	
	public function customer_bill()
    {
        $this->sma->checkPermissions('index');
        $this->data['tax_rates'] = $this->site->getAllTaxRates();
        $this->load->view($this->theme . 'pos/customer_bill', $this->data);
    }

    public function stripe_balance()
    {
        if (!$this->Owner) {
            return FALSE;
        }
        $this->load->admin_model('stripe_payments');

        return $this->stripe_payments->get_balance();
    }

    public function paypal_balance()
    {
        if (!$this->Owner) {
            return FALSE;
        }
        $this->load->admin_model('paypal_payments');

        return $this->paypal_payments->get_balance();
    }

    public function registers()
    {
        $this->sma->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['registers'] = $this->pos_model->getOpenRegisters();
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('pos'), 'page' => lang('pos')), array('link' => '#', 'page' => lang('open_registers')));
        $meta = array('page_title' => lang('open_registers'), 'bc' => $bc);
        $this->page_construct('pos/registers', $meta, $this->data);
    }

    public function open_register()
    {
        $this->sma->checkPermissions('index');
        $this->form_validation->set_rules('cash_in_hand', lang("cash_in_hand"), 'trim|required|numeric');

        if ($this->form_validation->run() == TRUE) {
            $data = array(
                'date' => date('Y-m-d H:i:s'),
                'cash_in_hand' => $this->input->post('cash_in_hand'),
                'user_id'      => $this->session->userdata('user_id'),
                'status'       => 'open',
                );
        } 
        if ($this->form_validation->run() == TRUE && $this->pos_model->openRegister($data)) {
            $this->session->set_flashdata('message', lang("welcome_to_pos"));
            admin_redirect("pos");
        } else {

            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('open_register')));
            $meta = array('page_title' => lang('open_register'), 'bc' => $bc);
            $this->page_construct('pos/open_register', $meta, $this->data);
        }
    }

    public function user_open_register($cash_in_hand = NULL)
    {
        $cash_in_hand = $this->input->post('cash_in_hand'); 

        if ($cash_in_hand) {
            $data = array(
                'date' => date('Y-m-d H:i:s'),
                'cash_in_hand' => $this->input->post('cash_in_hand'),
                'user_id'      => $this->session->userdata('user_id'),
                'status'       => 'open',
                );
        } 
        if ($this->pos_model->openRegister($data)) {
        	$msg = 'success'; 
            
        } else {
			$msg = 'error';
        }
     
        $this->sma->send_json(array('msg' => $msg));
    }
    public function close_register($user_id = NULL)
    {
        $this->sma->checkPermissions('index');
        if (!$this->Owner && !$this->Admin) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->form_validation->set_rules('total_cash', lang("total_cash"), 'trim|required|numeric');
        $this->form_validation->set_rules('total_cheques', lang("total_cheques"), 'trim|required|numeric');
        $this->form_validation->set_rules('total_cc_slips', lang("total_cc_slips"), 'trim|required|numeric');

        if ($this->form_validation->run() == TRUE) {
            if ($this->Owner || $this->Admin) {
                $user_register = $user_id ? $this->pos_model->registerData($user_id) : NULL;
                $rid = $user_register ? $user_register->id : $this->session->userdata('register_id');
                $user_id = $user_register ? $user_register->user_id : $this->session->userdata('user_id');
            } else {
                $rid = $this->session->userdata('register_id');
                $user_id = $this->session->userdata('user_id');
            }
            $data = array(
                'closed_at'                => date('Y-m-d H:i:s'),
                'total_cash'               => $this->input->post('total_cash'),
                'total_cheques'            => $this->input->post('total_cheques'),
                'total_cc_slips'           => $this->input->post('total_cc_slips'),
                'total_cash_submitted'     => $this->input->post('total_cash_submitted'),
                'total_cheques_submitted'  => $this->input->post('total_cheques_submitted'),
                'total_cc_slips_submitted' => $this->input->post('total_cc_slips_submitted'),
                'note'                     => $this->input->post('note'),
                'status'                   => 'close',
                'transfer_opened_bills'    => $this->input->post('transfer_opened_bills'),
                'closed_by'                => $this->session->userdata('user_id'),
                );
        } elseif ($this->input->post('close_register')) {
            $this->session->set_flashdata('error', (validation_errors() ? validation_errors() : $this->session->flashdata('error')));
            admin_redirect("pos");
        }

        if ($this->form_validation->run() == TRUE && $this->pos_model->closeRegister($rid, $user_id, $data)) {
            $this->session->set_flashdata('message', lang("register_closed"));
            admin_redirect("welcome");
        } else {
        	
            if ($this->Owner || $this->Admin) {
                $user_register = $user_id ? $this->pos_model->registerData($user_id) : NULL;
                $register_open_time = $user_register ? $user_register->date : NULL;
                $this->data['cash_in_hand'] = $user_register ? $user_register->cash_in_hand : NULL;
                $this->data['register_open_time'] = $user_register ? $register_open_time : NULL;
            } else {
                $register_open_time = $this->session->userdata('register_open_time');
                $this->data['cash_in_hand'] = NULL;
                $this->data['register_open_time'] = NULL;
            }
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['ccsales'] = $this->pos_model->getRegisterCCSales($register_open_time, $user_id);

            $this->data['cashsales'] = $this->pos_model->getRegisterCashSales($register_open_time, $user_id);
            $this->data['chsales'] = $this->pos_model->getRegisterChSales($register_open_time, $user_id);
            $this->data['gcsales'] = $this->pos_model->getRegisterGCSales($register_open_time);
            $this->data['pppsales'] = $this->pos_model->getRegisterPPPSales($register_open_time, $user_id);
            $this->data['stripesales'] = $this->pos_model->getRegisterStripeSales($register_open_time, $user_id);
            $this->data['authorizesales'] = $this->pos_model->getRegisterAuthorizeSales($register_open_time, $user_id);
            $this->data['totalsales'] = $this->pos_model->getRegisterSales($register_open_time, $user_id);
            $this->data['refunds'] = $this->pos_model->getRegisterRefunds($register_open_time, $user_id);
            $this->data['cashrefunds'] = $this->pos_model->getRegisterCashRefunds($register_open_time, $user_id);
            $this->data['expenses'] = $this->pos_model->getRegisterExpenses($register_open_time, $user_id);
            $this->data['users'] = $this->pos_model->getUsers($user_id);
            $this->data['suspended_bills'] = $this->pos_model->getSuspendedsales($user_id);
            $this->data['user_id'] = $user_id;
            $this->data['modal_js'] = $this->site->modal_js();
           /* echo "<pre>";
            print_r($this->data);die;*/	

            $this->load->view($this->theme . 'pos/close_register', $this->data);
        }
    }

    public function getrecipeDataByCode($code = NULL, $warehouse_id = NULL)
    {
        $this->sma->checkPermissions('index');
        if ($this->input->get('code')) {
            $code = $this->input->get('code', TRUE);
        }
        if ($this->input->get('warehouse_id')) {
            $warehouse_id = $this->input->get('warehouse_id', TRUE);
        }
        if ($this->input->get('customer_id')) {
            $customer_id = $this->input->get('customer_id', TRUE);
        }
        if (!$code) {
            echo NULL;
            die();
        }
        $warehouse = $this->site->getWarehouseByID($warehouse_id);
        $customer = $this->site->getCompanyByID($customer_id);
        $customer_group = $this->site->getCustomerGroupByID($customer->customer_group_id);
        /*$discount_recipe = $this->site->getDiscounts($code);*/
        $row = $this->pos_model->getWHrecipe($code, $warehouse_id);
        $option = false;
		
		
		
        if ($row) {
            unset($row->cost, $row->details, $row->recipe_details, $row->image, $row->barcode_symbology, $row->cf1, $row->cf2, $row->cf3, $row->cf4, $row->cf5, $row->cf6, $row->supplier1price, $row->supplier2price, $row->cfsupplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1, $row->supplier2, $row->supplier3, $row->supplier4, $row->supplier5, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);
			
			
            $row->item_tax_method = $row->tax_method;
            $row->qty = 1;
            $row->discount = '0';
            $row->serial = '';
            $options = $this->pos_model->getrecipeOptions($row->id, $warehouse_id);
			$addons = $this->pos_model->getrecipeAddons($row->id);
            if ($options) {
                $opt = current($options);
                if (!$option) {
                    $option = $opt->id;
                }
            } else {
                $opt = json_decode('{}');
                $opt->price = 0;
            }
            $row->option = $option;
			
			if ($addons) {
                $aon = current($addons);
                if (!$option) {
                    $option = $aon->id;
                }
            } else {
                $aon = json_decode('{}');
                $aon->price = 0;
            }
            $row->addon = !empty($addon) ? $addon : NULL;
			
			$buy = $this->site->checkBuyget($row->id);
			if(!empty($buy)){
				$row->buy_id = $buy->id;
				$row->get_item = $buy->get_item;
				$row->buy_quantity = $buy->buy_quantity;
				$row->get_quantity = $buy->get_quantity;
				$total_quantity = $x_quantity % $y_quantity;
				$x_quantity = ($x_quantity - $total_quantity) / $y_quantity;
				$total_get_quantity = $x_quantity * $b_quantity;
				$row->total_get_quantity = $total_get_quantity;
				$row->free_recipe = $buy->free_recipe;
			}else{
				$row->buy_id = 0;
				$row->get_item = 0;
				$row->buy_quantity = 0;
				$row->get_quantity = 0;
				$row->total_get_quantity = 0;
				$row->free_recipe = '';
			}
			
            $row->quantity = 0;
            $pis = $this->site->getPurchasedItems($row->id, $warehouse_id, $row->option);
            if ($pis) {
                foreach ($pis as $pi) {
                    $row->quantity += $pi->quantity_balance;
                }
            }
            if ($row->type == 'standard' && (!$this->Settings->overselling && $row->quantity < 1)) {
                echo NULL; die();
            }
            if ($options) {
                $option_quantity = 0;
                foreach ($options as $option) {
                    $pis = $this->site->getPurchasedItems($row->id, $warehouse_id, $row->option);
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
                if ($pr_group_price = $this->site->getrecipeGroupPrice($row->id, $customer->price_group_id)) {
                    $row->price = $pr_group_price->price;
                }
            } elseif ($warehouse->price_group_id) {
                if ($pr_group_price = $this->site->getrecipeGroupPrice($row->id, $warehouse->price_group_id)) {
                    $row->price = $pr_group_price->price;
                }
            }
            $row->price = $row->price;
            $row->real_unit_price = $row->price;
            $row->base_quantity = 1;
            $row->base_unit = $row->price ;
            $row->base_unit_price = $row->price;
            $row->unit = $row->sale_unit ? $row->sale_unit : $row->unit;
            $row->comment = '';
            $combo_items = false;
            if ($row->type == 'combo') {
                $combo_items = $this->pos_model->getrecipeComboItems($row->id, $warehouse_id);
				
            }
            $units = $this->site->getUnitsByBUID($row->base_unit);
            $tax_rate = $this->site->getTaxRateByID($row->tax_rate);

            $pr = array('id' => str_replace(".", "", microtime(true)), 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'category' => $row->category_id, 'row' => $row, 'combo_items' => $combo_items, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options, 'addons' => $addons);

            $this->sma->send_json($pr);
        } else {
            echo NULL;
        }
    }

    public function ajaxrecipe($category_id = NULL, $warehouse_id = NULL, $subcategory_id=NULL, $brand_id = NULL)
    {
		
		
        $this->sma->checkPermissions('index');
		
		
        if ($this->input->get('brand_id')) {
            $brand_id = $this->input->get('brand_id');
        }
        if ($this->input->get('category_id')) {
            $category_id = $this->input->get('category_id');
        } else {
            $category_id = $this->pos_settings->default_category;
        }
        if ($this->input->get('subcategory_id')) {
            $subcategory_id = $this->input->get('subcategory_id');
        } else {
            $subcategory_id = NULL;
        }
		
		if ($this->input->get('warehouse_id')) {
            $warehouse_id = $this->input->get('warehouse_id');
        } else {
            $warehouse_id = $warehouse_id;
        }
		
        if ($this->input->get('per_page') == 'n') {
            $page = 0;
        } else {
            $page = $this->input->get('per_page');
        }

        $this->load->library("pagination");


        $config = array();
        $config["base_url"] = base_url() . "pos/ajaxrecipe";
        $config["total_rows"] = $this->pos_model->recipe_count($category_id, $warehouse_id, $subcategory_id, $brand_id);
		
        $config["per_page"] = $this->pos_settings->pro_limit;
		
        $config['prev_link'] = FALSE;
        $config['next_link'] = FALSE;
        $config['display_pages'] = FALSE;
        $config['first_link'] = FALSE;
        $config['last_link'] = FALSE;

        $this->pagination->initialize($config);
		
		
		
        $recipe = $this->pos_model->fetch_recipe($category_id, $warehouse_id, $config["per_page"], $page, $subcategory_id, $brand_id);
		
       		
        $pro = 1;
        $prods = '<div>';
        if (!empty($recipe)) {
            foreach ($recipe as $recipe) {
							
                $count = $recipe->id;
				$buy = $this->site->checkBuyget($recipe->id);
				
				$default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
				$default_currency_rate = $default_currency_data->rate;
				$default_currency_symbol = $default_currency_data->symbol;
				
				if(!empty($buy)){
					
					
					if($buy->buy_method == 'buy_x_get_x'){
						$buyvalue = "<div class='rippon'><div class='offer_list'><span>Buy ".$buy->buy_quantity."  Get ".$buy->get_quantity." ".$buy->free_recipe." </span></div></div>";
					}elseif($buy->buy_method == 'buy_x_get_y'){
						$buyvalue = "<div class='rippon'><div class='offer_list'><span>Buy ".$buy->buy_quantity."  Get ".$buy->free_recipe." ( ".$buy->get_quantity.")</span></div></div>";
					}
					
				}else{
					$buyvalue = '';
				}
				
                if ($count < 10) {
                    $count = "0" . ($count / 100) * 100;
                }
                if ($category_id < 10) {
                    $category_id = "0" . ($category_id / 100) * 100;
                }
				
				if($this->Settings->user_language == 'khmer'){
											
					if(!empty($recipe->khmer_name)){
						
						$recipe_name = $recipe->khmer_name;
					}else{
						$recipe_name = $recipe->name;
					}
				}else{
					$recipe_name = $recipe->name;
				}
				
                $prods .= "<button id=\"recipe-" . $category_id . $count . "\" type=\"button\" value='" . $recipe->code . "' title=\"" . $recipe->name . "\" class=\"btn-prni btn-" . $this->pos_settings->recipe_button_color . " recipe pos-tip\" data-container=\"body\"><img src=\"" . base_url() . "assets/uploads/thumbs/" . $recipe->image . "\" alt=\"" . $recipe_name . "\" class='img-rounded' />";
				
				if(strlen($recipe->name) < 20){		
						
					$prods .= "<span class='name_strong'>" .$recipe_name. "</span>";
				}else{
					$prods .= "<marquee class='name_strong' behavior='alternate' direction='left' scrollamount='1'>&nbsp;&nbsp;" .$recipe_name. "&nbsp;&nbsp;</marquee>";
				}
				
				
				$prods .=  "<br><span class='price_strong'> ".$default_currency_symbol ."" . $this->sma->formatDecimal($recipe->price). "</span>".$buyvalue." </button>";

                $pro++;
            }
        }
        $prods .= "</div>";

        if ($this->input->get('per_page')) {
            echo $prods;
        } else {
            return $prods;
        }
    }
	
public function getSalesItems($sale_id = NULL)
    {
       $sale_id = $this->input->get('sale_id');
        
        $sales_item = $this->pos_model->getBillCashierPrintdata($sale_id);
        $sales = $this->pos_model->getSalesData($sale_id);
       /*echo "<pre>";
       print_r($sales);exit;
       echo "</pre>";*/
        /*$item = '';
        if (!empty($sales_item)) {
           
                   
                    $html ='<tbody class="ui-sortable">';
                    foreach($sales_item as $item){
                        $html .='<tr><td>'.$item->recipe_name.'</td><td>'.$item->unit_price.'</td><td>'.$item->quantity.'</td><td>'.$item->subtotal.'</td></tr>';
                    }
                    $html .='</tbody>';
                    
                    $html .='</table>
                    <div style="clear:both;"></div>
                </div>';
            $item = $html;
        }*/
        /*$table = $this->pos_model->checkTables($table_id,$order_type);*/
        $this->sma->send_json(array('sales_item' => $sales_item, 'sales' => $sales));
        /*$this->sma->send_json($sales_item);*/

       /* $this->sma->send_json(array('sales_item' => $item));*/
    }
    public function ajaxcategorydata($category_id = NULL)
    {
        $this->sma->checkPermissions('index');
        if ($this->input->get('category_id')) {
            $category_id = $this->input->get('category_id');
        } else {
            $category_id = $this->pos_settings->default_category;
        }

        $subcategories = $this->site->getrecipeSubCategories($category_id);
        $scats = '';
        if ($subcategories) {
            foreach ($subcategories as $category) {
				
				if($this->Settings->user_language == 'khmer'){
											
					if(!empty($category->khmer_name)){
						
						$subcategory_name = $category->khmer_name;
					}else{
						$subcategory_name = $category->name;
					}
				}else{
					$subcategory_name = $category->name;
				}
				
                $scats .= "<button id=\"subcategory-" . $category->id . "\" type=\"button\" value='" . $category->id . "' class=\"btn-prni subcategory\" ><img src=\"" . base_url() ."assets/uploads/thumbs/" . ($category->image ? $category->image : 'no_image.png') . "\" class='img-rounded img-thumbnail' />";
				
				
				 if(strlen($subcategory_name) < 20){		
				
					$scats .= "<span class='name_strong'>" .$subcategory_name. "</span>";
				}else{
					$scats .= "<marquee class='sub_strong' behavior='alternate' direction='left' scrollamount='1'>&nbsp;&nbsp;&nbsp;&nbsp;" .$subcategory_name. "&nbsp;&nbsp;&nbsp;&nbsp;</marquee>";
				}
				  $scats .=  "</button>";
				  
            }
        }

        $recipe = $this->ajaxrecipe($category_id, $this->session->userdata('warehouse_id'));

        if (!($tcp = $this->pos_model->recipe_count($category_id, $this->session->userdata('warehouse_id')))) {
            $tcp = 0;
        }

        $this->sma->send_json(array('recipe' => $recipe, 'subcategories' => $scats, 'tcp' => $tcp));
    }

    public function ajaxbranddata($brand_id = NULL)
    {
        $this->sma->checkPermissions('index');
        if ($this->input->get('brand_id')) {
            $brand_id = $this->input->get('brand_id');
        }

        $recipe = $this->ajaxrecipe(FALSE, $this->session->userdata('warehouse_id'), $brand_id);

        if (!($tcp = $this->pos_model->recipe_count(FALSE, $this->session->userdata('warehouse_id'), $brand_id))) {
            $tcp = 0;
        }

        $this->sma->send_json(array('recipe' => $recipe, 'tcp' => $tcp));
    }

    /* ------------------------------------------------------------------------------------ */

    public function view($sale_id = NULL, $modal = NULL)
    {
        $this->sma->checkPermissions('index');
        if ($this->input->get('id')) {
            $sale_id = $this->input->get('id');
        }
        $this->load->helper('pos');
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['message'] = $this->session->flashdata('message');
        $inv = $this->pos_model->getInvoiceByID($sale_id);
        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($inv->created_by, true);
        }
        $this->data['rows'] = $this->pos_model->getAllInvoiceItems($sale_id);
        $biller_id = $inv->biller_id;
        $customer_id = $inv->customer_id;
        $this->data['biller'] = $this->pos_model->getCompanyByID($biller_id);
        $this->data['customer'] = $this->pos_model->getCompanyByID($customer_id);
        $this->data['payments'] = $this->pos_model->getInvoicePayments($sale_id);
        $this->data['pos'] = $this->pos_model->getSetting();
        $this->data['barcode'] = $this->barcode($inv->reference_no, 'code128', 30);
        $this->data['return_sale'] = $inv->return_id ? $this->pos_model->getInvoiceByID($inv->return_id) : NULL;
        $this->data['return_rows'] = $inv->return_id ? $this->pos_model->getAllInvoiceItems($inv->return_id) : NULL;
        $this->data['return_payments'] = $this->data['return_sale'] ? $this->pos_model->getInvoicePayments($this->data['return_sale']->id) : NULL;
        $this->data['inv'] = $inv;
        $this->data['sid'] = $sale_id;
        $this->data['modal'] = $modal;
        $this->data['created_by'] = $this->site->getUser($inv->created_by);
        $this->data['printer'] = $this->pos_model->getPrinterByID($this->pos_settings->printer);
        $this->data['page_title'] = $this->lang->line("invoice");
        $this->load->view($this->theme . 'pos/view', $this->data);
    }

    public function register_details()
    {
        $this->sma->checkPermissions('index');
        $register_open_time = $this->session->userdata('register_open_time');
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['ccsales'] = $this->pos_model->getRegisterCCSales($register_open_time);
        $this->data['cashsales'] = $this->pos_model->getRegisterCashSales($register_open_time);
        $this->data['chsales'] = $this->pos_model->getRegisterChSales($register_open_time);
        $this->data['gcsales'] = $this->pos_model->getRegisterGCSales($register_open_time);
        $this->data['pppsales'] = $this->pos_model->getRegisterPPPSales($register_open_time);
        $this->data['stripesales'] = $this->pos_model->getRegisterStripeSales($register_open_time);
        $this->data['authorizesales'] = $this->pos_model->getRegisterAuthorizeSales($register_open_time);
        $this->data['totalsales'] = $this->pos_model->getRegisterSales($register_open_time);
        $this->data['refunds'] = $this->pos_model->getRegisterRefunds($register_open_time);
        $this->data['expenses'] = $this->pos_model->getRegisterExpenses($register_open_time);
        $this->load->view($this->theme . 'pos/register_details', $this->data);
    }

    public function today_sale()
    {
        if (!$this->Owner && !$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            $this->sma->md();
        }

        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['ccsales'] = $this->pos_model->getTodayCCSales();
        $this->data['cashsales'] = $this->pos_model->getTodayCashSales();
        $this->data['chsales'] = $this->pos_model->getTodayChSales();
        $this->data['pppsales'] = $this->pos_model->getTodayPPPSales();
        $this->data['stripesales'] = $this->pos_model->getTodayStripeSales();
        $this->data['authorizesales'] = $this->pos_model->getTodayAuthorizeSales();
        $this->data['totalsales'] = $this->pos_model->getTodaySales();
        $this->data['refunds'] = $this->pos_model->getTodayRefunds();
        $this->data['expenses'] = $this->pos_model->getTodayExpenses();
        $this->load->view($this->theme . 'pos/today_sale', $this->data);
    }

    public function check_pin()
    {
        $pin = $this->input->post('pw', TRUE);
        if ($pin == $this->pos_pin) {
            $this->sma->send_json(array('res' => 1));
        }
        $this->sma->send_json(array('res' => 0));
    }

    public function barcode($text = NULL, $bcs = 'code128', $height = 50)
    {
        return admin_url('recipe/gen_barcode/' . $text . '/' . $bcs . '/' . $height);
    }

    public function settings()
    {
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect("welcome");
        }
        $this->form_validation->set_message('is_natural_no_zero', $this->lang->line('no_zero_required'));
        $this->form_validation->set_rules('pro_limit', $this->lang->line('pro_limit'), 'required|is_natural_no_zero');
        $this->form_validation->set_rules('pin_code', $this->lang->line('delete_code'), 'numeric');
        $this->form_validation->set_rules('category', $this->lang->line('default_category'), 'required|is_natural_no_zero');
        $this->form_validation->set_rules('customer', $this->lang->line('default_customer'), 'required|is_natural_no_zero');
        $this->form_validation->set_rules('biller', $this->lang->line('default_biller'), 'required|is_natural_no_zero');
		$this->form_validation->set_rules('tax', $this->lang->line('default_tax'), 'required|is_natural_no_zero');
        if ($this->form_validation->run() == TRUE) {

				$data = array(
				'pro_limit'                 => $this->input->post('pro_limit'),
				'pin_code'                  => $this->input->post('pin_code') ? $this->input->post('pin_code') : NULL,
				'default_category'          => $this->input->post('category'),
				'default_customer'          => $this->input->post('customer'),
				'default_biller'            => $this->input->post('biller'),
				'default_billgenerator'     => $this->input->post('default_billgenerator'),
				'table_change'     => $this->input->post('table_change'),
				'default_tax'		  		=> $this->input->post('tax'),
				'tax_type'				    => $this->input->post('tax_type'),
				'display_time'              => $this->input->post('display_time'),
				'receipt_printer'           => $this->input->post('receipt_printer'),
				'cash_drawer_codes'         => $this->input->post('cash_drawer_codes'),
				'cf_title1'                 => $this->input->post('cf_title1'),
				'cf_title2'                 => $this->input->post('cf_title2'),
				'cf_value1'                 => $this->input->post('cf_value1'),
				'cf_value2'                 => $this->input->post('cf_value2'),
				'focus_add_item'            => $this->input->post('focus_add_item'),
				'add_manual_recipe'        => $this->input->post('add_manual_recipe'),
				'customer_selection'        => $this->input->post('customer_selection'),
				'add_customer'              => $this->input->post('add_customer'),
				'toggle_category_slider'    => $this->input->post('toggle_category_slider'),
				'toggle_subcategory_slider' => $this->input->post('toggle_subcategory_slider'),
				'toggle_brands_slider'      => $this->input->post('toggle_brands_slider'),
				'cancel_sale'               => $this->input->post('cancel_sale'),
				'suspend_sale'              => $this->input->post('suspend_sale'),
				'print_items_list'          => $this->input->post('print_items_list'),
				'finalize_sale'             => $this->input->post('finalize_sale'),
				'today_sale'                => $this->input->post('today_sale'),
				'open_hold_bills'           => $this->input->post('open_hold_bills'),
				'close_register'            => $this->input->post('close_register'),
				'tooltips'                  => $this->input->post('tooltips'),
				'keyboard'                  => $this->input->post('keyboard'),
				'pos_printers'              => $this->input->post('pos_printers'),
				'java_applet'               => $this->input->post('enable_java_applet'),
				'recipe_button_color'      => $this->input->post('recipe_button_color'),
				'paypal_pro'                => $this->input->post('paypal_pro'),
				'stripe'                    => $this->input->post('stripe'),
				'authorize'                 => $this->input->post('authorize'),
				'rounding'                  => $this->input->post('rounding'),
				'item_order'                => $this->input->post('item_order'),
				'after_sale_page'           => $this->input->post('after_sale_page'),
				'printer'                   => $this->input->post('receipt_printer'),
				'order_printers'            => json_encode($this->input->post('order_printers')),
				'auto_print'                => $this->input->post('auto_print'),
				'remote_printing'           => DEMO ? 1 : $this->input->post('remote_printing'),
				'customer_details'          => $this->input->post('customer_details'),
				'local_printers'            => $this->input->post('local_printers'),
				'display_tax'               => $this->input->post('display_tax'),
				'open_sale_register'        => $this->input->post('open_sale_register'),
				);
            $payment_config = array(
                'APIUsername'            => $this->input->post('APIUsername'),
                'APIPassword'            => $this->input->post('APIPassword'),
                'APISignature'           => $this->input->post('APISignature'),
                'stripe_secret_key'      => $this->input->post('stripe_secret_key'),
                'stripe_publishable_key' => $this->input->post('stripe_publishable_key'),
                'api_login_id'           => $this->input->post('api_login_id'),
                'api_transaction_key'    => $this->input->post('api_transaction_key'),
            );
        } elseif ($this->input->post('update_settings')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("pos/settings");
        }

        if ($this->form_validation->run() == TRUE && $this->pos_model->updateSetting($data)) {
            if (DEMO) {
                $this->session->set_flashdata('message', $this->lang->line('pos_setting_updated'));
                admin_redirect("pos/settings");
            }
            if ($this->write_payments_config($payment_config)) {
                $this->session->set_flashdata('message', $this->lang->line('pos_setting_updated'));
                admin_redirect("pos/settings");
            } else {
                $this->session->set_flashdata('error', $this->lang->line('pos_setting_updated_payment_failed'));
                admin_redirect("pos/settings");
            }
        } else {

            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

            $this->data['pos'] = $this->pos_model->getSetting();
            $this->data['categories'] = $this->site->getAllrecipeCategories();
            $this->data['customer'] = $this->pos_model->getCompanyByID($this->pos_settings->default_customer);
            $this->data['billers'] = $this->pos_model->getAllBillerCompanies();
			$this->data['taxs'] = $this->pos_model->getAllTaxRates();
            $this->config->load('payment_gateways');
            $this->data['stripe_secret_key'] = $this->config->item('stripe_secret_key');
            $this->data['stripe_publishable_key'] = $this->config->item('stripe_publishable_key');
            $authorize = $this->config->item('authorize');
            $this->data['api_login_id'] = $authorize['api_login_id'];
            $this->data['api_transaction_key'] = $authorize['api_transaction_key'];
            $this->data['APIUsername'] = $this->config->item('APIUsername');
            $this->data['APIPassword'] = $this->config->item('APIPassword');
            $this->data['APISignature'] = $this->config->item('APISignature');
            $this->data['printers'] = $this->pos_model->getAllPrinters();
            $this->data['paypal_balance'] = NULL; // $this->pos_settings->paypal_pro ? $this->paypal_balance() : NULL;
            $this->data['stripe_balance'] = NULL; // $this->pos_settings->stripe ? $this->stripe_balance() : NULL;
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('pos_settings')));
            $meta = array('page_title' => lang('pos_settings'), 'bc' => $bc);
            $this->page_construct('pos/settings', $meta, $this->data);
        }
    }

    public function write_payments_config($config)
    {
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect("welcome");
        }
        if (DEMO) {
            return TRUE;
        }
        $file_contents = file_get_contents('./assets/config_dumps/payment_gateways.php');
        $output_path = APPPATH . 'config/payment_gateways.php';
        $this->load->library('parser');
        $parse_data = array(
            'APIUsername'            => $config['APIUsername'],
            'APIPassword'            => $config['APIPassword'],
            'APISignature'           => $config['APISignature'],
            'stripe_secret_key'      => $config['stripe_secret_key'],
            'stripe_publishable_key' => $config['stripe_publishable_key'],
            'api_login_id'           => $config['api_login_id'],
            'api_transaction_key'    => $config['api_transaction_key'],
        );
        $new_config = $this->parser->parse_string($file_contents, $parse_data);

        $handle = fopen($output_path, 'w+');
        @chmod($output_path, 0777);

        if (is_writable($output_path)) {
            if (fwrite($handle, $new_config)) {
                @chmod($output_path, 0644);
                return TRUE;
            } else {
                @chmod($output_path, 0644);
                return FALSE;
            }
        } else {
            @chmod($output_path, 0644);
            return FALSE;
        }
    }

    public function opened_bills($per_page = 0)
    {
        $this->load->library('pagination');

        //$this->table->set_heading('Id', 'The Title', 'The Content');
        if ($this->input->get('per_page')) {
            $per_page = $this->input->get('per_page');
        }

        $config['base_url'] = admin_url('pos/opened_bills');
        $config['total_rows'] = $this->pos_model->bills_count();
        $config['per_page'] = 6;
        $config['num_links'] = 3;

        $config['full_tag_open'] = '<ul class="pagination pagination-sm">';
        $config['full_tag_close'] = '</ul>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a>';
        $config['cur_tag_close'] = '</a></li>';

        $this->pagination->initialize($config);
        $data['r'] = TRUE;
        $bills = $this->pos_model->fetch_bills($config['per_page'], $per_page);
        if (!empty($bills)) {
            $html = "";
            $html .= '<ul class="ob">';
            foreach ($bills as $bill) {
                $html .= '<li><button type="button" class="btn btn-info sus_sale" id="' . $bill->id . '"><p>' . $bill->suspend_note . '</p><strong>' . $bill->customer . '</strong><br>'.lang('date').': ' . $bill->date . '<br>'.lang('items').': ' . $bill->count . '<br>'.lang('total').': ' . $this->sma->formatMoney($bill->total) . '</button></li>';
            }
            $html .= '</ul>';
        } else {
            $html = "<h3>" . lang('no_opeded_bill') . "</h3><p>&nbsp;</p>";
            $data['r'] = FALSE;
        }

        $data['html'] = $html;

        $data['page'] = $this->pagination->create_links();
        echo $this->load->view($this->theme . 'pos/opened', $data, TRUE);

    }

    public function delete($id = NULL)
    {

        $this->sma->checkPermissions('index');

        if ($this->pos_model->deleteBill($id)) {
            $this->sma->send_json(array('error' => 0, 'msg' => lang("suspended_sale_deleted")));
        }
    }

    public function email_receipt($sale_id = NULL)
    {
        $this->sma->checkPermissions('index');
        if ($this->input->post('id')) {
            $sale_id = $this->input->post('id');
        }
        if ( ! $sale_id) {
            die('No sale selected.');
        }
        if ($this->input->post('email')) {
            $to = $this->input->post('email');
        }
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['message'] = $this->session->flashdata('message');

        $this->data['rows'] = $this->pos_model->getAllInvoiceItems($sale_id);
        $inv = $this->pos_model->getInvoiceByID($sale_id);
        $biller_id = $inv->biller_id;
        $customer_id = $inv->customer_id;
        $this->data['biller'] = $this->pos_model->getCompanyByID($biller_id);
        $this->data['customer'] = $this->pos_model->getCompanyByID($customer_id);

        $this->data['payments'] = $this->pos_model->getInvoicePayments($sale_id);
        $this->data['pos'] = $this->pos_model->getSetting();
        $this->data['barcode'] = $this->barcode($inv->reference_no, 'code128', 30);
        $this->data['inv'] = $inv;
        $this->data['sid'] = $sale_id;
        $this->data['page_title'] = $this->lang->line("invoice");

        if (!$to) {
            $to = $this->data['customer']->email;
        }
        if (!$to) {
            $this->sma->send_json(array('msg' => $this->lang->line("no_meil_provided")));
        }
        $receipt = $this->load->view($this->theme . 'pos/email_receipt', $this->data, TRUE);

        try {
            if ($this->sma->send_email($to, lang('receipt_from') .' ' . $this->data['biller']->company, $receipt)) {
                $this->sma->send_json(array('msg' => $this->lang->line("email_sent")));
            } else {
                $this->sma->send_json(array('msg' => $this->lang->line("email_failed")));
            }
        } catch (Exception $e) {
            $this->sma->send_json(array('msg' => $e->getMessage()));
        }

    }

    public function active()
    {
        $this->session->set_userdata('last_activity', now());
        if ((now() - $this->session->userdata('last_activity')) <= 20) {
            die('Successfully updated the last activity.');
        } else {
            die('Failed to update last activity.');
        }
    }

    public function add_payment($id = NULL)
    {
        $this->sma->checkPermissions('payments', TRUE, 'sales');
        $this->load->helper('security');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->form_validation->set_rules('reference_no', lang("reference_no"), 'required');
        $this->form_validation->set_rules('amount-paid', lang("amount"), 'required');
        $this->form_validation->set_rules('paid_by', lang("paid_by"), 'required');
        $this->form_validation->set_rules('userfile', lang("attachment"), 'xss_clean');
        if ($this->form_validation->run() == TRUE) {
            if ($this->input->post('paid_by') == 'deposit') {
                $sale = $this->pos_model->getInvoiceByID($this->input->post('sale_id'));
                $customer_id = $sale->customer_id;
                if ( ! $this->site->check_customer_deposit($customer_id, $this->input->post('amount-paid'))) {
                    $this->session->set_flashdata('error', lang("amount_greater_than_deposit"));
                    redirect($_SERVER["HTTP_REFERER"]);
                }
            } else {
                $customer_id = null;
            }
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $payment = array(
                'date'         => $date,
                'sale_id'      => $this->input->post('sale_id'),
                'reference_no' => $this->input->post('reference_no'),
                'amount'       => $this->input->post('amount-paid'),
                'paid_by'      => $this->input->post('paid_by'),
                'cheque_no'    => $this->input->post('cheque_no'),
                'cc_no'        => $this->input->post('paid_by') == 'gift_card' ? $this->input->post('gift_card_no') : $this->input->post('pcc_no'),
                'cc_holder'    => $this->input->post('pcc_holder'),
                'cc_month'     => $this->input->post('pcc_month'),
                'cc_year'      => $this->input->post('pcc_year'),
                'cc_type'      => $this->input->post('pcc_type'),
                'cc_cvv2'      => $this->input->post('pcc_ccv'),
                'note'         => $this->input->post('note'),
                'created_by'   => $this->session->userdata('user_id'),
                'type'         => 'received',
            );

            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = $this->digital_upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
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

        if ($this->form_validation->run() == TRUE && $msg = $this->pos_model->addPayment($payment, $customer_id)) {
            if ($msg) {
                if ($msg['status'] == 0) {
                    unset($msg['status']);
                    $error = '';
                    foreach ($msg as $m) {
                        if (is_array($m)) {
                            foreach ($m as $e) {
                                $error .= '<br>'.$e;
                            }
                        } else {
                            $error .= '<br>'.$m;
                        }
                    }
                    $this->session->set_flashdata('error', '<pre>' . $error . '</pre>');
                } else {
                    $this->session->set_flashdata('message', lang("payment_added"));
                }
            } else {
                $this->session->set_flashdata('error', lang("payment_failed"));
            }
            admin_redirect("pos/sales");
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $sale = $this->pos_model->getInvoiceByID($id);
            $this->data['inv'] = $sale;
            $this->data['payment_ref'] = $this->site->getReference('pay');
            $this->data['modal_js'] = $this->site->modal_js();

            $this->load->view($this->theme . 'pos/add_payment', $this->data);
        }
    }

    public function updates()
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect("welcome");
        }
        $this->form_validation->set_rules('purchase_code', lang("purchase_code"), 'required');
        $this->form_validation->set_rules('srampos_username', lang("srampos_username"), 'required');
        if ($this->form_validation->run() == TRUE) {
            $this->db->update('pos_settings', array('purchase_code' => $this->input->post('purchase_code', TRUE), 'srampos_username' => $this->input->post('srampos_username', TRUE)), array('pos_id' => 1));
            admin_redirect('pos/updates');
        } else {
            $fields = array('version' => $this->pos_settings->version, 'code' => $this->pos_settings->purchase_code, 'username' => $this->pos_settings->srampos_username, 'site' => base_url());
            $this->load->helper('update');
            $protocol = is_https() ? 'https://' : 'http://';
            $updates = get_remote_contents($protocol . 'api.srampos.com/v1/update/', $fields);
            $this->data['updates'] = json_decode($updates);
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('updates')));
            $meta = array('page_title' => lang('updates'), 'bc' => $bc);
            $this->page_construct('pos/updates', $meta, $this->data);
        }
    }

    public function install_update($file, $m_version, $version)
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect("welcome");
        }
        $this->load->helper('update');
        save_remote_file($file . '.zip');
        $this->sma->unzip('./files/updates/' . $file . '.zip');
        if ($m_version) {
            $this->load->library('migration');
            if (!$this->migration->latest()) {
                $this->session->set_flashdata('error', $this->migration->error_string());
                admin_redirect("pos/updates");
            }
        }
        $this->db->update('pos_settings', array('version' => $version), array('pos_id' => 1));
        unlink('./files/updates/' . $file . '.zip');
        $this->session->set_flashdata('success', lang('update_done'));
        admin_redirect("pos/updates");
    }

    function open_drawer() {

        $data = json_decode($this->input->get('data'));
        $this->load->library('escpos');
        $this->escpos->load($data->printer);
        $this->escpos->open_drawer();

    }

    function p() {

        $data = json_decode($this->input->get('data'));
        $this->load->library('escpos');
        $this->escpos->load($data->printer);
        $this->escpos->print_receipt($data);

    }

    function printers()
    {
        $this->sma->checkPermissions();
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['page_title'] = lang('printers');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('pos'), 'page' => lang('pos')), array('link' => '#', 'page' => lang('printers')));
        $meta = array('page_title' => lang('list_printers'), 'bc' => $bc);
        $this->page_construct('pos/printers', $meta, $this->data);
    }

    function get_printers()
    {
        $this->sma->checkPermissions('printers');

        $this->load->library('datatables');
        $this->datatables
        ->select("'sno',id, title, type, profile, path, ip_address, port")
        ->from("printers")
        ->add_column("Actions", "<div class='text-center'> <a href='" . admin_url('pos/edit_printer/$1') . "' class='btn-warning btn-xs tip' title='".lang("edit_printer")."'><i class='fa fa-edit'></i></a> <a href='#' class='btn-danger btn-xs tip po' title='<b>" . lang("delete_printer") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('pos/delete_printer/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", "id")
        ->unset_column('id');
        echo $this->datatables->generate();

    }

    function add_printer()
    {

        $this->sma->checkPermissions();

        $this->form_validation->set_rules('title', $this->lang->line("title"), 'required');
        $this->form_validation->set_rules('type', $this->lang->line("type"), 'required');
        $this->form_validation->set_rules('profile', $this->lang->line("profile"), 'required');
        $this->form_validation->set_rules('char_per_line', $this->lang->line("char_per_line"), 'required');
        if ($this->input->post('type') == 'network') {
            $this->form_validation->set_rules('ip_address', $this->lang->line("ip_address"), 'required|is_unique[printers.ip_address]');
            $this->form_validation->set_rules('port', $this->lang->line("port"), 'required');
        } else {
            $this->form_validation->set_rules('path', $this->lang->line("path"), 'required|is_unique[printers.path]');
        }

        if ($this->form_validation->run() == true) {

            $data = array('title' => $this->input->post('title'),
                'type' => $this->input->post('type'),
                'profile' => $this->input->post('profile'),
                'char_per_line' => $this->input->post('char_per_line'),
                'path' => $this->input->post('path'),
                'ip_address' => $this->input->post('ip_address'),
                'port' => ($this->input->post('type') == 'network') ? $this->input->post('port') : NULL,
            );

        }

        if ( $this->form_validation->run() == true && $cid = $this->pos_model->addPrinter($data)) {

            $this->session->set_flashdata('message', $this->lang->line("printer_added"));
            admin_redirect("pos/printers");

        } else {
            if($this->input->is_ajax_request()) {
                echo json_encode(array('status' => 'failed', 'msg' => validation_errors())); die();
            }

            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
            $this->data['page_title'] = lang('add_printer');
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('pos'), 'page' => lang('pos')), array('link' => admin_url('pos/printers'), 'page' => lang('printers')), array('link' => '#', 'page' => lang('add_printer')));
            $meta = array('page_title' => lang('add_printer'), 'bc' => $bc);
            $this->page_construct('pos/add_printer', $meta, $this->data);
        }
    }

    function edit_printer($id = NULL)
    {

        $this->sma->checkPermissions();
        if($this->input->get('id')) { $id = $this->input->get('id', TRUE); }

        $printer = $this->pos_model->getPrinterByID($id);
        $this->form_validation->set_rules('title', $this->lang->line("title"), 'required');
        $this->form_validation->set_rules('type', $this->lang->line("type"), 'required');
        $this->form_validation->set_rules('profile', $this->lang->line("profile"), 'required');
        $this->form_validation->set_rules('char_per_line', $this->lang->line("char_per_line"), 'required');
        if ($this->input->post('type') == 'network') {
            $this->form_validation->set_rules('ip_address', $this->lang->line("ip_address"), 'required');
            if ($this->input->post('ip_address') != $printer->ip_address) {
                $this->form_validation->set_rules('ip_address', $this->lang->line("ip_address"), 'is_unique[printers.ip_address]');
            }
            $this->form_validation->set_rules('port', $this->lang->line("port"), 'required');
        } else {
            $this->form_validation->set_rules('path', $this->lang->line("path"), 'required');
            if ($this->input->post('path') != $printer->path) {
                $this->form_validation->set_rules('path', $this->lang->line("path"), 'is_unique[printers.path]');
            }
        }

        if ($this->form_validation->run() == true) {

            $data = array('title' => $this->input->post('title'),
                'type' => $this->input->post('type'),
                'profile' => $this->input->post('profile'),
                'char_per_line' => $this->input->post('char_per_line'),
                'path' => $this->input->post('path'),
                'ip_address' => $this->input->post('ip_address'),
                'port' => ($this->input->post('type') == 'network') ? $this->input->post('port') : NULL,
            );

        }

        if ( $this->form_validation->run() == true && $this->pos_model->updatePrinter($id, $data)) {

            $this->session->set_flashdata('message', $this->lang->line("printer_updated"));
            admin_redirect("pos/printers");

        } else {

            $this->data['printer'] = $printer;
            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
            $this->data['page_title'] = lang('edit_printer');
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('pos'), 'page' => lang('pos')), array('link' => admin_url('pos/printers'), 'page' => lang('printers')), array('link' => '#', 'page' => lang('edit_printer')));
            $meta = array('page_title' => lang('edit_printer'), 'bc' => $bc);
            $this->page_construct('pos/edit_printer', $meta, $this->data);

        }
    }

    function delete_printer($id = NULL)
    {
        if(DEMO) {
            $this->session->set_flashdata('error', $this->lang->line("disabled_in_demo"));
            $this->sma->md();
        }
        //if (!$this->Owner) {
        //    $this->session->set_flashdata('error', lang('access_denied'));
        //    $this->sma->md();
        //}
	$this->sma->checkPermissions();

        if ($this->input->get('id')) { $id = $this->input->get('id', TRUE); }

        if ($this->pos_model->deletePrinter($id)) {
            $this->sma->send_json(array('error' => 0, 'msg' => lang("printer_deleted")));
        }

    }
	function gatdata_print_billing()
	{	
		$id =  $this->input->get('billid');
		$row['billdata'] = $this->pos_model->get_BillData($id);
		// echo "<pre>";
		// print_r($row['billdata']);exit;
		$row['billitemdata'] = $this->pos_model->getAllBillitems($id);
		$inv = $this->pos_model->getInvoiceByID($id);
		$row['biller'] = $this->pos_model->getCompanyByID($inv->biller_id);
		$row['inv'] = $inv;
		$row['created_by']= $this->site->getUser($inv->created_by);
		$customer_id = $inv->customer_id;
		$delivery_person = $inv->delivery_person_id;
	   $row['customer'] = $this->pos_model->getCompanyByID($customer_id);
	   if($delivery_person != 0){
	   		$row['delivery_person'] = $this->pos_model->getUserByID($delivery_person);
	   }
		$this->sma->send_json($row);
	}

/*public function check_timeout_notify($id = NULL,)
    {        
        $order_item_id = $this->input->post('id');

		$noti = $this->pos_model->checkTimeoutNotify($order_item_id);

		if(!empty($noti)){    
            $status = 'success'; 

        }else{            
            $status = 'error'; 
        }
        $this->sma->send_json(array('msg' => $msg, 'status' => $msg));
    }	*/
public function update_timeout_notify($id = NULL)
    {   
        $order_item_id = $this->input->post('id');

		$notification_array['from_role'] = $this->session->userdata('group_id');

		$orderitem = $this->pos_model->getOrderitemDetsils($order_item_id);


		$noti = $this->pos_model->checkTimeoutNotify($order_item_id);

		if(empty($noti)){

        if (!empty($orderitem)) {
            
            foreach ($orderitem as $item) {

            	$notification_array['insert_array'] = array(
					'msg' => 'The order ['.$item->name.'-'.$item->reference_no.'-'.$item->recipe_name.'] has been Timeout.',
					'type' => 'Order Timeout Status',
					'table_id' =>  $item->table_id,
					'user_id' => $this->session->userdata('user_id'),	
					'to_user_id' => $item->created_by,	
					'role_id' => WAITER,
					'warehouse_id' => $this->session->userdata('warehouse_id'),
					'created_on' => date('Y-m-d H:m:s'),
					'is_read' => 0,
					'order_item_id' => $order_item_id ,
				);
            }
        }    
		$result = $this->site->create_notification($notification_array);
        
    }	
  }
  function checkCustomerDiscount(){
    $billid = $this->input->post('bill_id');
    if($result = $this->pos_model->getCustomerDiscount($billid)){
	$dis_result = $this->pos_model->getAllCustomerDiscount();
	echo json_encode(array('cus_dis'=>$result,'all_dis'=>$dis_result));exit;
    }
    
    //echo 'no_discount';exit;
    echo json_encode(array('no_discount'=>'no_discount'));exit;
    
  }
  public function updateBillDetails(){
  	$billid = $this->input->post('bill_id');
  	$dis_id = $this->input->post('dis_id');
  	if($result = $this->pos_model->getCustomerDiscount($billid)){
	  	$return =  $this->pos_model->update_bill_withcustomer_discount($billid,$dis_id);
	  	$return = $this->sma->formatDecimal($return);
	  	echo json_encode(array('amount'=> $return));exit;
	  	// echo json_encode($return);exit;
  	}
  
 	echo json_encode(array('no_discount'=>'no_discount'));exit;
  
  	// $bil_items = $this->pos_model->getBillItemsRecipeID($billid);
  	/*if ($bil_items) {
	    $disamt = 0;
	    foreach ($bil_items as $item){	

	    $inputdis =  $this->pos_model->group_customer_discount_calculation($item->category_id,$item->amount,$dis_id);  */ 	
		/*$recipeDetails = $this->pos_model->getrecipeByID($recipe_id);
		$discount = $this->site->discountMultiple($recipe_id);		
		$price_total = $recipeDetails->cost;
		$finalAmt = $price_total;
		$dis = 0;
		if(!empty($discount)){                           
		    if($discount[2] == 'percentage_discount'){

		        $discount_value = $discount[1].'%';

		    }else{
			    $discount_value =$discount[1];
		    }
		    
		    $dis = $this->site->calculateDiscount($discount_value, $price_total);
		    $finalAmt = $price_total - $dis;
		  }*/
	/*}

  	echo "<pre>";
  	print_r($inputdis);die;
  }*/
}
  function updateBillDetails1(){
    $billid = $this->input->post('bill_id');
    $dis_id = $this->input->post('dis_id');
    $return = [];
    $this->pos_model->updateCustomerDiscount($billid,$dis_id);
    if($result = $this->pos_model->getCustomerDiscount($billid)){
	$getTax = $this->site->getTaxRateByID($result->tax_id);
	$discountVal = ($result->discount_type=="percentage_discount")?$result->value.'%':$result->value;
	
	$discount_amount = $this->site->calculateDiscount($discountVal,$result->total);
	$totalDiscount = $result->total_discount+$discount_amount;
	$totalAmt_afterDiscount = $result->total-$totalDiscount;
	if($result->tax_type==0){
	    $grandTotal = $totalAmt_afterDiscount/(($getTax->rate/100)+1);
	    $totalTax = $totalAmt_afterDiscount-($totalAmt_afterDiscount/(($getTax->rate/100)+1));
	    $amountPayable = $grandTotal+$totalTax;
	    
	}else{
	    $totalTax = $totalAmt_afterDiscount*($getTax->rate/100);
	    $grandTotal = $totalAmt_afterDiscount+$totalTax;
	    $amountPayable = $grandTotal;
	}
	$update_bil['grand_total'] = $this->sma->formatDecimal($grandTotal);
	$update_bil['total_tax'] = $this->sma->formatDecimal($totalTax);
	$update_bil['total_discount'] = $totalDiscount;
	$update_bil['round_total'] =  $this->sma->formatDecimal($grandTotal);
	$return['amount'] = $this->sma->formatDecimal($amountPayable);
	//print_R($update_bil);
	//print_r($return);exit;
	$this->pos_model->update_bil($billid,$update_bil,$discountVal);
	
	echo json_encode($return);exit;
    }
    echo json_encode(array('no_discount'=>'no_discount'));exit;
  }
  
  public function customer_bildetails(){
		$this->sma->checkPermissions('index');

		$user = $this->site->getUser();
				
		$this->data['warehouses'] = NULL;
		$this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
		$this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : NULL;
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->load->view($this->theme . 'pos/customerbildetails', $this->data);
	}
	
	public function  ajaxcustomer_bildetails(){
		
		
		$this->data['sales'] = $this->pos_model->getAllTablesWithCustomerRequest($this->session->userdata('warehouse_id'));
		$this->load->view($this->theme . 'pos/customerbildetails_ajax', $this->data);
	}
  
//  function calculate_customerdiscount(){
//    $recipeids = $this->input->post('recipeids');
//    $reciepe_ids = explode(",", $recipeids);
//    
//    if ($reciepe_ids) {
//                $disamt = 0;
//                foreach ($reciepe_ids as $key => $recipe_id)
//                	$group_id = $this->site->getRecipeGroupId($recipe_id);
//                	$disamt = $this->site->getCalculateCustomerDiscount($group_id);                
//            }die;
//	echo json_encode($return);exit;
//    }

    function calculate_customerdiscount(){
	$recipeids = $this->input->post('recipeids');
	$discountid = $this->input->post('discountid');
	$divide = $this->input->post('divide');
	$discounttype = $this->input->post('discounttype');
	$reciepe_ids = explode(",", $recipeids);
	$recipe =  array();
	$amt =  0;
	if ($reciepe_ids) {
	    $disamt = 0;
	    foreach ($reciepe_ids as $key => $recipe_id){
		$recipeDetails = $this->pos_model->getrecipeByID($recipe_id);
		
		$discount = $this->site->discountMultiple($recipe_id);
		
		$price_total = $recipeDetails->cost;
		// $price_total = $recipeDetails->cost;
		$finalAmt = $price_total;
		//var_dump($finalAmt);
		$dis = 0;
		if(!empty($discount)){                           
		    if($discount[2] == 'percentage_discount'){

		        $discount_value = $discount[1].'%';

		    }else{
			    $discount_value =$discount[1];
		    }
		    
		    $dis = $this->site->calculateDiscount($discount_value, $price_total);
		    $finalAmt = $price_total - $dis;
		    
		}
		
		/********* offer discount *****************/
		$TotalDiscount = $this->site->TotalDiscount();
                $offer_dis = 0;
                if(!empty($TotalDiscount) && $TotalDiscount[0] != 0){                                     
                    if($TotalDiscount[3] == 'percentage_discount'){
                        $totdiscount = $TotalDiscount[1].'%';
		    }else{
			$totdiscount =$TotalDiscount[1];
		    }
		    $offerdiscount = $this->site->calculateDiscount($totdiscount, $finalAmt);                                    
		    $offer_dis = $offerdiscount;
		    $finalAmt = $finalAmt - $offer_dis;  
                }        
		/****************          ***************/
		
		
		
		$recipe[$key]['id']  = $recipe_id;
		$recipe[$key]['disamt'] = $this->pos_model->recipe_customer_discount_calculation($recipe_id,$recipeDetails->category_id,$finalAmt,$discountid,$discounttype);
		$amt +=$recipe[$key]['disamt'];
	    }
	}
	
	echo json_encode($amt);exit;
    }
    
    function auto_split_calculate_customerdiscount(){
	$recipeids = $this->input->post('recipeids');
	$discountid = $this->input->post('discountid');
	$divide = $this->input->post('divide');
	$reciepe_ids = explode(",", $recipeids);
	$recipe =  array();
	$$amt =  0;
	if ($reciepe_ids) {
	    $disamt = 0;
	    foreach ($reciepe_ids as $key => $recipe_id){
		$recipeDetails = $this->pos_model->getrecipeByID($recipe_id);
		
		$discount = $this->site->discountMultiple($recipe_id);
		
		$price_total = $recipeDetails->cost;
		// $price_total = $recipeDetails->cost;
		$finalAmt = $price_total;
		//var_dump($finalAmt);
		$dis = 0;
		if(!empty($discount)){                           
		    if($discount[2] == 'percentage_discount'){

		        $discount_value = $discount[1].'%';

		    }else{
			    $discount_value =$discount[1];
		    }
		    
		    $dis = $this->site->calculateDiscount($discount_value, $price_total);
		    $finalAmt = $price_total - $dis;
		    
		}
		
		/********* offer discount *****************/
		$TotalDiscount = $this->site->TotalDiscount();
                $offer_dis = 0;
                if($TotalDiscount[0] != 0){                                     
                    if($TotalDiscount[3] == 'percentage_discount'){
                        $totdiscount = $TotalDiscount[1].'%';
		    }else{
			$totdiscount =$TotalDiscount[1];
		    }
		    $offerdiscount = $this->site->calculateDiscount($totdiscount, $finalAmt);                                    
		    $offer_dis = $offerdiscount;
		    $finalAmt = $finalAmt - $offer_dis;  
                }        
		/****************          ***************/

		/*$recipe[$key]['id']  = $recipe_id;
		$recipe[$key]['disamt'] = $this->pos_model->recipe_customer_discount_calculation($recipe_id,$recipeDetails->category_id,$finalAmt,$discountid);*/
		 $amt += $this->pos_model->recipe_customer_discount_calculation($recipe_id,$recipeDetails->category_id,$finalAmt,$discountid);
	    }
	}
	echo json_encode($amt);exit;
    }    
     public function change_table_number($cancel_remarks = NULL, $sale_id = NULL)
    {        
         $change_split_id = $this->input->post('change_split_id');
         $changed_table_id = $this->input->post('changed_table_id');   
		
         $result = $this->pos_model->change_table($change_split_id, $changed_table_id);

        if($result == true){
           $msg = 'success'; 

        }else{
            $msg = 'error';
            $status = 'error'; 
        }
        $this->sma->send_json(array('msg' => $msg, 'status' => $msg));
    }
     public function change_customer_number($cancel_remarks = NULL, $sale_id = NULL)
    {        
         $change_split_id = $this->input->post('change_split_id');
         $changed_customer_id = $this->input->post('changed_customer_id');   
		
         $result = $this->pos_model->change_customer($change_split_id, $changed_customer_id);

        if($result == true){
           $msg = 'success'; 

        }else{
            $msg = 'error';
            $status = 'error'; 
        }
        $this->sma->send_json(array('msg' => $msg, 'status' => $msg));
    }
    function test(){
	$d_q = $this->db->get_where('deposits', array('company_id' => 35,'credit_balance!='=>0))->result_array();
			$amountpayable = 59.16;
			foreach($d_q as $dep => $depositRow){			    
			    if($amountpayable<=$depositRow['credit_balance']){
				$payableamt = $amountpayable;
				$this->db->set('credit_balance', 'credit_balance-'.$payableamt,false);
				$this->db->set('credit_used', 'credit_used+'.$payableamt,false);
				$this->db->where('id',$depositRow['id']);
				$this->db->update('deposits');//echo 'exit';exit;
				break;
			    }else{
				$payableamt = $depositRow['credit_balance'];
				$this->db->set('credit_balance', 'credit_balance-'.$payableamt,false);
				$this->db->set('credit_used', 'credit_used+'.$payableamt,false);
				$this->db->where('id',$depositRow['id']);
				$this->db->update('deposits');
				$amountpayable = $amountpayable-$payableamt; 
			    }
			}
			print_R($this->db->error());
    }
    function billprint(){
	$data = $this->input->post();
	//echo '<pre>';
	//print_r($data);
	$split_id = $this->input->post('splits');
	
	for($i=1; $i<=$this->input->post('bils'); $i++){
	    
	    foreach($this->input->post('split['.$i.'][recipe_name]') as $k => $row){
		$bill_items['item'][$k]['recipe_name'] = $this->input->post('split['.$i.'][recipe_name]['.$k.']');
		$bill_items['item'][$k]['recipe_price'] = $this->input->post('split['.$i.'][unit_price]['.$k.']');
		$bill_items['item'][$k]['recipe_qty'] = $this->input->post('split['.$i.'][quantity]['.$k.']');
		$bill_items['item'][$k]['recipe_subtotal'] = $this->input->post('split['.$i.'][subtotal]['.$k.']');
	    }
	    
	    $bill_items['item_cnt'] = $this->input->post('split['.$i.'][total_item]');
	    $bill_items['total'] = $this->input->post('split['.$i.'][total_price]');
	    $bill_items['discount'] = $this->input->post('split['.$i.'][discount_amount]') + $this->input->post('split['.$i.'][itemdiscounts]');
	    $bill_items['tax_type'] = $this->input->post('split['.$i.'][tax_type]');
	    if($bill_items['tax_type']==0){
		$bill_items['grand_total'] = $this->input->post('split['.$i.'][grand_total]') + $this->input->post('split['.$i.'][tax_amount]');
	    }else{
		$bill_items['grand_total'] = $this->input->post('split['.$i.'][grand_total]');
	    }
	    
	    $bill_items['biller_id'] = $this->input->post('split['.$i.'][biller_id]');
	    
	    $bill_items['tax_type'] = $this->input->post('split['.$i.'][tax_type]');
	    if($bill_items['tax_type']==0){
		$taxtype = 'Tax Inclusive';
	    }else if($bill_items['tax_type']==1){
		$taxtype = 'Tax Exclusive';
	    }
	    $tax_details = $this->site->getTaxRateByID($this->input->post('split['.$i.'][ptax]'));
	   
	    $bill_items['tax_type'] = $taxtype.$tax_details->name;
	    $bill_items['tax_rate'] = $tax_details->rate;
	    $bill_items['tax'] = $this->input->post('split['.$i.'][tax_amount]');
	    
	}
	$bill_items['date'] = date('Y-m-d H:i:s');
	
	
	$this->db->
		select('r.name table,o.reference_no,c.name customer')
		->from('orders o')
		
		->join('restaurant_tables r','o.table_id = r.id')
		->join('companies c','c.id = o.customer_id')
		->where(array('o.split_id' => $split_id));
	$orders = $this->db->get()->row_array();
	$bill_items['table_name'] = $orders['table'];
	$bill_items['reference_no'] = $orders['reference_no'];
	$bill_items['customer_name'] = $orders['customer'];
	$this->data['bill_items'] = $bill_items;
	$this->load->view($this->theme . 'pos/print_bill', $this->data,false);
    }
}