<?php defined('BASEPATH') or exit('No direct script access allowed');

class Qsr extends MY_Controller
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

        $this->load->admin_model('qsr_model');
        $this->load->admin_model('settings_model');
        $this->load->helper('text');
        $this->pos_settings = $this->qsr_model->getSetting();
        $this->settings = $this->qsr_model->getSettings();
        $this->pos_settings->pin_code = $this->pos_settings->pin_code ? md5($this->pos_settings->pin_code) : NULL;
        $this->data['pos_settings'] = $this->pos_settings;
        $this->data['settings'] = $this->settings;
        $this->session->set_userdata('last_activity', now());
        $this->lang->admin_load('pos', $this->Settings->user_language);
        $this->load->library('form_validation');
    }

    public function sales($warehouse_id = NULL)
    {
        //$this->sma->checkPermissions('index');

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
       // $this->sma->checkPermissions('index');

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
        //$this->sma->checkPermissions();
		$default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
		$this->data['biller'] = $this->site->getCompanyByID($this->pos_settings->default_biller);
        $this->data['customer_discount'] = $this->site->GetAllcostomerDiscounts();
		$default_currency_rate = $default_currency_data->rate;
		$default_currency_symbol = $default_currency_data->symbol;

        $user_group = $this->qsr_model->getUserByID($this->session->userdata('user_id'));
// var_dump($user_group);
        $gp = $this->settings_model->getGroupPermissions($user_group->group_id);

				
        if (!$this->pos_settings->default_biller || !$this->pos_settings->default_customer || !$this->pos_settings->default_category) {
            $this->session->set_flashdata('warning', lang('please_update_settings'));
            admin_redirect('qsr/settings');
        }
		
		$this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : NULL;

		$this->data['tax_rates'] = $this->site->getAllTaxRates();
		$currency = $this->site->getAllCurrencies();
		
        $this->data['sid'] = $this->input->get('suspend_id') ? $this->input->get('suspend_id') : $sid;
		
        $did = $this->input->post('delete_id') ? $this->input->post('delete_id') : NULL;
        $suspend = $this->input->post('suspend') ? TRUE : FALSE;

        $count = $this->input->post('count') ? $this->input->post('count') : NULL;

        $duplicate_sale = $this->input->get('duplicate') ? $this->input->get('duplicate') : NULL;
		
        //validate form input
        $this->form_validation->set_rules('customer', $this->lang->line("customer"), 'trim|required');
        $this->form_validation->set_rules('warehouse', $this->lang->line("warehouse"), 'required');
        $this->form_validation->set_rules('biller', $this->lang->line("biller"), 'required');

        if ($this->form_validation->run() == TRUE) {
            /*echo "<pre>";
print_r($this->input->post());die;*/
            $date = date('Y-m-d H:i:s');
            $warehouse_id = $this->input->post('warehouse');
            $customer_id = $this->input->post('customer');
            $biller_id = $this->input->post('biller');
            $total_items = $this->input->post('total_items');
            $sale_status = 'completed';
            $payment_status = 'due';
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
                $item_id = $_POST['recipe_id'][$r];
                $item_type = $_POST['recipe_type'][$r];
                $item_code = $_POST['recipe_code'][$r];
                $item_name = $_POST['recipe_name'][$r];
                $item_comment = $_POST['recipe_comment'][$r];
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
                    $recipe_details = $item_type != 'manual' ? $this->qsr_model->getrecipeByCode($item_code) : NULL;
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

                    $discount = $this->site->discountMultiple($item_id);
                    $price_total = $real_unit_price * $item_unit_quantity;
                     $discount_value = '';
                        if(!empty($discount)){
                           
                            if($discount[2] == 'percentage_discount'){
                              $discount_value = $discount[1].'%';
                            }else{
                                $discount_value =$discount[1];
                            }                            
                             // $price_total = $real_unit_price * $item_unit_quantity;
                             $item_discount = $this->site->calculateDiscount($discount_value, $price_total);                             
                        }else{
                             $item_discount = 0;                                                                                  
                        }
// echo $discount_value;die;
            $offer_dis = 0.0000;            
            if($this->input->post('discount_on_total'))
            {
                /*echo $this->input->post('discount_on_total'); echo "<br>";
                echo $item_discount; echo "<br>";
                echo $item_net_price * $item_unit_quantity-$item_discount;echo "<br>";
                echo $this->input->post('sub_total')-$item_discount;echo "<br>";
                echo $this->input->post('item_discount');echo "<br>";*/
                // echo $this->input->post('sub_total') -$this->input->post('item_discount');die;
               $offer_dis = $this->site->calculate_Discount($this->input->post('discount_on_total'), ($item_net_price * $item_unit_quantity-$item_discount),$this->input->post('sub_total') -$this->input->post('item_discount'));
               // echo $offer_dis;die;
            }         
    if($this->input->post('order_discount_input'))
    {   
            // $subtotal =$postData['split'][$i]['subtotal'][$key];
            $tot_dis1 = 00;//$this->input->post('[split]['.$i.'][tot_dis1]');
            $item_dis = 0;//$postData['split'][$i]['item_dis'][$key];
            
        if($this->Settings->customer_discount=="customer"){
            $recipe_id =  $item_id;
            
            $finalAmt = $price_total - $item_discount -$offer_dis; 
            $customer_discount_status = 'applied';
            $discountid = $this->input->post('order_discount_input');
            $recipeDetails = $this->pos_model->getrecipeByID($recipe_id);
            $group_id =$recipeDetails->category_id;
            $subcategory_id =$recipeDetails->subcategory_id;
            $input_dis = $this->pos_model->recipe_customer_discount_calculation($recipe_id,$group_id,$subcategory_id,$finalAmt,$discountid);
        }else if($this->Settings->customer_discount=="manual"){
            
         $input_dis = $this->site->calculate_Discount($this->input->post('order_discount_input'), (($item_net_price * $item_unit_quantity-$item_discount)-$offer_dis),(($item_net_price * $item_unit_quantity-$item_discount)-$offer_dis));
         /*echo "<pre>";
         print_r($input_dis);*/
         $this->input->post('sub_total');
         $this->input->post('total');
         $this->input->post('item_discount');
         $this->input->post('discount_on_total');
           // var_dump($item_discount);die;
        }
    }else{    
        // echo "string";die;
       $input_dis = 0;
    }

    $getTax = $this->site->getTaxRateByID($this->pos_settings->default_tax);

    if($this->pos_settings->tax_type != 0){              

         $itemtax = $this->site->calculateOrderTax($getTax->id, ($item_net_price * $item_unit_quantity-($offer_dis ? $offer_dis:0) -($input_dis ? $input_dis:0)-($item_discount)));
    }else{
        $itemtax = $this->site->calculateOrderTax($getTax->id, ($item_net_price * $item_unit_quantity-($offer_dis ? $offer_dis:0) -($input_dis ? $input_dis:0)-($item_discount)));
 
       // $final_val = ($item_net_price * $item_unit_quantity-($offer_dis ? $offer_dis:0) -($input_dis ? $input_dis:0)-($item_discount));

        //$subval = $final_val/(($default_tax/$final_val)+1);
/*echo $subval;
 echo "<pre>";
die;*/

       // $getTax = $this->site->getTaxRateByID($getTax->id);

        //$itemtax = ($subval) * ($getTax->rate / 100);
    }     

                    $variant = explode("|",$_POST['variant'][$r]);

                    $recipe = array(
                        'recipe_id'      => $item_id,
                        'recipe_code'    => $item_code,
                        'recipe_name'    => $item_name,
                        'recipe_type'    => $item_type,
                        'option_id'       => $item_option,
                        'addon_id'          => $item_addon,
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
                        'variant' => $_POST['variant'][$r] ? $_POST['variant'][$r] : '',
                        'recipe_variant_id' => $variant[0] ? $variant[0] : 0
                    );
                  
                    $tax_type =  $this->pos_settings->tax_type;

                    $bill_items_recipe = array(
                        'recipe_name' => $item_name,
                        'unit_price' => $this->sma->formatDecimal($real_unit_price + $item_tax),
                        'net_unit_price' => $this->sma->formatDecimal($item_net_price + $item_tax)*$item_quantity,
                        'warehouse_id' => $warehouse_id,
                        'recipe_type' => $item_type,
                        'quantity' => $item_quantity,
                        'recipe_id' => $item_id,
                        'recipe_code' => $item_code,
                        'discount' => $this->input->post('order_discount_input') ? $this->input->post('order_discount_input') : 0,
                        'item_discount' => $item_discount,
                        'off_discount' => $offer_dis ? $offer_dis:0,
                        'input_discount' => $input_dis ? $input_dis:0,
                        'tax_type' => $tax_type, 
                        'tax' => $itemtax,  
                        'subtotal' => $this->sma->formatDecimal($real_unit_price*$item_quantity),             
                        'recipe_variant' => $variant[1] ? $variant[1] : '',
                        'recipe_variant_id' => $variant[0] ? $variant[0] : 0                  
                    );                                 

                    $recipes[] = ($recipe + $gst_data);
                    $bill_recipes[] = ($bill_items_recipe + $gst_data);
                    // $bill_items_recipes[] = ($bill_items_recipe + $gst_data);
                    $total += $this->sma->formatDecimal(($item_net_price * $item_unit_quantity), 4);
                }
            }
            /*echo "<pre>";
  print_r($recipe);
            print_r($recipe);die;*/
            if (empty($recipes)) {
                $this->form_validation->set_rules('recipe', lang("order_items"), 'required');
            } elseif ($this->pos_settings->item_order == 1) {
                krsort($recipes);                
            }


            $order_discount = $this->site->calculateDiscount($this->input->post('discount'), ($total + $recipe_tax));
            $total_discount = $this->sma->formatDecimal(($order_discount + $recipe_discount), 4);
            $order_tax = $this->site->calculateOrderTax($this->input->post('order_tax'), ($total + $recipe_tax - $total_discount));
            $total_tax = $this->sma->formatDecimal(($recipe_tax + $order_tax), 4);
            $grand_total = $this->sma->formatDecimal(($total + $total_tax + $this->sma->formatDecimal($shipping) - $order_discount), 4);
            $rounding = 0;
            if ($this->pos_settings->rounding) {
                $round_total = $this->sma->roundNumber($grand_total, $this->pos_settings->rounding);
                $rounding = $this->sma->formatDecimal($round_total - $grand_total);
            }

            $data = array('date'  => $date,
                'reference_no'      => date('YmdHis'),
                'sales_split_id'    => 'SPILT'.date('YmdHis'),
                'customer_id'       => $customer_id,
                'customer'          => $customer,
                'biller_id'         => $biller_id,
                'biller'            => $biller,
                'warehouse_id'      => $warehouse_id,
                'note'              => $note,
                'staff_note'        => $staff_note,
                'total'             => $this->input->post('sub_total'),
                'recipe_discount'   => $recipe_discount,
                'order_discount_id' => $this->input->post('discount'),
                'order_discount'    => $order_discount,
                'total_discount'    =>$this->input->post('discount') ? $this->input->post('discount') : 0,
                'recipe_tax'        => $recipe_tax,
                'order_tax_id'      => $this->input->post('order_tax'),
                'order_tax'         => $order_tax,
                'total_tax'         => $total_tax,
                'shipping'          => $this->sma->formatDecimal($shipping),
                'grand_total'       => $grand_total,
                'total_items'       => $total_items,
                'sale_status'       => 'Process',
                'payment_status'    => $payment_status,
                'payment_term'      => $payment_term,
                'rounding'          => $rounding,
                'suspend_note'      => $this->input->post('suspend_note'),
                'pos'               => 1,
                'paid'              => $this->input->post('amount-paid') ? $this->input->post('amount-paid') : 0,
                'created_by'        => $this->session->userdata('user_id'),
                'hash'              => hash('sha256', microtime() . mt_rand()),
                );

             $order_data = array('date'  => $date,
                'reference_no'      => date('YmdHis'),
                'split_id'          => 'SPILT'.date('YmdHis'),
                'customer_id'       => $customer_id,
                'customer'          => $customer,
                'biller_id'         => $biller_id,
                'biller'            => $biller,
                'warehouse_id'      => $warehouse_id,
                'note'              => $note,
                'staff_note'        => $staff_note,
                'total'             => $this->input->post('sub_total'),
                'recipe_discount'  => $recipe_discount,
                'order_discount_id' => $this->input->post('discount'),
                'order_discount'    => $order_discount,
                'total_discount'    => $this->input->post('discount') ? $this->input->post('discount') : 0,
                'recipe_tax'       => $recipe_tax,
                'order_tax_id'      => $this->input->post('order_tax'),
                'order_tax'         => $order_tax,
                'total_tax'         => $total_tax,
                'shipping'          => $this->sma->formatDecimal($shipping),
                'grand_total'       => $grand_total,
                'total_items'       => $total_items,
                'sale_status'       => 'Process',
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
            
          
/*echo "<pre>";
var_dump($suspend);die;*/
 /*var_dump($suspend);die;*/
            if ($suspend == TRUE) {
				
				$balance = $this->input->post('balance_final_amount') ?  $this->input->post('balance_final_amount') : 0;
                $dueamount = $this->input->post('due_amount') ?  $this->input->post('due_amount') : 0; 
                $total = $this->input->post('total') ?  $this->input->post('total') : 0; 
                $total_pay =  $total - $balance;
				$paid = $total - $dueamount;
                $p = isset($_POST['paid_by']) ? sizeof($_POST['paid_by']) : 0; 
				
				$default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
								
				for ($r = 0; $r < $p; $r++) {

                    if($_POST['amount_KHR'][$r] == '' && $_POST['amount_USD'][$r] == '')
                    {
                           unset($link);
                    }else{
					    foreach($currency as $currency_row){	
    						if($currency_row->rate == $default_currency_data->rate){						
    							$p = isset($_POST['amount_'.$currency_row->code][$r]) ? sizeof($_POST['amount_'.$currency_row->code]) : 0;							
    							$amount = $_POST['amount_'.$currency_row->code][$r];							
    						}else{							
    							$amount_exchange = $_POST['amount_'.$currency_row->code][$r];
    						}
					   }

        				if(!empty($amount) || !empty($amount_exchange)){
            				$payment[] = array(
            					'date'         => date('Y-m-d H:i:s'),					
            					'amount'       => $amount ? $amount:0,
            					'amount_exchange'   => $amount_exchange ? $amount_exchange:0,
            					'pos_paid'     => $_POST['amount_USD'][$r],
            					'pos_balance'  => round($_POST['balance_amount'][$r], 3),
            					'paid_by'     => $_POST['paid_by'][$r],
            					'cc_no'       => $_POST['cc_no'][$r],
                                /* 'cheque_no'   => $_POST['cheque_no'][$r],
            					 'cc_holder'   => $_POST['cc_holer'][$r],
            					 'cc_month'    => $_POST['cc_month'][$r],
            					 'cc_year'     => $_POST['cc_year'][$r],
            					 'cc_type'     => $_POST['cc_type'][$r],					 
            					 'sale_note'   => $_POST['sale_note'] ? $_POST['sale_note'] : '',
            					 'staff_note'   => $_POST['staffnote'] ? $_POST['staffnote'] : '',
            					 'payment_note' => $_POST['payment_note'][$r],*/
            					 'created_by'   => $this->session->userdata('user_id'),
            					 'type'         => 'received',
            				);
        				}				
				
        				foreach($currency as $currency_row){						
        					if($default_currency_data->code == $currency_row->code){
        						if(!empty($_POST['amount_'.$currency_row->code][$r])){
        							$multi_currency[] = array(								
        								'currency_id' => $currency_row->id,
        								'currency_rate' => $currency_row->rate,
        								'amount' => $_POST['amount_'.$currency_row->code][$r],
        							);
        						}
        					}else{
        						if(!empty($_POST['amount_'.$currency_row->code][$r])){
        							$multi_currency[] = array(								
        								'currency_id' => $currency_row->id,
        								'currency_rate' => $currency_row->rate,
        								'amount' => $_POST['amount_'.$currency_row->code][$r],
        							);
        						}
        					}
        			    }
				
			         }
	           }		
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
					'sale_status'			=> "Closed",
					'default_currency_code' => $default_currency_data->code,
					'default_currency_rate' => $default_currency_data->rate,
                );
				
				
            }else{
                /*echo "<pre>";
                print_r($this->input->post());die;*/
                $balance = $this->input->post('balance_final_amount') ?  $this->input->post('balance_final_amount') : 0;
                $dueamount = $this->input->post('due_amount') ?  $this->input->post('due_amount') : 0; 
                $total = $this->input->post('total') ?  $this->input->post('total') : 0; 

                $total_pay =  $total - $balance;                
                $paid = $total - $dueamount;
                
                $p = isset($_POST['paid_by']) ? sizeof($_POST['paid_by']) : 0; 
                
                $default_currency_data = $this->site->getCurrencyByID($this->Settings->default_currency);
                                
                for ($r = 0; $r < $p; $r++) {
                    if($_POST['amount_KHR'][$r] == '' && $_POST['amount_USD'][$r] == '')
                        {
                            unset($link);
                        }else{
                            foreach($currency as $currency_row){                        
                                                        
                                if($currency_row->rate == $default_currency_data->rate){
                                
                                    $p = isset($_POST['amount_'.$currency_row->code][$r]) ? sizeof($_POST['amount_'.$currency_row->code]) : 0;
                                    $amount = $_POST['amount_'.$currency_row->code][$r];
                                    
                                }else{
                                    $amount_exchange = $_POST['amount_'.$currency_row->code][$r];                            
                                }
                            }
                
                            if(!empty($amount) || !empty($amount_exchange)){
                                $payment[] = array(
                                    'date'         => date('Y-m-d H:i:s'),                    
                                    'amount'       => $amount ? $amount:0,
                                    'amount_exchange'   => $amount_exchange ? $amount_exchange:0,
                                    'pos_paid'     => $_POST['amount_USD'][$r],
                                    'pos_balance'  => round($_POST['balance_amount'][$r], 3),
                                    'paid_by'     => $_POST['paid_by'][$r],
                                    'cc_no'       => $_POST['cc_no'][$r],
                                    /*'cheque_no'   => $_POST['cheque_no'][$r],
                                    'cc_holder'   => $_POST['cc_holer'][$r],
                                    'cc_month'    => $_POST['cc_month'][$r],
                                    'cc_year'     => $_POST['cc_year'][$r],
                                    'cc_type'     => $_POST['cc_type'][$r],                     
                                    'sale_note'   => $_POST['sale_note'] ? $_POST['sale_note'] : '',
                                    'staff_note'   => $_POST['staffnote'] ? $_POST['staffnote'] : '',
                                    'payment_note' => $_POST['payment_note'][$r],*/
                                    'created_by'   => $this->session->userdata('user_id'),
                                    'type'         => 'received',
                                );
                            }
                
                            foreach($currency as $currency_row){
                                    
                                if($default_currency_data->code == $currency_row->code){
                                    if(!empty($_POST['amount_'.$currency_row->code][$r])){
                                        $multi_currency[] = array(
                                            'currency_id' => $currency_row->id,
                                            'currency_rate' => $currency_row->rate,
                                            'amount' => $_POST['amount_'.$currency_row->code][$r],
                                        );
                                    }  
                                }else{
                                    if(!empty($_POST['amount_'.$currency_row->code][$r])){
                                        $multi_currency[] = array(                                
                                            'currency_id' => $currency_row->id,
                                            'currency_rate' => $currency_row->rate,
                                            'amount' => $_POST['amount_'.$currency_row->code][$r],
                                        );
                                    }
                                }
                            }  
                        }
                } 
                $taxation = $this->input->post('taxation') ? $this->input->post('taxation') :0;
                $update_bill = array(
                    'updated_at'            => date('Y-m-d H:i:s'),
                    'created_by'            => $this->session->userdata('user_id'),
                    'total_pay'             => $total_pay,
                    'balance'               => $balance,
                    'paid'                  => $paid,
                    'payment_status'        => 'Completed',
                    'default_currency_code' => $default_currency_data->code,
                    'default_currency_rate' => $default_currency_data->rate,
                    'table_whitelisted'     => $taxation,
                    );

                $sales_bill = array(
                    'grand_total'           => $total,              
                    'paid'                  => $paid,
                    'payment_status'        =>'Paid',
                    'sale_status'           => "Closed",
                    'default_currency_code' => $default_currency_data->code,
                    'default_currency_rate' => $default_currency_data->rate,
                );
            }
            /*if (!isset($payment) || empty($payment)) {
                $payment = array();
            }*/
             //$this->sma->print_arrays($data, $recipes, $payment, $update_bill, $sales_bill);			 
        }
        /*echo "<pre>";
        print_r($payment);
        echo "<pre>";
        print_r($bill_recipes);
        print_r($this->input->post());
        die;*/
        if ($this->form_validation->run() == TRUE && !empty($recipes) && !empty($data)) {
            if ($suspend == TRUE ) {
            // var_dump($did);die;
                if ($this->qsr_model->suspendSale($data, $recipes, $did)) {
                    $this->session->set_userdata('remove_posls', 1);
                    $this->session->set_flashdata('message', $this->lang->line("sale_suspended"));
                    admin_redirect("qsr");
                }
            } else {
                $loyalty_used_points = $this->input->post('loyalty_used_points') ? $this->input->post('loyalty_used_points') : 0;
                if ($sale = $this->qsr_model->addInsertbil($data, $recipes, $payment, $update_bill, $sales_bill, $multi_currency, $did,$order_data, $bill_recipes,$total,$customer_id,$loyalty_used_points,$taxation)) {
					
                    $this->session->set_userdata('remove_posls', 1);
                    //$msg = $this->lang->line("QSR Sale successfully added");
                    if (!empty($sale['message'])) {
                        foreach ($sale['message'] as $m) {
                            $msg .= '<br>' . $m;
                        }
                    }
                   // $this->session->set_flashdata('message', $msg);
                    $redirect_to = $this->pos_settings->after_sale_page ? "pos" : "qsr/view_bill/" . $sale['sale_id'];
                    if ($this->pos_settings->auto_print) {
                        if ($this->Settings->remote_printing != 1) {
                            $redirect_to .= '?print='.$sale['sale_id'];
                        }
                    }
                    admin_redirect($redirect_to);
                }
            }
        } else {
            $this->data['old_sale'] = NULL;
            $this->data['oid'] = NULL;
            if ($duplicate_sale) {
                if ($old_sale = $this->qsr_model->getInvoiceByID($duplicate_sale)) {
                    $inv_items = $this->qsr_model->getSaleItems($duplicate_sale);
                    $this->data['oid'] = $duplicate_sale;
                    $this->data['old_sale'] = $old_sale;
                    $this->data['message'] = lang('old_sale_loaded');
                    $this->data['customer'] = $this->qsr_model->getCompanyByID($old_sale->customer_id);
                } else {
                    $this->session->set_flashdata('error', lang("bill_x_found"));
                    admin_redirect("qsr");
                }
            }
            $this->data['suspend_sale'] = NULL;
            if ($sid) {

                if ($suspended_sale = $this->qsr_model->getOpenBillByID($sid)) {
                    $inv_items = $this->qsr_model->getSuspendedSaleItems($sid);

                    $this->data['sid'] = $sid;
                    $this->data['suspend_sale'] = $suspended_sale;
                    $this->data['message'] = lang('suspended_sale_loaded');
                    $this->data['customer'] = $this->qsr_model->getCompanyByID($suspended_sale->customer_id);
                    $this->data['reference_note'] = $suspended_sale->suspend_note;
                } else {
                    $this->session->set_flashdata('error', lang("bill_x_found"));
                    admin_redirect("qsr");
                }
            }

            if (($sid || $duplicate_sale) && $inv_items) {
                /*echo "<pre>";
                print_r($inv_items);
                die;*/
                     // print_r(krsort($inv_items));die;
                    $c = rand(100000, 9999999);
                    foreach ($inv_items as $item) {
                        $row = $this->site->getrecipeByID($item->recipe_id);
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
                       $options = $this->qsr_model->getrecipeOptions($row->id, $item->warehouse_id);
								$addons = $this->qsr_model->getrecipeAddons($row->id);
		
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
                            $combo_items = $this->qsr_model->getrecipeComboItems($row->id, $item->warehouse_id);
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

                $this->data['customer'] = $this->qsr_model->getCompanyByID($this->pos_settings->default_customer);
                $this->data['reference_note'] = NULL;
            }

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['message'] = isset($this->data['message']) ? $this->data['message'] : $this->session->flashdata('message');

            // $this->data['biller'] = $this->site->getCompanyByID($this->pos_settings->default_biller);
            $this->data['billers'] = $this->site->getAllCompanies('biller');
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['tax_rates'] = $this->site->getAllTaxRates();
            $this->data['user'] = $this->site->getUser();
            $this->data["tcp"] = $this->qsr_model->recipe_count($this->pos_settings->default_category, $this->session->userdata('warehouse_id'));
			$this->data['recipe'] = $this->ajaxrecipe($this->pos_settings->default_category, $this->session->userdata('warehouse_id'));
			$this->data['categories'] = $this->site->getAllrecipeCategories();
			$this->data['brands'] = $this->site->getAllBrands();
			$this->data['subcategories'] = $this->site->getrecipeSubCategories($this->pos_settings->default_category);
            $this->data['printer'] = $this->qsr_model->getPrinterByID($this->pos_settings->printer);
            $order_printers = json_decode($this->pos_settings->order_printers);
            $printers = array();
            if (!empty($order_printers)) {
                foreach ($order_printers as $printer_id) {
                    $printers[] = $this->qsr_model->getPrinterByID($printer_id);
                }
            }
            $this->data['order_printers'] = $printers;
            $this->data['pos_settings'] = $this->pos_settings;

            if ($this->pos_settings->after_sale_page && $saleid = $this->input->get('print', true)) {
                if ($inv = $this->qsr_model->getInvoiceByID($saleid)) {
                    $this->load->helper('pos');
                    if (!$this->session->userdata('view_right')) {
                        $this->sma->view_rights($inv->created_by, true);
                    }
                    $this->data['rows'] = $this->qsr_model->getAllInvoiceItems($inv->id);
                    $this->data['biller'] = $this->qsr_model->getCompanyByID($inv->biller_id);
                    $this->data['customer'] = $this->qsr_model->getCompanyByID($inv->customer_id);
                    $this->data['payments'] = $this->qsr_model->getInvoicePayments($inv->id);
                    $this->data['return_sale'] = $inv->return_id ? $this->qsr_model->getInvoiceByID($inv->return_id) : NULL;
                    $this->data['return_rows'] = $inv->return_id ? $this->qsr_model->getAllInvoiceItems($inv->return_id) : NULL;
                    $this->data['return_payments'] = $this->data['return_sale'] ? $this->qsr_model->getInvoicePayments($this->data['return_sale']->id) : NULL;
                    $this->data['inv'] = $inv;
                    $this->data['print'] = $inv->id;
                    $this->data['created_by'] = $this->site->getUser($inv->created_by);
                }
            }
/*echo "<pre>";
echo "sivan";
print_r($this->data['items']);
echo "</pre>";die;*/
            $this->load->view($this->theme . 'qsr/add_newscreen', $this->data);
        }
    }

    public function view_bill($billid = NULL, $modal = NULL)
    {
        //$this->sma->checkPermissions('index');
		
		$this->data['order_item'] = $this->qsr_model->getAllBillitems($billid);
		$this->data['message'] = $this->session->flashdata('message');
		
		$inv = $this->qsr_model->getInvoiceByID($billid);
		$this->load->helper('pos');						
		if (!$this->session->userdata('view_right')) {
			$this->sma->view_rights($inv->created_by, true);
		}        
		$this->data['rows'] = $this->qsr_model->getAllInvoiceItems($billid);
		
		$biller_id = $inv->biller_id;
		$bill_id = $inv->sales_id;
		
		$customer_id = $inv->customer_id;
		$delivery_person_id = $inv->delivery_person_id;
		
		$this->data['inv'] = $inv;
		$this->data['customer'] = $this->qsr_model->getCompanyByID($customer_id);
		
		if($delivery_person_id != 0){
			$this->data['delivery_person'] = $this->qsr_model->getUserByID($delivery_person_id);
		}
		$this->data['created_by'] = $this->site->getUser($inv->created_by);
		$this->data['printer'] = $this->qsr_model->getPrinterByID($this->pos_settings->printer);
		$this->data['biller'] = $this->qsr_model->getCompanyByID($biller_id);
		
		$this->data['payments'] = $this->qsr_model->getInvoicePayments($billid);
		/*echo "<pre>";
		var_du($this->data['payments']);die;*/
		$this->data['return_sale'] = $inv->return_id ? $this->qsr_model->getInvoiceByID($inv->return_id) : NULL;
		$this->data['return_rows'] = $inv->return_id ? $this->qsr_model->getAllInvoiceItems($inv->return_id) : NULL;
		$this->data['return_payments'] = $this->data['return_sale'] ? $this->qsr_model->getInvoicePayments($this->data['return_sale']->id) : NULL;
		$this->data['type'] = $this->input->post('type'); 
		
		
		
        $this->data['tax_rates'] = $this->site->getAllTaxRates();
        $this->load->view($this->theme . 'qsr/view_bill', $this->data);
    }
	
	public function customer_bill()
    {
        //$this->sma->checkPermissions('index');
        $this->data['tax_rates'] = $this->site->getAllTaxRates();
        $this->load->view($this->theme . 'qsr/customer_bill', $this->data);
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
        $this->data['registers'] = $this->qsr_model->getOpenRegisters();
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('pos'), 'page' => lang('pos')), array('link' => '#', 'page' => lang('open_registers')));
        $meta = array('page_title' => lang('open_registers'), 'bc' => $bc);
        $this->page_construct('pos/registers', $meta, $this->data);
    }

    public function open_register()
    {
       // $this->sma->checkPermissions('index');
        $this->form_validation->set_rules('cash_in_hand', lang("cash_in_hand"), 'trim|required|numeric');

        if ($this->form_validation->run() == TRUE) {
            $data = array(
                'date' => date('Y-m-d H:i:s'),
                'cash_in_hand' => $this->input->post('cash_in_hand'),
                'user_id'      => $this->session->userdata('user_id'),
                'status'       => 'open',
                );
        }
        if ($this->form_validation->run() == TRUE && $this->qsr_model->openRegister($data)) {
            $this->session->set_flashdata('message', lang("welcome_to_pos"));
            admin_redirect("pos");
        } else {

            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('open_register')));
            $meta = array('page_title' => lang('open_register'), 'bc' => $bc);
            $this->page_construct('pos/open_register', $meta, $this->data);
        }
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
                $user_register = $user_id ? $this->qsr_model->registerData($user_id) : NULL;
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

        if ($this->form_validation->run() == TRUE && $this->qsr_model->closeRegister($rid, $user_id, $data)) {
            $this->session->set_flashdata('message', lang("register_closed"));
            admin_redirect("welcome");
        } else {
            if ($this->Owner || $this->Admin) {
                $user_register = $user_id ? $this->qsr_model->registerData($user_id) : NULL;
                $register_open_time = $user_register ? $user_register->date : NULL;
                $this->data['cash_in_hand'] = $user_register ? $user_register->cash_in_hand : NULL;
                $this->data['register_open_time'] = $user_register ? $register_open_time : NULL;
            } else {
                $register_open_time = $this->session->userdata('register_open_time');
                $this->data['cash_in_hand'] = NULL;
                $this->data['register_open_time'] = NULL;
            }
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['ccsales'] = $this->qsr_model->getRegisterCCSales($register_open_time, $user_id);
            $this->data['cashsales'] = $this->qsr_model->getRegisterCashSales($register_open_time, $user_id);
            $this->data['chsales'] = $this->qsr_model->getRegisterChSales($register_open_time, $user_id);
            $this->data['gcsales'] = $this->qsr_model->getRegisterGCSales($register_open_time);
            $this->data['pppsales'] = $this->qsr_model->getRegisterPPPSales($register_open_time, $user_id);
            $this->data['stripesales'] = $this->qsr_model->getRegisterStripeSales($register_open_time, $user_id);
            $this->data['authorizesales'] = $this->qsr_model->getRegisterAuthorizeSales($register_open_time, $user_id);
            $this->data['totalsales'] = $this->qsr_model->getRegisterSales($register_open_time, $user_id);
            $this->data['refunds'] = $this->qsr_model->getRegisterRefunds($register_open_time, $user_id);
            $this->data['cashrefunds'] = $this->qsr_model->getRegisterCashRefunds($register_open_time, $user_id);
            $this->data['expenses'] = $this->qsr_model->getRegisterExpenses($register_open_time, $user_id);
            $this->data['users'] = $this->qsr_model->getUsers($user_id);
            $this->data['suspended_bills'] = $this->qsr_model->getSuspendedsales($user_id);
            $this->data['user_id'] = $user_id;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'pos/close_register', $this->data);
        }
    }

   /* public function getrecipeDataByCode($code = NULL, $warehouse_id = NULL)
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
        $row = $this->qsr_model->getWHrecipe($code, $warehouse_id);
        $option = false;
        if ($row) {
            unset($row->cost, $row->details, $row->recipe_details, $row->image, $row->barcode_symbology, $row->cf1, $row->cf2, $row->cf3, $row->cf4, $row->cf5, $row->cf6, $row->supplier1price, $row->supplier2price, $row->cfsupplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1, $row->supplier2, $row->supplier3, $row->supplier4, $row->supplier5, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);
            $row->item_tax_method = $row->tax_method;
            $row->qty = 1;
            $row->discount = '0';
            $row->serial = '';
            $options = $this->qsr_model->getrecipeOptions($row->id, $warehouse_id);
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
            $row->price = $row->price + (($row->price * $customer_group->percent) / 100);
            $row->real_unit_price = $row->price;
            $row->base_quantity = 1;
            $row->base_unit = $row->unit;
            $row->base_unit_price = $row->price;
            $row->unit = $row->sale_unit ? $row->sale_unit : $row->unit;
            $row->comment = '';
            $combo_items = false;
            if ($row->type == 'combo') {
                $combo_items = $this->qsr_model->getrecipeComboItems($row->id, $warehouse_id);
            }
            $units = $this->site->getUnitsByBUID($row->base_unit);
            $tax_rate = $this->site->getTaxRateByID($row->tax_rate);

            $pr = array('id' => str_replace(".", "", microtime(true)), 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'category' => $row->category_id, 'row' => $row, 'combo_items' => $combo_items, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options);

            $this->sma->send_json($pr);
        } else {
            echo NULL;
        }
    }

    public function ajaxrecipes($category_id = NULL, $brand_id = NULL)
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
        if ($this->input->get('per_page') == 'n') {
            $page = 0;
        } else {
            $page = $this->input->get('per_page');
        }

        $this->load->library("pagination");

        $config = array();
        $config["base_url"] = base_url() . "pos/ajaxrecipes";
        $config["total_rows"] = $this->qsr_model->recipes_count($category_id, $subcategory_id, $brand_id);
        $config["per_page"] = $this->pos_settings->pro_limit;
        $config['prev_link'] = FALSE;
        $config['next_link'] = FALSE;
        $config['display_pages'] = FALSE;
        $config['first_link'] = FALSE;
        $config['last_link'] = FALSE;

        $this->pagination->initialize($config);

        $recipes = $this->qsr_model->fetch_recipes($category_id, $config["per_page"], $page, $subcategory_id, $brand_id);
        $pro = 1;
        $prods = '<div>';
        if (!empty($recipes)) {
            foreach ($recipes as $recipe) {
                $count = $recipe->id;
                if ($count < 10) {
                    $count = "0" . ($count / 100) * 100;
                }
                if ($category_id < 10) {
                    $category_id = "0" . ($category_id / 100) * 100;
                }

                $prods .= "<button id=\"recipe-" . $category_id . $count . "\" type=\"button\" value='" . $recipe->code . "' title=\"" . $recipe->name . "\" class=\"btn-prni btn-" . $this->pos_settings->recipe_button_color . " recipe pos-tip\" data-container=\"body\"><img src=\"" . base_url() . "assets/uploads/thumbs/" . $recipe->image . "\" alt=\"" . $recipe->name . "\" class='img-rounded' /><span>" . character_limiter($recipe->name, 40) . "</span></button>";

                $pro++;
            }
        }
        $prods .= "</div>";

        if ($this->input->get('per_page')) {
            echo $prods;
        } else {
            return $prods;
        }
    }*/
	
	public function getrecipeDataByCode($code = NULL, $warehouse_id = NULL)
    {
       // $this->sma->checkPermissions('index');
        /* insted of recipe code used here recipe_id in same parametter $this->input->get('code') */
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
        $row = $this->qsr_model->getWHrecipe($code, $warehouse_id);
        $option = false;
		
		
		
        if ($row) {
            unset($row->cost, $row->details, $row->recipe_details, $row->image, $row->barcode_symbology, $row->cf1, $row->cf2, $row->cf3, $row->cf4, $row->cf5, $row->cf6, $row->supplier1price, $row->supplier2price, $row->cfsupplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1, $row->supplier2, $row->supplier3, $row->supplier4, $row->supplier5, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);
			
			
            $row->item_tax_method = $row->tax_method;
            $row->qty = 1;
            
            $discount = $this->site->discountMultiple($row->id);
            $discount_value = '';
            if(!empty($discount)){                           
                if($discount[2] == 'percentage_discount'){
                  $discount_value = $discount[1].'%';
                }else{
                    $discount_value =$discount[1];
                }                            
                 $price_total = $row->price;
                 $dis = $this->site->calculateDiscount($discount_value, $price_total);                             
                 $row->discount = $dis;                             
            }else{
                 $row->discount = '0';                             
            } 
 /*var_dump($row->price);
 var_dump($row->discount);*/
            $row->serial = '';
            $options = $this->qsr_model->getrecipeOptions($row->id, $warehouse_id);
			$addons = $this->qsr_model->getrecipeAddons($row->id);
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
                $combo_items = $this->qsr_model->getrecipeComboItems($row->id, $warehouse_id);
				
            }
            $units = $this->site->getUnitsByBUID($row->base_unit);
            $tax_rate = $this->site->getTaxRateByID($row->tax_rate);

            $pr = array('id' => str_replace(".", "", microtime(true)), 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'category' => $row->category_id, 'row' => $row, 'combo_items' => $combo_items, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options, 'addons' => $addons);

            $this->sma->send_json($pr);
        } else {
            echo NULL;
        }
    }

    public function getrecipeVarientDataByCode($code = NULL, $warehouse_id = NULL)
    {
       // $this->sma->checkPermissions('index');
        /* insted of recipe code used here recipe_id in same parametter $this->input->get('code') */
        if ($this->input->get('code')) {
            $code = $this->input->get('code', TRUE);
        }
        if ($this->input->get('warehouse_id')) {
            $warehouse_id = $this->input->get('warehouse_id', TRUE);
        }
        if ($this->input->get('customer_id')) {
            $customer_id = $this->input->get('customer_id', TRUE);
        }

        $variant = $this->input->get('variant', TRUE);

        if (!$code) {
            echo NULL;
            die();
        }
        $warehouse = $this->site->getWarehouseByID($warehouse_id);
        $customer = $this->site->getCompanyByID($customer_id);
        $customer_group = $this->site->getCustomerGroupByID($customer->customer_group_id);
        /*$discount_recipe = $this->site->getDiscounts($code);*/
        $row = $this->qsr_model->getWHrecipe($code, $warehouse_id);
        $option = false;
/*var_dump($row);*/
        if ($row) {
            unset($row->cost, $row->details, $row->recipe_details, $row->image, $row->barcode_symbology, $row->cf1, $row->cf2, $row->cf3, $row->cf4, $row->cf5, $row->cf6, $row->supplier1price, $row->supplier2price, $row->cfsupplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1, $row->supplier2, $row->supplier3, $row->supplier4, $row->supplier5, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);
            
            
            $row->item_tax_method = $row->tax_method;
            $row->qty = 1;
            
            $discount = $this->site->discountMultiple($row->id);
            
            $variantData = $this->qsr_model->getVariantData($variant,$row->id);
/*var_dump($variantData);die;*/
            $row->price = $variantData->price;//$row->price;

            $discount_value = '';
            if(!empty($discount)){                           
                if($discount[2] == 'percentage_discount'){
                  $discount_value = $discount[1].'%';
                }else{
                    $discount_value =$discount[1];
                }                            
                 $price_total = $row->price;
                 $dis = $this->site->calculateDiscount($discount_value, $price_total);                             
                 $row->discount = $dis;                             
            }else{
                 $row->discount = '0';                             
            } 
 /*var_dump($row->price);
 var_dump($row->discount);*/
            $row->serial = '';
            $options = $this->qsr_model->getrecipeOptions($row->id, $warehouse_id);
            $addons = $this->qsr_model->getrecipeAddons($row->id);
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

            $variantData = $this->qsr_model->getVariantData($variant,$row->id);
/*var_dump($variantData);die;*/
            $row->price = $variantData->price;//$row->price;
            $row->variant = $variantData->name;
            $row->variant_id = $variantData->attr_id;

            /*$row->price = $row->price;*/
            $row->real_unit_price = $variantData->price;
            $row->base_quantity = 1;
            $row->base_unit = $row->price ;
            $row->base_unit_price = $row->price;
            $row->unit = $row->sale_unit ? $row->sale_unit : $row->unit;
            $row->comment = '';
            $combo_items = false;
            if ($row->type == 'combo') {
                $combo_items = $this->qsr_model->getrecipeComboItems($row->id, $warehouse_id);
                
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
		
		
        //$this->sma->checkPermissions('index');
		
		
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
        $config["total_rows"] = $this->qsr_model->recipe_count($category_id, $warehouse_id, $subcategory_id, $brand_id);
		
        $config["per_page"] = $this->pos_settings->pro_limit;
		
        $config['prev_link'] = FALSE;
        $config['next_link'] = FALSE;
        $config['display_pages'] = FALSE;
        $config['first_link'] = FALSE;
        $config['last_link'] = FALSE;

        $this->pagination->initialize($config);
		
		
		
        $recipe = $this->qsr_model->fetch_recipe($category_id, $warehouse_id, $config["per_page"], $page, $subcategory_id, $brand_id);
		
       		
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
				
                $class = '';
                $vari ='';
                $varients = false;

                $varients = $this->qsr_model->isVarientExist($recipe->id);  
                /*var_dump($varients);die;*/
                 if(!empty($varients)){
                    $class= "has-varients";
                    $vari = '<div class="variant-popup" style="display: none;">';
                    foreach($varients as $k => $varient){
                        $vari .= '<button data-id="'.$varient->variant_id.'" id="recipe-'.$category_id . $count.'" type="button" value="'.$recipe->id .'" title="" class="btn-default  recipe-varient pos-tip" data-container="body" data-original-title="'.$varient->name.'" tabindex="-1">';

                        if(strlen($varient->name) < 20){    
                                 $vari .= "<span class='name_strong'>" .$varient->name. "</span>";
                            }else{
                                $vari .='<marquee class="name_strong" behavior="alternate" direction="left" scrollamount="1">&nbsp;&nbsp;'.$varient->name.'&nbsp;&nbsp;</marquee>';
                          }
                             $vari .='<br>
                             <span class="price_strong"> '.$default_currency_symbol.$this->sma->formatDecimal($varient->price).'</span> </button>';
                    }
                    $vari .= '</div>';
                }
				
               /* $prods .= "<span><button id=\"recipe-" . $category_id . $count . "\" type=\"button\" value='" . $recipe->id . "' title=\"" . $recipe->name . "\" class=\"btn-prni btn-" . $this->pos_settings->recipe_button_color . " ".$class." recipe pos-tip\" data-container=\"body\"><img src=\"" . base_url() . "assets/uploads/thumbs/" . $recipe->image . "\" alt=\"" . $recipe->name . "\" class='img-rounded' />";*/

                $prods .= "<span><button id=\"recipe-" . $category_id . $count . "\" type=\"button\" value='" . $recipe->id . "' title=\"" . $recipe->name . "\" class=\"btn-prni btn-" . $this->pos_settings->recipe_button_color ." ".$class." recipe pos-tip\" data-container=\"body\">";

				
				if(strlen($recipe->name) < 20){		
						
					$prods .= "<span class='name_strong'>" .$recipe->name. "</span>";
				}else{
					$prods .= "<marquee class='name_strong' behavior='alternate' direction='left' scrollamount='1'>&nbsp;&nbsp;" .$recipe->name. "&nbsp;&nbsp;</marquee>";
				}
				
				$prods .=  "<br><span class='price_strong'> ".$default_currency_symbol ."" . $this->sma->formatDecimal($recipe->price). "</span>".$buyvalue." </button>";
                $prods .=$vari.'</span>';

                $pro++;
            }
        }
        $prods .= "</div>";

        /*if ($this->input->get('per_page')) {*/
            if ($this->input->get('per_page')  != NULL) {
            echo $prods;
        } else {
            return $prods;
        }
    }

	public function ajaxcategorydata($category_id = NULL)
    {
       // $this->sma->checkPermissions('index');
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

               /* $scats .= "<button id=\"subcategory-" . $category->id . "\" type=\"button\" value='" . $category->id . "' class=\"btn-prni subcategory\" ><img src=\"" . base_url() ."assets/uploads/thumbs/" . ($category->image ? $category->image : 'no_image.png') . "\" class='img-rounded img-thumbnail' /><span>" . $category->name . "</span></button>";*/
                $scats .= "<button id=\"subcategory-" . $category->id . "\" type=\"button\" value='" . $category->id . "' class=\"btn-prni subcategory\" >";
                if(strlen($subcategory_name) < 20){     
                
                    $scats .= "<span class='name_strong'>" .$subcategory_name. "</span>";
                }else{
                    $scats .= "<marquee class='sub_strong' behavior='alternate' direction='left' scrollamount='1'>&nbsp;&nbsp;&nbsp;&nbsp;" .$subcategory_name. "&nbsp;&nbsp;&nbsp;&nbsp;</marquee>";
                }
                  $scats .=  "</button>";
            }
        }

        $recipe = $this->ajaxrecipe($category_id, $this->session->userdata('warehouse_id'));

        if (!($tcp = $this->qsr_model->recipe_count($category_id, $this->session->userdata('warehouse_id')))) {
            $tcp = 0;
        }

        $this->sma->send_json(array('recipe' => $recipe, 'subcategories' => $scats, 'tcp' => $tcp));
    }
	
   /* public function ajaxcategorydata($category_id = NULL)
    {
        $this->sma->checkPermissions('index');
        if ($this->input->get('category_id')) {
            $category_id = $this->input->get('category_id');
        } else {
            $category_id = $this->pos_settings->default_category;
        }

        $subcategories = $this->site->getSubCategories($category_id);
        $scats = '';
        if ($subcategories) {
            foreach ($subcategories as $category) {
                $scats .= "<button id=\"subcategory-" . $category->id . "\" type=\"button\" value='" . $category->id . "' class=\"btn-prni subcategory\" ><img src=\"" . base_url() ."assets/uploads/thumbs/" . ($category->image ? $category->image : 'no_image.png') . "\" class='img-rounded img-thumbnail' /><span>" . $category->name . "</span></button>";
            }
        }

        $recipes = $this->ajaxrecipes($category_id);

        if (!($tcp = $this->qsr_model->recipes_count($category_id))) {
            $tcp = 0;
        }

        $this->sma->send_json(array('recipes' => $recipes, 'subcategories' => $scats, 'tcp' => $tcp));
    }*/

    public function ajaxbranddata($brand_id = NULL)
    {
        $this->sma->checkPermissions('index');
        if ($this->input->get('brand_id')) {
            $brand_id = $this->input->get('brand_id');
        }

        $recipes = $this->ajaxrecipes(FALSE, $brand_id);

        if (!($tcp = $this->qsr_model->recipes_count(FALSE, FALSE, $brand_id))) {
            $tcp = 0;
        }

        $this->sma->send_json(array('recipes' => $recipes, 'tcp' => $tcp));
    }

    /* ------------------------------------------------------------------------------------ */

    public function view($sale_id = NULL, $modal = NULL)
    {
       // $this->sma->checkPermissions('index');
        if ($this->input->get('id')) {
            $sale_id = $this->input->get('id');
        }
        $this->load->helper('pos');
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['message'] = $this->session->flashdata('message');
        $inv = $this->qsr_model->getInvoiceByID($sale_id);
        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($inv->created_by, true);
        }
        $this->data['rows'] = $this->qsr_model->getAllInvoiceItems($sale_id);
        $biller_id = $inv->biller_id;
        $customer_id = $inv->customer_id;
        $this->data['biller'] = $this->qsr_model->getCompanyByID($biller_id);
        $this->data['customer'] = $this->qsr_model->getCompanyByID($customer_id);
        $this->data['payments'] = $this->qsr_model->getInvoicePayments($sale_id);
        $this->data['pos'] = $this->qsr_model->getSetting();
        $this->data['barcode'] = $this->barcode($inv->reference_no, 'code128', 30);
        $this->data['return_sale'] = $inv->return_id ? $this->qsr_model->getInvoiceByID($inv->return_id) : NULL;
        $this->data['return_rows'] = $inv->return_id ? $this->qsr_model->getAllInvoiceItems($inv->return_id) : NULL;
        $this->data['return_payments'] = $this->data['return_sale'] ? $this->qsr_model->getInvoicePayments($this->data['return_sale']->id) : NULL;
        $this->data['inv'] = $inv;
        $this->data['sid'] = $sale_id;
        $this->data['modal'] = $modal;
        $this->data['created_by'] = $this->site->getUser($inv->created_by);
        $this->data['printer'] = $this->qsr_model->getPrinterByID($this->pos_settings->printer);
        $this->data['page_title'] = $this->lang->line("invoice");
        $this->load->view($this->theme . 'pos/view', $this->data);
    }

    public function register_details()
    {
       // $this->sma->checkPermissions('index');
        $register_open_time = $this->session->userdata('register_open_time');
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['ccsales'] = $this->qsr_model->getRegisterCCSales($register_open_time);
        $this->data['cashsales'] = $this->qsr_model->getRegisterCashSales($register_open_time);
        $this->data['chsales'] = $this->qsr_model->getRegisterChSales($register_open_time);
        $this->data['gcsales'] = $this->qsr_model->getRegisterGCSales($register_open_time);
        $this->data['pppsales'] = $this->qsr_model->getRegisterPPPSales($register_open_time);
        $this->data['stripesales'] = $this->qsr_model->getRegisterStripeSales($register_open_time);
        $this->data['authorizesales'] = $this->qsr_model->getRegisterAuthorizeSales($register_open_time);
        $this->data['totalsales'] = $this->qsr_model->getRegisterSales($register_open_time);
        $this->data['refunds'] = $this->qsr_model->getRegisterRefunds($register_open_time);
        $this->data['expenses'] = $this->qsr_model->getRegisterExpenses($register_open_time);
        $this->load->view($this->theme . 'pos/register_details', $this->data);
    }

    public function today_sale()
    {
        if (!$this->Owner && !$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            $this->sma->md();
        }

        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['ccsales'] = $this->qsr_model->getTodayCCSales();
        $this->data['cashsales'] = $this->qsr_model->getTodayCashSales();
        $this->data['chsales'] = $this->qsr_model->getTodayChSales();
        $this->data['pppsales'] = $this->qsr_model->getTodayPPPSales();
        $this->data['stripesales'] = $this->qsr_model->getTodayStripeSales();
        $this->data['authorizesales'] = $this->qsr_model->getTodayAuthorizeSales();
        $this->data['totalsales'] = $this->qsr_model->getTodaySales();
        $this->data['refunds'] = $this->qsr_model->getTodayRefunds();
        $this->data['expenses'] = $this->qsr_model->getTodayExpenses();
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
        return admin_url('recipes/gen_barcode/' . $text . '/' . $bcs . '/' . $height);
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

        if ($this->form_validation->run() == TRUE) {

            $data = array(
                'pro_limit'                 => $this->input->post('pro_limit'),
                'pin_code'                  => $this->input->post('pin_code') ? $this->input->post('pin_code') : NULL,
                'default_category'          => $this->input->post('category'),
                'default_customer'          => $this->input->post('customer'),
                'default_biller'            => $this->input->post('biller'),
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

        if ($this->form_validation->run() == TRUE && $this->qsr_model->updateSetting($data)) {
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

            $this->data['pos'] = $this->qsr_model->getSetting();
            $this->data['categories'] = $this->site->getAllCategories();
            //$this->data['customer'] = $this->qsr_model->getCompanyByID($this->pos_settings->default_customer);
            $this->data['billers'] = $this->qsr_model->getAllBillerCompanies();
            $this->config->load('payment_gateways');
            $this->data['stripe_secret_key'] = $this->config->item('stripe_secret_key');
            $this->data['stripe_publishable_key'] = $this->config->item('stripe_publishable_key');
            $authorize = $this->config->item('authorize');
            $this->data['api_login_id'] = $authorize['api_login_id'];
            $this->data['api_transaction_key'] = $authorize['api_transaction_key'];
            $this->data['APIUsername'] = $this->config->item('APIUsername');
            $this->data['APIPassword'] = $this->config->item('APIPassword');
            $this->data['APISignature'] = $this->config->item('APISignature');
            $this->data['printers'] = $this->qsr_model->getAllPrinters();
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

        $config['base_url'] = admin_url('qsr/opened_bills');
        $config['total_rows'] = $this->qsr_model->bills_count();
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
        $bills = $this->qsr_model->fetch_bills($config['per_page'], $per_page);
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
        echo $this->load->view($this->theme . 'qsr/opened', $data, TRUE);

    }

    public function delete($id = NULL)
    {

       // $this->sma->checkPermissions('index');

        if ($this->qsr_model->deleteBill($id)) {
            $this->sma->send_json(array('error' => 0, 'msg' => lang("suspended_sale_deleted")));
        }
    }

    public function email_receipt($sale_id = NULL)
    {
       // $this->sma->checkPermissions('index');
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

        $this->data['rows'] = $this->qsr_model->getAllInvoiceItems($sale_id);
        $inv = $this->qsr_model->getInvoiceByID($sale_id);
        $biller_id = $inv->biller_id;
        $customer_id = $inv->customer_id;
        $this->data['biller'] = $this->qsr_model->getCompanyByID($biller_id);
        $this->data['customer'] = $this->qsr_model->getCompanyByID($customer_id);

        $this->data['payments'] = $this->qsr_model->getInvoicePayments($sale_id);
        $this->data['pos'] = $this->qsr_model->getSetting();
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
                $sale = $this->qsr_model->getInvoiceByID($this->input->post('sale_id'));
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

        if ($this->form_validation->run() == TRUE && $msg = $this->qsr_model->addPayment($payment, $customer_id)) {
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

            $sale = $this->qsr_model->getInvoiceByID($id);
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
        $this->form_validation->set_rules('envato_username', lang("envato_username"), 'required');
        if ($this->form_validation->run() == TRUE) {
            $this->db->update('pos_settings', array('purchase_code' => $this->input->post('purchase_code', TRUE), 'envato_username' => $this->input->post('envato_username', TRUE)), array('pos_id' => 1));
            admin_redirect('pos/updates');
        } else {
            $fields = array('version' => $this->pos_settings->version, 'code' => $this->pos_settings->purchase_code, 'username' => $this->pos_settings->envato_username, 'site' => base_url());
            $this->load->helper('update');
            $protocol = is_https() ? 'https://' : 'http://';
            $updates = get_remote_contents($protocol . 'api.tecdiary.com/v1/update/', $fields);
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
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect("pos");
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['page_title'] = lang('printers');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('pos'), 'page' => lang('pos')), array('link' => '#', 'page' => lang('printers')));
        $meta = array('page_title' => lang('list_printers'), 'bc' => $bc);
        $this->page_construct('pos/printers', $meta, $this->data);
    }

    function get_printers()
    {
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            $this->sma->md();
        }

        $this->load->library('datatables');
        $this->datatables
        ->select("id, title, type, profile, path, ip_address, port")
        ->from("printers")
        ->add_column("Actions", "<div class='text-center'> <a href='" . admin_url('pos/edit_printer/$1') . "' class='btn-warning btn-xs tip' title='".lang("edit_printer")."'><i class='fa fa-edit'></i></a> <a href='#' class='btn-danger btn-xs tip po' title='<b>" . lang("delete_printer") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('pos/delete_printer/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", "id")
        ->unset_column('id');
        echo $this->datatables->generate();

    }

    function add_printer()
    {

        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect("pos");
        }

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

        if ( $this->form_validation->run() == true && $cid = $this->qsr_model->addPrinter($data)) {

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

        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect("pos");
        }
        if($this->input->get('id')) { $id = $this->input->get('id', TRUE); }

        $printer = $this->qsr_model->getPrinterByID($id);
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

        if ( $this->form_validation->run() == true && $this->qsr_model->updatePrinter($id, $data)) {

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
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            $this->sma->md();
        }

        if ($this->input->get('id')) { $id = $this->input->get('id', TRUE); }

        if ($this->qsr_model->deletePrinter($id)) {
            $this->sma->send_json(array('error' => 0, 'msg' => lang("printer_deleted")));
        }

    }
    function calculate_customerdiscount(){
    $recipeids = $this->input->post('recipeids');
    $recipeqtys = $this->input->post('recipeqtys');
    $discountid = $this->input->post('discountid');
    /*$divide = $this->input->post('divide');    */
    /*$reciepe_ids = explode(",", $recipeids);
    $reciepe_qtys = explode(",", $recipeqtys);*/
    $recipe =  array();
    $amt =  0;
   /* print_r($recipeqtys);
    var_dump($recipeqtys);die;*/
    if ($recipeids) {
/*echo "string";die;*/
        $disamt = 0;
        foreach ($recipeids as $key => $recipe_id){
        $recipeDetails = $this->site->getrecipeByID($recipe_id);
        
        $discount = $this->site->discountMultiple($recipe_id);
        $current_qty = $recipeqtys[$key];
        $price_total = $recipeDetails->cost;
        // $price_total = $recipeDetails->cost;
        $finalAmt = $price_total*$current_qty;
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
        $subgroup_id =$recipeDetails->subcategory_id;
        $recipe[$key]['disamt'] = $this->qsr_model->recipe_customer_discount_calculation($recipe_id,$recipeDetails->category_id,$subgroup_id,$finalAmt,$discountid);
        $amt +=$recipe[$key]['disamt'];
        }
    }
    
    echo json_encode($amt);exit;
    }
    public function getTotalDiscount()
    {
        $value = $this->input->post('value');
        $offer_dis =0;
        $TotalDiscount = $this->site->TotalDiscount(); 
            if($TotalDiscount[0] != 0)
                {                                     
                 if($TotalDiscount[3] == 'percentage_discount'){
                        $totdiscount = $TotalDiscount[1].'%';
                    }else{
                        $totdiscount =$TotalDiscount[1];
                    }
                    if($TotalDiscount[2]  <= $value){
                        $totdiscount1 = $this->site->calculateDiscount($totdiscount, $value);                   
                        $offer_dis = $totdiscount1;  
                    }                  
                }   
        $this->sma->send_json($offer_dis);                        
    }    
    public function reprinter(){
        $start = $this->input->get('date'); 
        if($start){
            $start = $start;
        }
        else{
            $start =  date('Y-m-d');
        }       
        $this->data['sales'] = $this->qsr_model->getAllBillingforReprint($start);      
        // print_r($this->data['sales']);die;     
        $this->load->view($this->theme . 'qsr/bill_reprint', $this->data);
    }  
public function reprint_view($billid = NULL, $modal = NULL)
    {
        //$this->sma->checkPermissions('index');
        $billid = $this->input->get('bill_id');
        
        $this->data['order_item'] = $this->qsr_model->getAllBillitems($billid);
        $this->data['message'] = $this->session->flashdata('message');
        
        $inv = $this->qsr_model->getInvoiceByID($billid);
        $this->load->helper('pos');                     
        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($inv->created_by, true);
        }        
        $this->data['rows'] = $this->qsr_model->getAllInvoiceItems($billid);
        
        $biller_id = $inv->biller_id;
        $bill_id = $inv->sales_id;
        
        $customer_id = $inv->customer_id;
        $delivery_person_id = $inv->delivery_person_id;
        
        $this->data['inv'] = $inv;
        $this->data['customer'] = $this->qsr_model->getCompanyByID($customer_id);
        
        if($delivery_person_id != 0){
            $this->data['delivery_person'] = $this->qsr_model->getUserByID($delivery_person_id);
        }
        $this->data['created_by'] = $this->site->getUser($inv->created_by);
        $this->data['printer'] = $this->qsr_model->getPrinterByID($this->pos_settings->printer);
        $this->data['biller'] = $this->qsr_model->getCompanyByID($biller_id);
        
        $this->data['payments'] = $this->qsr_model->getInvoicePayments($billid);        
        $this->data['return_sale'] = $inv->return_id ? $this->qsr_model->getInvoiceByID($inv->return_id) : NULL;
        $this->data['return_rows'] = $inv->return_id ? $this->qsr_model->getAllInvoiceItems($inv->return_id) : NULL;
        $this->data['return_payments'] = $this->data['return_sale'] ? $this->qsr_model->getInvoicePayments($this->data['return_sale']->id) : NULL;
        $this->data['type'] = $this->input->post('type'); 
        $this->data['tax_rates'] = $this->site->getAllTaxRates();
        $this->load->view($this->theme . 'qsr/reprint_viewbill', $this->data);
    }
    public function reports_old(){

        $reports_type = 4;//$this->input->get('type');
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

        if($reports_type == 4){
            
            $this->data['settlement'] = $this->qsr_model->getSettlementReport($start,$end);                 
            $this->data['dates'] = $dates;
            $this->load->view($this->theme . 'qsr/settlement_reports', $this->data);
        }else{
            $this->data['settlement'] = $this->qsr_model->getSettlementReport($start,$end);
            $this->load->view($this->theme . 'qsr/settlement_reports', $this->data);
            
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
            
            $this->data['recipes'] = $this->qsr_model->getItemSaleReports($start,$end);
            $this->data['round'] = $this->qsr_model->getRoundamount($start,$end);            
            $this->load->view($this->theme . 'qsr/item_reports', $this->data);

        }elseif($reports_type == 2){

            $vale = $this->settings->default_currency;
            $this->data['row'] = $this->qsr_model->getdaysummary($start,$end);
            $this->data['collection'] = $this->qsr_model->getCollection($start,$end);
            $this->load->view($this->theme . 'qsr/day_reports', $this->data);

        }elseif($reports_type == 3){

            $this->data['cashier'] = $this->qsr_model->getCashierReport($start,$end);            
            $this->data['dates'] = $dates;
            $this->load->view($this->theme . 'qsr/cashier_reports', $this->data);

        }elseif($reports_type == 4){

            $this->data['settlement'] = $this->qsr_model->getSettlementReport($start,$end);                 
            $this->data['dates'] = $dates;
            $this->load->view($this->theme . 'qsr/settlement_reports', $this->data);

        }else{
            $this->data['settlement'] = $this->qsr_model->getSettlementReport($start,$end);                 
            $this->data['dates'] = $dates;
            $this->load->view($this->theme . 'qsr/settlement_reports', $this->data);
            
        }
    }

}
