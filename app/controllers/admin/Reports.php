<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        $this->report_view_access = $this->session->userdata('report_view_access') ? $this->session->userdata('report_view_access') : 0;  
        $this->report_show = 0;
        if($this->report_view_access == 2)
         {             
             $this->report_show = 0;
         }elseif ($this->report_view_access == 3) {
             $this->report_show = 1;
         }
         else{            
            $this->report_show = '';
         }

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }

        $this->lang->admin_load('reports', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('reports_model');
        $this->data['pb'] = array(
            'cash' => lang('cash'),
            'CC' => lang('CC'),
            'Cheque' => lang('Cheque'),
            'paypal_pro' => lang('paypal_pro'),
            'stripe' => lang('stripe'),
            'gift_card' => lang('gift_card'),
            'deposit' => lang('deposit'),
            'authorize' => lang('authorize'),
            );

    }

    function index()
    {
        $this->sma->checkPermissions();
        $data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['monthly_sales'] = $this->reports_model->getChartData();

        $this->data['stock'] = $this->reports_model->getStockValue();
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('reports')));
        $meta = array('page_title' => lang('reports'), 'bc' => $bc);
        $this->page_construct('reports/index', $meta, $this->data);

    }

    function warehouse_stock($warehouse = NULL)
    {
        $this->sma->checkPermissions();
        $data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        if ($this->input->get('warehouse')) {
            $warehouse = $this->input->get('warehouse');
        }
        $this->data['stock'] = $warehouse ? $this->reports_model->getWarehouseStockValue($warehouse) : $this->reports_model->getStockValue();
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['warehouse_id'] = $warehouse;
        $this->data['warehouse'] = $warehouse ? $this->site->getWarehouseByID($warehouse) : NULL;
        $this->data['totals'] = $this->reports_model->getWarehouseTotals($warehouse);
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('reports')));
        $meta = array('page_title' => lang('reports'), 'bc' => $bc);
        $this->page_construct('reports/warehouse_stock', $meta, $this->data);

    }

    function expiry_alerts($warehouse_id = NULL)
    {
        $this->sma->checkPermissions();
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['warehouse_id'] = $warehouse_id;
            $this->data['warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : NULL;
        } else {
            $user = $this->site->getUser();
            $this->data['warehouses'] = NULL;
            $this->data['warehouse_id'] = $user->warehouse_id;
            $this->data['warehouse'] = $user->warehouse_id ? $this->site->getWarehouseByID($user->warehouse_id) : NULL;
        }

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('product_expiry_alerts')));
        $meta = array('page_title' => lang('product_expiry_alerts'), 'bc' => $bc);
        $this->page_construct('reports/expiry_alerts', $meta, $this->data);
    }

    function getExpiryAlerts($warehouse_id = NULL)
    {
        $this->sma->checkPermissions('expiry_alerts', TRUE);
        $date = date('Y-m-d', strtotime('+3 months'));

        if (!$this->Owner && !$warehouse_id) {
            $user = $this->site->getUser();
            $warehouse_id = $user->warehouse_id;
        }

        $this->load->library('datatables');
        if ($warehouse_id) {
            $this->datatables
                ->select("'sno',image, product_code, product_name, quantity_balance, warehouses.name, expiry")
                ->from('purchase_items')
                ->join('products', 'products.id=purchase_items.product_id', 'left')
                ->join('warehouses', 'warehouses.id=purchase_items.warehouse_id', 'left')
                ->where('warehouse_id', $warehouse_id)
                ->where('expiry !=', NULL)->where('expiry !=', '0000-00-00')
                ->where('expiry <', $date);
        } else {
            $this->datatables
                ->select("'sno',image, product_code, product_name, quantity_balance, warehouses.name, expiry")
                ->from('purchase_items')
                ->join('products', 'products.id=purchase_items.product_id', 'left')
                ->join('warehouses', 'warehouses.id=purchase_items.warehouse_id', 'left')
                ->where('expiry !=', NULL)->where('expiry !=', '0000-00-00')
                ->where('expiry <', $date);
        }
        echo $this->datatables->generate();
    }

    function quantity_alerts($warehouse_id = NULL)
    {
        $this->sma->checkPermissions();
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['warehouse_id'] = $warehouse_id;
            $this->data['warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : NULL;
        } else {
            $user = $this->site->getUser();
            $this->data['warehouses'] = NULL;
            $this->data['warehouse_id'] = $user->warehouse_id;
            $this->data['warehouse'] = $user->warehouse_id ? $this->site->getWarehouseByID($user->warehouse_id) : NULL;
        }

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('product_quantity_alerts')));
        $meta = array('page_title' => lang('product_quantity_alerts'), 'bc' => $bc);
        $this->page_construct('reports/quantity_alerts', $meta, $this->data);
    }

    function getQuantityAlerts($warehouse_id = NULL, $pdf = NULL, $xls = NULL)
    {
        $this->sma->checkPermissions('quantity_alerts', TRUE);
        if (!$this->Owner && !$warehouse_id) {
            $user = $this->site->getUser();
            $warehouse_id = $user->warehouse_id;
        }

        if ($pdf || $xls) {

            if ($warehouse_id) {
                $this->db
                    ->select('products.image as image, products.code, products.name, warehouses_products.quantity, alert_quantity')
                    ->from('products')->join('warehouses_products', 'warehouses_products.product_id=products.id', 'left')
                    ->where('alert_quantity > warehouses_products.quantity', NULL)
                    ->where('warehouse_id', $warehouse_id)
                    ->where('track_quantity', 1)
                    ->order_by('products.code desc');
            } else {
                $this->db
                    ->select('image, code, name, quantity, alert_quantity')
                    ->from('products')
                    ->where('alert_quantity > quantity', NULL)
                    ->where('track_quantity', 1)
                    ->order_by('code desc');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('product_quantity_alerts'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('product_code'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('product_name'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('quantity'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('alert_quantity'));

                $row = 2;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->code);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->name);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->quantity);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->alert_quantity);
                    $row++;
                }

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $filename = 'product_quantity_alerts';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {

            $this->load->library('datatables');
            if ($warehouse_id) {
                $this->datatables
                    ->select('"sno",image, code, name, wp.quantity, alert_quantity')
                    ->from('products')
                    ->join("( SELECT * from {$this->db->dbprefix('warehouses_products')} WHERE warehouse_id = {$warehouse_id}) wp", 'products.id=wp.product_id', 'left')
                    ->where('alert_quantity > wp.quantity', NULL)
                    ->or_where('wp.quantity', NULL)
                    ->where('track_quantity', 1)
                    ->group_by('products.id');
            } else {
                $this->datatables
                    ->select('"sno",image, code, name, quantity, alert_quantity')
                    ->from('products')
                    ->where('alert_quantity > quantity', NULL)
                    ->where('track_quantity', 1);
            }

            echo $this->datatables->generate();

        }

    }

    function suggestions()
    {
        $term = $this->input->get('term', TRUE);
        if (strlen($term) < 1) {
            die();
        }

        $rows = $this->reports_model->getProductNames($term);
        if ($rows) {
            foreach ($rows as $row) {
                $pr[] = array('id' => $row->id, 'label' => $row->name . " (" . $row->code . ")");

            }
            $this->sma->send_json($pr);
        } else {
            echo FALSE;
        }
    }

    public function best_sellers($warehouse_id = NULL)
    {
        $this->sma->checkPermissions();

        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $y1 = date('Y', strtotime('-1 month'));
        $m1 = date('m', strtotime('-1 month'));  
        
        $m1sdate = $y1.'-'.$m1.'-01 00:00:00';
        $m1edate = $y1.'-'.$m1.'-'. days_in_month($m1, $y1) . ' 23:59:59';
        $this->data['m1'] = date('M Y', strtotime($y1.'-'.$m1));
        $this->data['m1bs'] = $this->reports_model->getBestSeller($m1sdate, $m1edate, $warehouse_id);        


        $y2 = date('Y', strtotime('-2 months'));
        $m2 = date('m', strtotime('-2 months'));
        $m2sdate = $y2.'-'.$m2.'-01 00:00:00';
        $m2edate = $y2.'-'.$m2.'-'. days_in_month($m2, $y2) . ' 23:59:59';
        $this->data['m2'] = date('M Y', strtotime($y2.'-'.$m2));
        $this->data['m2bs'] = $this->reports_model->getBestSeller($m2sdate, $m2edate, $warehouse_id);      

        $y3 = date('Y', strtotime('-3 months'));
        $m3 = date('m', strtotime('-3 months'));
        $m3sdate = $y3.'-'.$m3.'-01 23:59:59';
        $this->data['m3'] = date('M Y', strtotime($y3.'-'.$m3)).' - '.$this->data['m1'];
        $this->data['m3bs'] = $this->reports_model->getBestSeller($m3sdate, $m1edate, $warehouse_id);        

        $y4 = date('Y', strtotime('-12 months'));
        $m4 = date('m', strtotime('-12 months'));
        $m4sdate = $y4.'-'.$m4.'-01 23:59:59';
        $this->data['m4'] = date('M Y', strtotime($y4.'-'.$m4)).' - '.$this->data['m1'];
        $this->data['m4bs'] = $this->reports_model->getBestSeller($m4sdate, $m1edate, $warehouse_id);
        // $this->sma->print_arrays($this->data['m1bs'], $this->data['m2bs'], $this->data['m3bs'], $this->data['m4bs']);
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : NULL;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('best_sellers')));
        $meta = array('page_title' => lang('best_sellers'), 'bc' => $bc);

        $this->page_construct('reports/best_sellers', $meta, $this->data);

    }

    function products()
    {
        $this->sma->checkPermissions();
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['categories'] = $this->site->getAllCategories();

        $this->data['brands'] = $this->site->getAllBrands();
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        
        if ($this->input->post('start_date')) {
            $dt = "From " . $this->input->post('start_date') . " to " . $this->input->post('end_date');
        } else {
            $dt = "Till " . $this->input->post('end_date');
        }
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('sale_items_report')));
        $meta = array('page_title' => lang('products_report'), 'bc' => $bc);
        $this->page_construct('reports/products', $meta, $this->data);
    }

   function getProductsReport_old($pdf = NULL, $xls = NULL)
    {
        $this->sma->checkPermissions('products', TRUE);

        $product = $this->input->get('product') ? $this->input->get('product') : NULL;
        $category = $this->input->get('category') ? $this->input->get('category') : NULL;
        $brand = $this->input->get('brand') ? $this->input->get('brand') : NULL;
        $subcategory = $this->input->get('subcategory') ? $this->input->get('subcategory') : NULL;
        $warehouse = $this->input->get('warehouse') ? $this->input->get('warehouse') : NULL;
        $cf1 = $this->input->get('cf1') ? $this->input->get('cf1') : NULL;
        $cf2 = $this->input->get('cf2') ? $this->input->get('cf2') : NULL;
        $cf3 = $this->input->get('cf3') ? $this->input->get('cf3') : NULL;
        $cf4 = $this->input->get('cf4') ? $this->input->get('cf4') : NULL;
        $cf5 = $this->input->get('cf5') ? $this->input->get('cf5') : NULL;
        $cf6 = $this->input->get('cf6') ? $this->input->get('cf6') : NULL;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
        $end_date = $this->input->get('end_date') ? $this->input->get('end_date') : NULL;

        $pp = "( SELECT product_id, SUM(CASE WHEN pi.purchase_id IS NOT NULL THEN quantity ELSE 0 END) as purchasedQty, SUM(quantity_balance) as balacneQty, SUM( unit_cost * quantity_balance ) balacneValue, SUM( (CASE WHEN pi.purchase_id IS NOT NULL THEN (pi.subtotal) ELSE 0 END) ) totalPurchase from {$this->db->dbprefix('purchase_items')} pi LEFT JOIN {$this->db->dbprefix('purchases')} p on p.id = pi.purchase_id ";
        


            $sp = "( SELECT PR.id,PR.name,SU.name AS saleunit,PU.name AS productunit,SUM(RP.max_quantity) AS soldQty,PR.open_stock_quantity AS OS 
            FROM " . $this->db->dbprefix('bils') . "  P
            JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.bil_id = P.id 
            JOIN " . $this->db->dbprefix('recipe_products') . " RP ON RP.recipe_id =  BI.recipe_id 
            JOIN " . $this->db->dbprefix('products') . " PR ON PR.id = RP.product_id
            JOIN " . $this->db->dbprefix('units') . " SU ON SU.id = RP.units_id
            JOIN " . $this->db->dbprefix('units') . " PU ON PU.id = PR.unit";


        if ($start_date || $warehouse) {
            $pp .= " WHERE ";
            $sp .= " WHERE ";
            if ($start_date) {
                $start_date = $this->sma->fld($start_date);
                $end_date = $end_date ? $this->sma->fld($end_date) : date('Y-m-d');
                $pp .= " p.date >= '{$start_date}' AND p.date < '{$end_date}' ";
                $sp .= " s.date >= '{$start_date}' AND s.date < '{$end_date}' ";
                if ($warehouse) {
                    $pp .= " AND ";
                    $sp .= " AND ";
                }
            }
            if ($warehouse) {
                $pp .= " pi.warehouse_id = '{$warehouse}' ";
                $sp .= " si.warehouse_id = '{$warehouse}' ";
            }
        }
        $pp .= " GROUP BY pi.product_id ) PCosts";
        $sp .= " GROUP BY PR.id ) PSales";

        if ($pdf || $xls) {
            
            $this->db
                ->select($this->db->dbprefix('products') . ".code, " . $this->db->dbprefix('products') . ".name,
                COALESCE( PCosts.purchasedQty, 0 ) as PurchasedQty,
                COALESCE( PSales.soldQty, 0 ) as SoldQty,
                COALESCE( PCosts.balacneQty, 0 ) as BalacneQty,
                COALESCE( PCosts.totalPurchase, 0 ) as TotalPurchase,
                COALESCE( PCosts.balacneValue, 0 ) as TotalBalance,
                (COALESCE( 100, 0 ) - COALESCE( PCosts.totalPurchase, 0 )) as Profit", FALSE)
                ->from('products')
                ->join($sp, 'products.id = PSales.id', 'left')
                ->join($pp, 'products.id = PCosts.product_id', 'left')
                ->order_by('products.name');
                /*
            $this->db
                ->select($this->db->dbprefix('products') . ".code, " . $this->db->dbprefix('products') . ".name,

                CONCAT(COALESCE( PCosts.purchasedQty, 0 ), '__', COALESCE( PCosts.totalPurchase, 0 )) as purchased,

                CONCAT(COALESCE( PSales.soldQty, 0 ), '__', COALESCE( 100, 0 )) as sold,
                (COALESCE( 100, 0 ) - COALESCE( PCosts.totalPurchase, 0 )) as Profit,
                CONCAT(COALESCE( PCosts.balacneQty, 0 ), '__', COALESCE( PCosts.balacneValue, 0 )) as balance, {$this->db->dbprefix('products')}.id as id", FALSE)
                ->from('products')
                ->join($sp, 'products.id = PSales.id', 'left')
                ->join($pp, 'products.id = PCosts.product_id', 'left')
                ->order_by('products.name');*/
                
                

            if ($product) {
                $this->db->where($this->db->dbprefix('products') . ".id", $product);
            }
            if ($cf1) {
                $this->db->where($this->db->dbprefix('products') . ".cf1", $cf1);
            }
            if ($cf2) {
                $this->db->where($this->db->dbprefix('products') . ".cf2", $cf2);
            }
            if ($cf3) {
                $this->db->where($this->db->dbprefix('products') . ".cf3", $cf3);
            }
            if ($cf4) {
                $this->db->where($this->db->dbprefix('products') . ".cf4", $cf4);
            }
            if ($cf5) {
                $this->db->where($this->db->dbprefix('products') . ".cf5", $cf5);
            }
            if ($cf6) {
                $this->db->where($this->db->dbprefix('products') . ".cf6", $cf6);
            }
            if ($category) {
                $this->db->where($this->db->dbprefix('products') . ".category_id", $category);
            }
            if ($subcategory) {
                $this->db->where($this->db->dbprefix('products') . ".subcategory_id", $subcategory);
            }
            if ($brand) {
                $this->db->where($this->db->dbprefix('products') . ".brand", $brand);
            }

            $q = $this->db->get();
            /*echo "<pre>";
            print_r($q->result());die;*/
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('products_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('product_code'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('product_name'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('purchased'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('sold'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('balance'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('purchased_amount'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('sold_amount'));
                $this->excel->getActiveSheet()->SetCellValue('H1', lang('profit_loss'));
                $this->excel->getActiveSheet()->SetCellValue('I1', lang('stock_in_hand'));

                $row = 2;
                $sQty = 0;
                $pQty = 0;
                $sAmt = 0;
                $pAmt = 0;
                $bQty = 0;
                $bAmt = 0;
                $pl = 0;
                /*echo "<pre>";
                print_r($data);die;*/
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->code);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->name);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->PurchasedQty);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->SoldQty);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->BalacneQty);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->TotalPurchase);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->TotalSales);
                    $this->excel->getActiveSheet()->SetCellValue('H' . $row, $data_row->Profit);
                    $this->excel->getActiveSheet()->SetCellValue('I' . $row, $data_row->TotalBalance);
                    $pQty += $data_row->PurchasedQty;
                    $sQty += $data_row->SoldQty;
                    $bQty += $data_row->BalacneQty;
                    $pAmt += $data_row->TotalPurchase;
                    $sAmt += $data_row->TotalSales;
                    $bAmt += $data_row->TotalBalance;
                    $pl += $data_row->Profit;
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle("C" . $row . ":I" . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('C' . $row, $pQty);
                $this->excel->getActiveSheet()->SetCellValue('D' . $row, $sQty);
                $this->excel->getActiveSheet()->SetCellValue('E' . $row, $bQty);
                $this->excel->getActiveSheet()->SetCellValue('F' . $row, $pAmt);
                $this->excel->getActiveSheet()->SetCellValue('G' . $row, $sAmt);
                $this->excel->getActiveSheet()->SetCellValue('H' . $row, $pl);
                $this->excel->getActiveSheet()->SetCellValue('I' . $row, $bAmt);

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('C2:G' . $row)->getAlignment()->setWrapText(true);
                $filename = 'products_report';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {

            $this->load->library('datatables');
            //$this->datatables
            //    ->select($this->db->dbprefix('products') . ".code, " . $this->db->dbprefix('products') . ".name,PSales.soldQty,
            //    CONCAT(COALESCE( PCosts.purchasedQty, 0 ), '__', COALESCE( PCosts.totalPurchase, 0 )) as purchased,
            //
            //    CONCAT(COALESCE( PSales.soldQty, 0 ), '__', COALESCE( 100, 0 )) as sold,
            //    (COALESCE( 100, 0 ) - COALESCE( PCosts.totalPurchase, 0 )) as Profit,
            //    CONCAT(COALESCE( PCosts.balacneQty, 0 ), '__', COALESCE( PCosts.balacneValue, 0 )) as balance, {$this->db->dbprefix('products')}.id as id", FALSE)
            //    ->from('products')
            //    ->join($sp, 'products.id = PSales.id', 'left')
            //    ->join($pp, 'products.id = PCosts.product_id', 'left')
            //    ->group_by('products.code, PSales.soldQty,  PCosts.purchasedQty, PCosts.totalPurchase, PCosts.balacneQty, PCosts.balacneValue');
            //
            //if ($product) {
            //    $this->datatables->where($this->db->dbprefix('products') . ".id", $product);
            //}
            //if ($cf1) {
            //    $this->datatables->where($this->db->dbprefix('products') . ".cf1", $cf1);
            //}
            //if ($cf2) {
            //    $this->datatables->where($this->db->dbprefix('products') . ".cf2", $cf2);
            //}
            //if ($cf3) {
            //    $this->datatables->where($this->db->dbprefix('products') . ".cf3", $cf3);
            //}
            //if ($cf4) {
            //    $this->datatables->where($this->db->dbprefix('products') . ".cf4", $cf4);
            //}
            //if ($cf5) {
            //    $this->datatables->where($this->db->dbprefix('products') . ".cf5", $cf5);
            //}
            //if ($cf6) {
            //    $this->datatables->where($this->db->dbprefix('products') . ".cf6", $cf6);
            //}
            //if ($category) {
            //    $this->datatables->where($this->db->dbprefix('products') . ".category_id", $category);
            //}
            //if ($subcategory) {
            //    $this->datatables->where($this->db->dbprefix('products') . ".subcategory_id", $subcategory);
            //}
            //if ($brand) {
            //    $this->datatables->where($this->db->dbprefix('products') . ".brand", $brand);
            //}
//print_r($this->datatables);exit;
            //echo $this->datatables->generate();exit;
            //SL.Quantity
            
                    //WHEN SU.name="Litre" THEN (SP.price/1000)*SRP.max_quantity
        //WHEN SU.name="Package" THEN (SP.price/1000)*SRP.max_quantity
        //WHEN SU.name="Gram" THEN (SP.price/1000)*SRP.max_quantity
        //WHEN SU.name="Millilitre" THEN (SP.price/1000)*SRP.max_quantity
        //WHEN SU.name="Pieces" THEN (SP.price/1000)*SRP.max_quantity
        
        //CONCAT(CASE WHEN (SL.sold < SUM(P.purchased*SL.Quantity)) THEN "-" ELSE "+" END,"_",SUM(SL.sold-(P.purchased*SL.Quantity))) profitloss,
        //echo $sp;exit;
    $purchased = "
                (
                    SELECT
                        recipe_id,product_id,SU.name,SP.price,SRP.max_quantity,SRU.name unit_name,
         
                        SUM(CASE
                         WHEN (SU.name='Kg' AND SRU.name='Gram') THEN (SP.price/SRU.operation_value)*SRP.max_quantity
                         WHEN (SU.name='Kg' AND SRU.name='Kg') THEN SP.price*SRP.max_quantity
                         
                         WHEN (SU.name='Litre' AND SRU.name='Millilitre') THEN (SP.price/SRU.operation_value)*SRP.max_quantity
                         WHEN (SU.name='Litre' AND SRU.name='Litre') THEN SP.price*SRP.max_quantity
                         
                         WHEN (SU.name='Package' AND SRU.name='Pieces') THEN (SP.price/SRU.operation_value)*SRP.max_quantity
                         WHEN (SU.name='Package' AND SRU.name='Package') THEN SP.price*SRP.max_quantity
                 
                        ELSE 0 END) purchased
                
                    FROM ".$this->db->dbprefix('recipe_products')." SRP         
                    JOIN ".$this->db->dbprefix('products')." SP on SRP.product_id=SP.id 
                    JOIN ".$this->db->dbprefix('units')." SU on SU.id=SP.unit 
                    JOIN ".$this->db->dbprefix('units')." SRU on SRU.id=SRP.units_id
                    group by SRP.recipe_id
                    order by product_id
                ) P";
        $sold = "
            (
                SELECT recipe_id,SUM(quantity) as quantity,SUM(SBI.unit_price*quantity) as sold FROM ".$this->db->dbprefix('bil_items')." SBI
                            join ".$this->db->dbprefix('bils')." SB on SBI.bil_id=SB.id
                            where SB.payment_status='completed'
                            group by SBI.recipe_id
            ) SLSold";
 $this->datatables
                ->select("'sno',".
                    $this->db->dbprefix('warehouses').".name as branch,".
                    $this->db->dbprefix('recipe').".code,
            ".$this->db->dbprefix('recipe').".name,
            SUM(P.purchased*SLSold.quantity) as purchased,
            SLSold.sold,
            SUM(SLSold.sold-(P.purchased*SLSold.quantity)) as profitloss,
            SLSold.Quantity
                ")
                ->from($this->db->dbprefix('recipe'))
                ->join($purchased, $this->db->dbprefix('recipe').".id=P.recipe_id")
                ->join($sold, $this->db->dbprefix('recipe').".id=SLSold.recipe_id")
                ->join('warehouses', 'warehouses.id=recipe.warehouse')
                ->group_by($this->db->dbprefix('recipe').".id");
                //print_R($this->datatables);exit;
 echo $this->datatables->generate();
//print_R($this->db);
        }

    }
   function getProductsReport($pdf = NULL, $xls = NULL)
    {
        $this->sma->checkPermissions('products', TRUE);

        $product = $this->input->get('product') ? $this->input->get('product') : NULL;
        //$category = $this->input->get('category') ? $this->input->get('category') : NULL;
        //$brand = $this->input->get('brand') ? $this->input->get('brand') : NULL;
        //$subcategory = $this->input->get('subcategory') ? $this->input->get('subcategory') : NULL;
        $warehouse_id = $this->input->get('warehouse') ? $this->input->get('warehouse') : NULL;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
        $end_date = $this->input->get('end_date') ? $this->input->get('end_date') : NULL;
        $purchased = "
                (
                    SELECT
                        recipe_id,product_id,SU.name,SP.price,SRP.max_quantity,SRU.name unit_name,
         
                        SUM(CASE
                         WHEN (SU.name='Kg' AND SRU.name='Gram') THEN (SP.price/SRU.operation_value)*SRP.max_quantity
                         WHEN (SU.name='Kg' AND SRU.name='Kg') THEN SP.price*SRP.max_quantity
                         
                         WHEN (SU.name='Litre' AND SRU.name='Millilitre') THEN (SP.price/SRU.operation_value)*SRP.max_quantity
                         WHEN (SU.name='Litre' AND SRU.name='Litre') THEN SP.price*SRP.max_quantity
                         
                         WHEN (SU.name='Package' AND SRU.name='Pieces') THEN (SP.price/SRU.operation_value)*SRP.max_quantity
                         WHEN (SU.name='Package' AND SRU.name='Package') THEN SP.price*SRP.max_quantity
                 
                        ELSE 0 END) purchased
                
                    FROM ".$this->db->dbprefix('recipe_products')." SRP         
                    JOIN ".$this->db->dbprefix('products')." SP on SRP.product_id=SP.id 
                    JOIN ".$this->db->dbprefix('units')." SU on SU.id=SP.unit 
                    JOIN ".$this->db->dbprefix('units')." SRU on SRU.id=SRP.units_id
                    group by SRP.recipe_id
                    order by product_id
                ) P";
        $sold = "
            (
                SELECT recipe_id,SUM(quantity) as quantity,SUM(SBI.unit_price*quantity) as sold FROM ".$this->db->dbprefix('bil_items')." SBI
                            join ".$this->db->dbprefix('bils')." SB on SBI.bil_id=SB.id
                            where SB.payment_status='completed'";
            if ($start_date) {
                $sold .=' AND DATE_FORMAT(SB.date, "%Y-%m-%d") >="'.$start_date.'"';
            }
            if ($end_date) {
             $sold .=' AND DATE_FORMAT(SB.date, "%Y-%m-%d") <="'.$end_date.'"';
            }
            if($warehouse_id != 0){
                $sold .=' AND SBI.warehouse_id='.$warehouse_id;    
            }
            if(!$this->Owner && !$this->Admin){
                $sold .= " AND SB.table_whitelisted =0";
            }
         $sold .= " group by SBI.recipe_id
            ) SLSold";
            //echo $sold;exit;
        if ($pdf || $xls) {
            $this->db
                ->select($this->db->dbprefix('recipe').".code,
                ".$this->db->dbprefix('recipe').".name,
                SUM(P.purchased*SLSold.quantity) as purchased,
                SLSold.sold,
                SUM(SLSold.sold-(P.purchased*SLSold.quantity)) as profitloss,
                SLSold.Quantity
                    ")
                ->from($this->db->dbprefix('recipe'))
                ->join($purchased, $this->db->dbprefix('recipe').".id=P.recipe_id")
                ->join($sold, $this->db->dbprefix('recipe').".id=SLSold.recipe_id")
                ->group_by($this->db->dbprefix('recipe').".id");
            $q = $this->db->get();
            //echo "<pre>";
            //print_r($q->result());die;
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('products_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('recipe_code'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('recipe_name'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('purchased'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('sold'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('profit_loss'));

                $row = 2;
                $sQty = 0;
                $pQty = 0;
                $sAmt = 0;
                $pAmt = 0;
                $bQty = 0;
                $bAmt = 0;
                $pl = 0;
                /*echo "<pre>";
                print_r($data);die;*/
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->code);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->name);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->purchased);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->sold);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->profitloss);
                    $pQty += $data_row->purchased;
                    $sQty += $data_row->sold;
                    $pl += $data_row->profitloss;
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle("C" . $row . ":I" . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('C' . $row, $pQty);
                $this->excel->getActiveSheet()->SetCellValue('D' . $row, $sQty);
                $this->excel->getActiveSheet()->SetCellValue('E' . $row, $pl);

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('C2:G' . $row)->getAlignment()->setWrapText(true);
                $filename = 'products_report';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {

            $this->load->library('datatables');
            $this->datatables
                    ->select("'sno',".
                        $this->db->dbprefix('recipe').".code,
                ".$this->db->dbprefix('recipe').".name,
                SUM(P.purchased*SLSold.quantity) as purchased,
                SLSold.sold,
                SUM(SLSold.sold-(P.purchased*SLSold.quantity)) as profitloss,
                SLSold.Quantity
                    ")
                    ->from($this->db->dbprefix('recipe'))
                    ->join($purchased, $this->db->dbprefix('recipe').".id=P.recipe_id")
                    ->join($sold, $this->db->dbprefix('recipe').".id=SLSold.recipe_id")
                    ->group_by($this->db->dbprefix('recipe').".id");
                    //print_R($this->datatables);exit;
            if ($product) {
                $this->datatables->where($this->db->dbprefix('recipe') . ".id", $product);
            }
            
            echo $this->datatables->generate();
//print_R($this->db);
        }

    }
    
  function recipe()
    {        
        $this->sma->checkPermissions();
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->reports_model->getStaff();
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('item_sale_report')));
        $meta = array('page_title' => lang('item_sale_report'), 'bc' => $bc);
        
        $this->page_construct('reports/recipe', $meta, $this->data);
    }

   public function get_itemreports($start_date = NULL, $end_date = NULL, $warehouse_id = NULL){

        $this->sma->checkPermissions('recipe',TRUE);        
        $start = $this->input->post('start_date');
        $end = $this->input->post('end_date');
        $warehouse_id = $this->input->post('warehouse_id');
        $limit = $this->input->post('pagelimit');    
        $offsetSegment = 4;
        $offset = $this->uri->segment($offsetSegment,0);

        $this->session->set_userdata('start_date', $this->input->post('start_date'));
        $this->session->set_userdata('end_date', $this->input->post('end_date'));

        $data= '';
        if ($start != '' && $end != '') {
            $data = $this->reports_model->getItemSaleReports($start,$end,$warehouse_id,$limit,$offset,$this->report_view_access,$this->report_show);
            $round_tot = $this->reports_model->getRoundamount($start,$end,$warehouse_id);
            
             if (!empty($data['data'])) {                 
                 $itemreports = $data['data'];
             }
             else{                
                $itemreports = 'empty';
             }
             if ($round_tot != false) {                 
                 $round = $round_tot;
             }
             else{                
                $round = 'empty';
             }
        }
        else{
            $itemreports = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('reports/get_itemreports',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('itemreports' => $itemreports,'round' => $round,'pagination'=>$pagination));
   } 

  function pos_settlement()
    {        
        $this->sma->checkPermissions();
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->reports_model->getStaff();        
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('pos_settlement_report')));
        $meta = array('page_title' => lang('pos_settlement_report'), 'bc' => $bc);
        $this->settings = $this->reports_model->getSettings();
        $this->data['default_currency'] = $this->settings->default_currency;
        $this->page_construct('reports/pos_settlement', $meta, $this->data);
    }
      public function get_settlementreports($start_date = NULL, $end_date = NULL, $warehouse_id = NULL, $defalut_currency = NULL){
        $this->sma->checkPermissions('pos_settlement',TRUE);
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $warehouse_id = $this->input->post('warehouse_id');
        $limit = $this->input->post('pagelimit');        
        $offsetSegment = 4;
        $offset = $this->uri->segment($offsetSegment,0);
        $defalut_currency = $this->input->post('defalut_currency');

        $this->session->set_userdata('start_date', $this->input->post('start_date'));
        $this->session->set_userdata('end_date', $this->input->post('end_date'));

        $data= '';
        if ($end_date != '' && $end_date != '') {
            $data = $this->reports_model->getPosSettlementReport($start_date,$end_date,$warehouse_id,$defalut_currency,$limit,$offset,$this->report_view_access,$this->report_show);
            
            if (!empty($data['data'])){
                 
                 $settlements = $data['data'];
             }
             else{
                
                $settlements = 'empty';
             }
        }
        else{
            $settlements = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('reports/get_settlementreports',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('settlements' => $settlements,'pagination'=>$pagination));
   } 
   function kot_details()
    {
        $this->sma->checkPermissions();
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->reports_model->getStaff();
        /*$this->data['users'] = $this->reports_model->getPosSettlementReport();*/
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('kot_details_report')));
        $meta = array('page_title' => lang('kot_details_report'), 'bc' => $bc);
        
        $this->page_construct('reports/kot_details', $meta, $this->data);
    }  
 public function get_kotdetailsreports($start_date = NULL, $end_date = NULL, $kot = NULL, $warehouse_id = NULL){
        $this->sma->checkPermissions('kot_details',TRUE);
        $start = $this->input->post('start_date');
        $end = $this->input->post('end_date');
        $kot = $this->input->post('kot');
        $warehouse_id = $this->input->post('warehouse_id');
        $limit = $this->input->post('pagelimit');        
        $offsetSegment = 4;
        $offset = $this->uri->segment($offsetSegment,0);

        $this->session->set_userdata('start_date', $this->input->post('start_date'));
        $this->session->set_userdata('end_date', $this->input->post('end_date'));

        $data= '';

        if ($start != '' && $end != '' && $kot != '') {
             if($kot == 'kot_cancel') 
             {                
                $data = $this->reports_model->getKotCancelReport($start,$end,$warehouse_id,$limit,$offset);
             }
             elseif ($kot == 'kot_pending')
             {
              $data = $this->reports_model->getKotPendingReport($start,$end,$warehouse_id,$limit,$offset);  
             }
             else{                
                $data = $this->reports_model->getKotDetailsReport($start,$end,$warehouse_id,$limit,$offset);
             }
             
             if (!empty($data['data'])){
                 
                 $kotdetails = $data['data'];
             }
             else{
                
                $kotdetails = 'empty';
             }
        }
        else{
            $kotdetails = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('reports/get_kotdetailsreports',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('kotdetails' => $kotdetails,'pagination'=>$pagination));
   } 
   
    function user_reports()
    {
        $this->sma->checkPermissions();
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->reports_model->getStaff();        
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
		$this->data['groups'] = $this->site->getAllGroups();  
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('user_report')));
        $meta = array('page_title' => lang('user_report'), 'bc' => $bc);
        
        $this->page_construct('reports/user_reports', $meta, $this->data);
    }    
 public function get_user_reports($start = NULL, $end = NULL, $user = NULL){
        $this->sma->checkPermissions('user_reports',true);
        $start = $this->input->post('start');
        $end = $this->input->post('end');
        $user = $this->input->post('user');
		$group = $this->input->post('group');
        $limit = $this->input->post('pagelimit');        
        $offsetSegment = 4;
        $offset = $this->uri->segment($offsetSegment,0);

        $this->session->set_userdata('start_date', $this->input->post('start'));
        $this->session->set_userdata('end_date', $this->input->post('end'));

        $data= '';
        if ($start != '' && $end != '') {
            $data = $this->reports_model->getCashierReport($start,$end,$user,$limit,$offset,$group);
            if (!empty($data['data'])){
                 
                 $user_report = $data['data'];
             }
             else{
                
                $user_report = 'empty';
             }
        }
        else{
            $user_report = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('reports/get_user_reports',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('user_report' => $user_report,'pagination'=>$pagination));
   }
    function home_delivery()
    {
        $this->sma->checkPermissions();
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->reports_model->getStaff();        
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['customers'] = $this->site->getAllCompanies('customer');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('home_delivery_report')));
        $meta = array('page_title' => lang('home_delivery_report'), 'bc' => $bc);
        
        $this->page_construct('reports/home_delivery', $meta, $this->data);
    }
   public function get_homedelivery_reports($start = NULL, $end = NULL, $warehouse_id = NULL, $customer = NULL){
        $this->sma->checkPermissions('home_delivery',TRUE);
        $start = $this->input->post('start');
        $end = $this->input->post('end');
        $warehouse_id = $this->input->post('warehouse_id');
        $customer = $this->input->post('customer');
        $limit = $this->input->post('pagelimit');        
        $offsetSegment = 4;
        $offset = $this->uri->segment($offsetSegment,0);

        $this->session->set_userdata('start_date', $this->input->post('start'));
        $this->session->set_userdata('end_date', $this->input->post('end'));

        $data= '';
        if ($start != '' && $end != '') {
            $data = $this->reports_model->getHomedeliveryReport($start,$end,$warehouse_id,$customer,$limit,$offset);
            if (!empty($data['data'])){
                 
                 $home_delivery = $data['data'];
             }
             else{
                
                $home_delivery = 'empty';
             }
        }
        else{
            $home_delivery = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('reports/get_homedelivery_reports',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('home_delivery' => $home_delivery,'pagination'=>$pagination));
   } 
   function take_away()
    {
        $this->sma->checkPermissions();
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->reports_model->getStaff();        
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('take_away')));
        $meta = array('page_title' => lang('take_away'), 'bc' => $bc);
        
        $this->page_construct('reports/takeaway', $meta, $this->data);
    }
   public function get_take_away_reports($start = NULL, $end = NULL, $warehouse_id = NULL){
        $this->sma->checkPermissions('take_away',TRUE);
        $start = $this->input->post('start');
        $end = $this->input->post('end');
        $warehouse_id = $this->input->post('warehouse_id');
        $limit = $this->input->post('pagelimit');        
        $offsetSegment = 4;
        $offset = $this->uri->segment($offsetSegment,0);

        $this->session->set_userdata('start_date', $this->input->post('start'));
        $this->session->set_userdata('end_date', $this->input->post('end'));

        $data= '';
        if ($start != '' && $end != '') {
            $data = $this->reports_model->getTakeAwayReport($start,$end,$warehouse_id,$limit,$offset);
            if (!empty($data['data'])){
                 
                 $take_away = $data['data'];
             }
             else{
                
                $take_away = 'empty';
             }
        }
        else{
            $take_away = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('reports/get_take_away_reports',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('take_away' => $take_away,'pagination'=>$pagination));
   }  
    function daywise()
    {
        $this->sma->checkPermissions('daily_sales');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->reports_model->getStaff();        
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('day_wise')));
        $meta = array('page_title' => lang('day_wise'), 'bc' => $bc);
        
        $this->page_construct('reports/day_wise', $meta, $this->data);
    } 
    public function get_DaySummaryreports($start = NULL, $warehouse_id = NULL){
        $this->sma->checkPermissions('daily_sales',TRUE);
        $start = $this->input->post('start_date');
        $limit = $this->input->post('pagelimit');        
        $warehouse_id = $this->input->post('warehouse_id');
        $offsetSegment = 4;
        $offset = $this->uri->segment($offsetSegment,0);
        $data= '';
        if ($start != '') {
            $data = $this->reports_model->getDaysummaryReport($start, $warehouse_id,$limit,$offset,$this->report_view_access,$this->report_show);
            if (!empty($data['data'])) {
                 
                 $daysummary = $data['data'];
             }
             else{
                
                $daysummary = 'empty';
             }
        }
        else{
            $daysummary = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('reports/get_DaySummaryreports',$limit,$offsetSegment,$total);
        //echo $daysummary;
        $this->sma->send_json(array('daysummary' => $daysummary,'pagination'=>$pagination));
   }   
   function discount_summary()
    {
        $this->sma->checkPermissions();
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->reports_model->getStaff();        
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('discount_summary')));
        $meta = array('page_title' => lang('discount_summary'), 'bc' => $bc);
        
        $this->page_construct('reports/discount_summary', $meta, $this->data);
    }   
public function get_DiscountSummary($start = NULL, $end = NULL, $dis_type = NULL, $warehouse_id = NULL){
        $this->sma->checkPermissions('discount_summary',TRUE);
        $start = $this->input->post('start_date');
        $end = $this->input->post('end_date');
        $dis_type = $this->input->post('dis_type');
        $warehouse_id = $this->input->post('warehouse_id');
        $limit = $this->input->post('pagelimit');        
        $offsetSegment = 4;
        $offset = $this->uri->segment($offsetSegment,0);

        $this->session->set_userdata('start_date', $this->input->post('start_date'));
        $this->session->set_userdata('end_date', $this->input->post('end_date'));

        $data= '';
        if ($start != '' && $end != '' && $dis_type != '') {
            if($dis_type == 'dis_details') 
             {                
                $data = $this->reports_model->getDiscountDetailsReport($start, $end, $warehouse_id,$limit,$offset,$this->report_view_access,$this->report_show);
             }
             else
             {
                $data = $this->reports_model->getDiscountsummaryReport($start, $end, $warehouse_id,$limit,$offset,$this->report_view_access,$this->report_show);
             }
            if (!empty($data['data']))
             {
                 $discount = $data['data'];
             }
             else
             {  
                $discount = 'empty';
             }
        }
        else
        {
            $discount = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('reports/get_DiscountSummary',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('discount' => $discount,'pagination'=>$pagination));
   }
   function void_bills()
    {
        $this->sma->checkPermissions();
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->reports_model->getStaff();        
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('void_bill')));
        $meta = array('page_title' => lang('void_bill'), 'bc' => $bc);
        
        $this->page_construct('reports/void_bills', $meta, $this->data);
    }  
  public function get_voidbills_reports($start = NULL, $end = NULL, $warehouse_id = NULL){
        $this->sma->checkPermissions('void_bills',TRUE);
        $start = $this->input->post('start_date');
        $end = $this->input->post('end_date');
        $warehouse_id = $this->input->post('warehouse_id');
        $limit = $this->input->post('pagelimit');        
        $offsetSegment = 4;
        $offset = $this->uri->segment($offsetSegment,0);

        $this->session->set_userdata('start_date', $this->input->post('start_date'));
        $this->session->set_userdata('end_date', $this->input->post('end_date'));

        $data= '';
        if ($start != '' && $end != '') {
            $data = $this->reports_model->getVoidBillsReport($start,$end,$warehouse_id,$limit,$offset,$this->report_view_access,$this->report_show);  
            if (!empty($data['data'])){
                 
                 $voidbills = $data['data'];
             }
             else{
                
                $voidbills = 'empty';
             }
        }
        else{
            $voidbills = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('reports/get_popular_reports',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('void_bills' => $voidbills,'pagination'=>$pagination));
   } 
   function tax_reports()
    {
        $this->sma->checkPermissions();
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->reports_model->getStaff();        
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('tax_reports')));
        $meta = array('page_title' => lang('tax_reports'), 'bc' => $bc);
        
        $this->page_construct('reports/tax_reports', $meta, $this->data);
    }   
  public function get_tax_reports($start = NULL, $end = NULL, $warehouse_id = NULL){
        $this->sma->checkPermissions('tax_reports',TRUE);
        $start = $this->input->post('start_date');
        $end = $this->input->post('end_date');
        $warehouse_id = $this->input->post('warehouse_id');
        $limit = $this->input->post('pagelimit');        
        $offsetSegment = 4;
        $offset = $this->uri->segment($offsetSegment,0);

        $this->session->set_userdata('start_date', $this->input->post('start_date'));
        $this->session->set_userdata('end_date', $this->input->post('end_date'));

        $data= '';
        if ($start != '' && $end != '') {
            $data = $this->reports_model->getTaxReport($start,$end,$warehouse_id,$limit,$offset,$this->report_view_access,$this->report_show);  
            if (!empty($data['data'])) {
                 
                 $taxrep = $data['data'];
             }
             else{
                
                $taxrep = 'empty';
             }
        }
        else{
            $taxrep = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('reports/get_tax_reports',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('tax' => $taxrep,'pagination'=>$pagination));
   }      
    function popular_analysis()
    {
        $this->sma->checkPermissions();
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->reports_model->getStaff();        
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('popular_analysis_reports')));
        $meta = array('page_title' => lang('popular_analysis_reports'), 'bc' => $bc);
        $this->page_construct('reports/popular_analysis', $meta, $this->data);
    }    
    public function get_popular_reports($start = NULL, $end = NULL, $popular = NULL, $warehouse_id = NULL){
        $this->sma->checkPermissions('popular_analysis',TRUE);
        $start = $this->input->post('start_date');
        $end = $this->input->post('end_date');
        $popular = $this->input->post('popular');
        $warehouse_id = $this->input->post('warehouse_id');
        $limit = $this->input->post('pagelimit');        
        $offsetSegment = 4;
        $offset = $this->uri->segment($offsetSegment,0);

        $this->session->set_userdata('start_date', $this->input->post('start_date'));
        $this->session->set_userdata('end_date', $this->input->post('end_date'));

        $data= '';
        if ($start != '' && $end != '') {
           if($popular == 'popular') 
             {                
                $data = $this->reports_model->getPopularReports($start,$end,$warehouse_id,$limit,$offset,$this->report_view_access,$this->report_show);
                $round_tot = $this->reports_model->getRoundamount($start,$end,$warehouse_id);
             }
             else
             {
              $data = $this->reports_model->getNonPopularReports($start,$end,$warehouse_id,$limit,$offset,$this->report_view_access,$this->report_show); 
              $round_tot = $this->reports_model->getRoundamount($start,$end,$warehouse_id); 
             }
           if ($round_tot != false) {
                 $round = $round_tot;
             }
             else{
                $round = 'empty';
             }
            if (!empty($data['data'])){
                 $popular = $data['data'];
             }
             else{
                $popular = 'empty';
             }
        }
        else{
            $popular = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('reports/get_popular_reports',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('popular_non_popular' => $popular,'round' => $round,'pagination'=>$pagination));
   }     
    function cover_analysis()
    {
        $this->sma->checkPermissions();
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->reports_model->getStaff();        
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('cover_analysis')));
        $meta = array('page_title' => lang('cover_analysis'), 'bc' => $bc);
        
        $this->page_construct('reports/cover_analysis', $meta, $this->data);
    }    
 public function get_cover_analysis($start = NULL, $end = NULL, $warehouse_id = NULL){
        $this->sma->checkPermissions('cover_analysis',TRUE);
        $start = $this->input->post('start');
        $end = $this->input->post('end');
        $warehouse_id = $this->input->post('warehouse_id');
        $limit = $this->input->post('pagelimit');        
        $offsetSegment = 4;
        $offset = $this->uri->segment($offsetSegment,0);

        $this->session->set_userdata('start_date', $this->input->post('start'));
        $this->session->set_userdata('end_date', $this->input->post('end'));

        $data= '';
        if ($start != '' && $end != '') {
            $data = $this->reports_model->getCoverAnalysisReport($start,$end,$warehouse_id,$limit,$offset,$this->report_view_access,$this->report_show);
            if (!empty($data['data'])){
                 
                 $coveranalysis = $data['data'];
             }
             else{
                
                $coveranalysis = 'empty';
             }
        }
        else{
            $coveranalysis = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('reports/get_cover_analysis',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('cover_analysis' => $coveranalysis,'pagination'=>$pagination));
   }
    function monthly_reports()
    {
        $this->sma->checkPermissions('monthly_sales');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->reports_model->getStaff();        
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('category_wise_monthly_sales_report')));
        $meta = array('page_title' => lang('category_wise_monthly_sales_report'), 'bc' => $bc);
        
        $this->page_construct('reports/monthly_reports', $meta, $this->data);
    }  

 public function get_monthly_reports($start= NULL,$end= NULL,$warehouse_id= NULL){
    $this->sma->checkPermissions('monthly_sales',TRUE);    

        $start = $this->input->post('start');
      /*  if ($start != '') {            
           $start = $start; 
        }
        else{
            $start = date('Y-m-d');
        }*/
        //$end = $this->input->post('end');
        $warehouse_id = $this->input->post('warehouse_id');
        $limit = $this->input->post('pagelimit');     
        $offsetSegment = 4;
        $offset = $this->uri->segment($offsetSegment,0);
        $data= '';
        if ($start != '') {
            $data = $this->reports_model->getMonthlyReport($start,$warehouse_id,$limit,$offset,$this->report_view_access,$this->report_show);
         
        if (!empty($data['data'])) {
                 
                 $MonthlyReports = $data['data'];
             }
             else{
                
                $MonthlyReports = 'empty';
             }
        }
        else{
            $MonthlyReports = 'error';
        }
   // echo $MonthlyReports;
        $total = $data['total'];
        $pagination = $this->pagination('reports/get_monthly_reports',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('monthly_reports' => $MonthlyReports,'pagination'=>$pagination));
        /*$this->sma->send_json(array('monthly_reports' => $month));*/
   } 
 function bill_details()
    {
        $this->sma->checkPermissions();
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->reports_model->getStaff();        
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('bill_details_report')));
        $meta = array('page_title' => lang('bill_details_report'), 'bc' => $bc);
        
        $this->page_construct('reports/bill_details', $meta, $this->data);
    }    
public function get_bill_no($start = NULL, $end = NULL, $warehouse_id=NULL){    
        $start = $this->input->post('start');
        $end = $this->input->post('end');
        $warehouse_id = $this->input->post('warehouse_id');

        $data= '';
        if ($start != '' && $end != '') {
            $data = $this->reports_model->getBill_no($start,$end,$warehouse_id,$this->report_view_access,$this->report_show);
            if ($data != false) {
                 $bill_no = $data;
             }
             else{
                $bill_no = 'empty';
             }
        }
        else{
            $bill_no = 'error';
        }
        $this->sma->send_json(array('bill_no' => $bill_no));
   } 

 public function get_bill_details_reports($start = NULL, $end = NULL, $bill_no = NULL, $warehouse_id = NULL){
        $this->sma->checkPermissions('bill_details',TRUE);
        $start = $this->input->post('start');
        $end = $this->input->post('end');
        $bill_no = $this->input->post('bill_no');
        $warehouse_id = $this->input->post('warehouse_id');
        $limit = $this->input->post('pagelimit');        
        $offsetSegment = 4;
        $offset = $this->uri->segment($offsetSegment,0);

        $this->session->set_userdata('start_date', $this->input->post('start'));
        $this->session->set_userdata('end_date', $this->input->post('end'));

        $data= '';
        $table_whitelisted = $this->input->post('table_whitelisted');
        if ($start != '' && $end != '') {
            $data = $this->reports_model->getBillDetailsReport($start,$end,$bill_no,$warehouse_id,$limit,$offset,$this->report_view_access,$this->report_show);
            if (!empty($data['data'])){
                 $bill = $data['data'];
             }
             else{
                $bill = 'empty';
             }
        }
        else{
            $bill = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('reports/get_bill_details_reports',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('bill_details' => $bill,'pagination'=>$pagination));
        
   } 
   function order_timing()
    {   if(!$this->Settings->recipe_time_management){ $this->session->set_flashdata('error','Access_denied');redirect();}
        $this->sma->checkPermissions('recipe');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->reports_model->getStaff();
        /*$this->data['users'] = $this->reports_model->getPosSettlementReport();*/
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('order_time_report')));
        $meta = array('page_title' => lang('order_time_report'), 'bc' => $bc);
        
        $this->page_construct('reports/order_timing', $meta, $this->data);
    }  
 public function get_ordertiming_details($start_date = NULL, $end_date = NULL, $warehouse_id = NULL){

        $start = $this->input->post('start_date');
        $end = $this->input->post('end_date');
        
        $warehouse_id = $this->input->post('warehouse_id');
        $limit = $this->input->post('pagelimit');        
        $offsetSegment = 4;
        $offset = $this->uri->segment($offsetSegment,0);

        $this->session->set_userdata('start_date', $this->input->post('start_date'));
        $this->session->set_userdata('end_date', $this->input->post('end_date'));
        
        $data= '';

        if ($start != '' && $end != '') {
             $data = $this->reports_model->getOrderTimeReport($start,$end,$warehouse_id,$limit,$offset,$this->report_view_access,$this->report_show);
             
             if (!empty($data['data'])) {
                 
                 $ordertime = $data['data'];
             }
             else{
                
                $ordertime = 'empty';
             }
        }
        else{
            $ordertime = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('reports/get_ordertiming_details',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('ordertime' => $ordertime,'pagination'=>$pagination));
   } 

function stock_audit()
    {
        $this->sma->checkPermissions();
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->reports_model->getStaff();        
        $this->data['Products'] = $this->reports_model->getProducts();

        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('stock_audit_rep')));
        $meta = array('page_title' => lang('stock_audit_rep'), 'bc' => $bc);
        
        $this->page_construct('reports/stock_audit', $meta, $this->data);
    }   
    public function get_StockAuditreports($start = NULL, $product_id = NULL,$warehouse_id = NULL){
     $this->sma->checkPermissions('stock_audit',TRUE);
        $start = $this->input->post('start_date');
        $product_id = $this->input->post('product_id');
        $warehouse_id = $this->input->post('warehouse_id');
        $limit = $this->input->post('pagelimit');        
        $offsetSegment = 4;
        $offset = $this->uri->segment($offsetSegment,0);
        /*echo "<pre>";
        print_r($this->input->post());die;*/
        $data= '';
        if ($start != '') {
            $data = $this->reports_model->getStockVariance($start, $product_id, $warehouse_id,$limit,$offset,$this->report_view_access,$this->report_show);
            if (!empty($data['data'])){             
                 $stockaudit = $data['data'];
             }
             else{
                 $stockaudit = 'empty';
             }
        }
        else{
            $stockaudit = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('reports/get_StockAuditreports',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('stock_audit' => $stockaudit,'pagination'=>$pagination));
   }       
function getRecipeReport($pdf = NULL, $xls = NULL)
    {
        $this->sma->checkPermissions('recipe', TRUE);
        $product = $this->input->get('product') ? $this->input->get('product') : NULL;
        $user = $this->input->get('user') ? $this->input->get('user') : NULL;
        $customer = $this->input->get('customer') ? $this->input->get('customer') : NULL;
        $biller = $this->input->get('biller') ? $this->input->get('biller') : NULL;
        $warehouse = $this->input->get('warehouse') ? $this->input->get('warehouse') : NULL;
        $reference_no = $this->input->get('reference_no') ? $this->input->get('reference_no') : NULL;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
        $end_date = $this->input->get('end_date') ? $this->input->get('end_date') : NULL;
        $serial = $this->input->get('serial') ? $this->input->get('serial') : NULL;

        if ($start_date) {
            $start_date = $this->sma->fld($start_date);
            $end_date = $this->sma->fld($end_date);
        }
        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user = $this->session->userdata('user_id');
        }

        if ($pdf || $xls) {

            $this->db
                ->select("date, reference_no, biller, customer, GROUP_CONCAT(CONCAT(" . $this->db->dbprefix('bil_items') . ".recipe_name, ' (', " . $this->db->dbprefix('bil_items') . ".quantity, ')') SEPARATOR '\n') as iname, grand_total, paid, payment_status", FALSE)
                ->from('bils')
                ->join('bil_items', 'bil_items.bil_id=bils.id', 'left')
                /*->join('warehouses', 'warehouses.id=bils.warehouse_id', 'left')*/
                ->group_by('bils.id')
                ->order_by('bils.date desc');

            if ($user) {
                $this->db->where('bils.created_by', $user);
            }
            if ($product) {
                $this->db->where('bil_items.recipe_id', $product);
            }
            /*if ($serial) {
                $this->db->like('bil_items.serial_no', $serial);
            }*/
            if ($biller) {
                $this->db->where('bils.biller_id', $biller);
            }
            if ($customer) {
                $this->db->where('bils.customer_id', $customer);
            }
           /* if ($warehouse) {
                $this->db->where('bils.warehouse_id', $warehouse);
            }*/
            if ($reference_no) {
                $this->db->like('bils.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->db->where($this->db->dbprefix('bils').'.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            $q = $this->db->get();
            
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {

                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('sales_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference_no'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('biller'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('customer'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('product_qty'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('grand_total'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('paid'));
                $this->excel->getActiveSheet()->SetCellValue('H1', lang('balance'));
                $this->excel->getActiveSheet()->SetCellValue('I1', lang('payment_status'));

                $row = 2;
                $total = 0;
                $paid = 0;
                $balance = 0;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($data_row->date));
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->reference_no);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->biller);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->customer);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->iname);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->grand_total);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->paid);
                    $this->excel->getActiveSheet()->SetCellValue('H' . $row, ($data_row->grand_total - $data_row->paid));
                    $this->excel->getActiveSheet()->SetCellValue('I' . $row, lang($data_row->payment_status));
                    $total += $data_row->grand_total;
                    $paid += $data_row->paid;
                    $balance += ($data_row->grand_total - $data_row->paid);
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle("F" . $row . ":H" . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('F' . $row, $total);
                $this->excel->getActiveSheet()->SetCellValue('G' . $row, $paid);
                $this->excel->getActiveSheet()->SetCellValue('H' . $row, $balance);

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('E2:E' . $row)->getAlignment()->setWrapText(true);
                $filename = 'sales_report';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {

           $si = "( SELECT id as sale_id,bil_id,quantity,subtotal, recipe_id as product_id, recipe_name as item_nane from {$this->db->dbprefix('bil_items')} ) FSI";
            if ($product || $serial) { $si .= " WHERE "; }
            if ($product) {
                $si .= " {$this->db->dbprefix('bil_items')}.recipe_id = {$product} ";
            }
            if ($product && $serial) { $si .= " AND "; }
           /* if ($serial) {
                $si .= " {$this->db->dbprefix('bil_items')}.serial_no LIKe '%{$serial}%' ";
            }*/
            // $si .= " GROUP BY {$this->db->dbprefix('bil_items')}.bil_id ) FSI";
            
            $this->load->library('datatables');
            $this->datatables
                ->select("FSI.item_nane, SUM( COALESCE( FSI.quantity, 0 ) ) as Qty,SUM( COALESCE( FSI.subtotal, 0 ) ) as subtotal, biller, customer,  grand_total, paid, (grand_total-paid) as balance, payment_status, {$this->db->dbprefix('bils')}.id as id", FALSE)
                ->from('bils')
                ->join($si, 'FSI.bil_id=bils.id', 'left')
                ->join('recipe', 'recipe.id=FSI.product_id', 'left')
                ->group_by('FSI.product_id');
            if ($user) {
                $this->datatables->where('bils.created_by', $user);
            }
            if ($product) {
                $this->datatables->where('FSI.product_id', $product);
            }
            /*if ($serial) {
                $this->datatables->like('FSI.serial_no', $serial);
            }*/
            if ($biller) {
                $this->datatables->where('bils.biller_id', $biller);
            }
            if ($customer) {
                $this->datatables->where('bils.customer_id', $customer);
            }
            /*if ($warehouse) {
                $this->datatables->where('bils.warehouse_id', $warehouse);
            }*/
            if($this->report_view_access != 1)
             {
                 $this->datatables->where('bils.table_whitelisted', $this->report_show);
             }

            if ($reference_no) {
                $this->datatables->like('bils.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->datatables->where($this->db->dbprefix('bils').'.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            echo $this->datatables->generate();

        }

    }    
    function categories()
    {
        $this->sma->checkPermissions();
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['categories'] = $this->site->getAllCategories();
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        if ($this->input->post('start_date')) {
            $dt = "From " . $this->input->post('start_date') . " to " . $this->input->post('end_date');
        } else {
            $dt = "Till " . $this->input->post('end_date');
        }
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('categories_report')));
        $meta = array('page_title' => lang('categories_report'), 'bc' => $bc);
        $this->page_construct('reports/categories', $meta, $this->data);
    }

    function getCategoriesReport($pdf = NULL, $xls = NULL)
    {
        $this->sma->checkPermissions('categories', TRUE);
        $warehouse = $this->input->get('warehouse') ? $this->input->get('warehouse') : NULL;
        $category = $this->input->get('category') ? $this->input->get('category') : NULL;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
        $end_date = $this->input->get('end_date') ? $this->input->get('end_date') : NULL;

        $pp = "( SELECT pp.category_id as category, SUM( pi.quantity ) purchasedQty, SUM( pi.subtotal ) totalPurchase from {$this->db->dbprefix('products')} pp
                left JOIN " . $this->db->dbprefix('purchase_items') . " pi ON pp.id = pi.product_id
                left join " . $this->db->dbprefix('purchases') . " p ON p.id = pi.purchase_id ";

        $sp = "( SELECT sp.category_id as category, SUM( si.quantity ) soldQty, SUM( si.subtotal ) totalSale12,SUM(DISTINCT s.total-s.total_discount+CASE WHEN (s.tax_type = 1) THEN s.total_tax ELSE 0 END) as totalSale
         from {$this->db->dbprefix('recipe')} sp
                left JOIN " . $this->db->dbprefix('bil_items') . " si ON sp.id = si.recipe_id
                left join " . $this->db->dbprefix('bils') . " s ON s.id = si.bil_id ";
            
        if ($start_date || $warehouse) {
            $pp .= " WHERE ";
            $sp .= " WHERE ";
            if ($start_date) {
                $start_date = $this->sma->fld($start_date);
                $end_date = $end_date ? $this->sma->fld($end_date) : date('Y-m-d');
                $pp .= " p.date >= '{$start_date}' AND p.date < '{$end_date}' ";
                $sp .= " s.date >= '{$start_date}' AND s.date < '{$end_date}' ";
                if ($warehouse) {
                    $pp .= " AND ";
                    $sp .= " AND ";
                }
            }
            if ($warehouse) {
                $pp .= " pi.warehouse_id = '{$warehouse}' ";
                $sp .= " si.warehouse_id = '{$warehouse}' ";
            }
        }
        if($this->report_view_access != 1)
            {
                $sp .= "AND s.table_whitelisted = {$this->report_show} ";                 
            }

        $pp .= " GROUP BY pp.category_id ) PCosts";
        $sp .= " GROUP BY sp.category_id ) PSales";

        if ($pdf || $xls) {

            $this->db
                ->select($this->db->dbprefix('recipe_categories') . ".code, " . $this->db->dbprefix('recipe_categories') . ".name,
                    SUM( COALESCE( PCosts.purchasedQty, 0 ) ) as PurchasedQty,
                    SUM( COALESCE( PSales.soldQty, 0 ) ) as SoldQty,
                    SUM( COALESCE( PCosts.totalPurchase, 0 ) ) as TotalPurchase,
                    SUM( COALESCE( PSales.totalSale, 0 ) ) as TotalSales,
                    (SUM( COALESCE( PSales.totalSale, 0 ) )- SUM( COALESCE( PCosts.totalPurchase, 0 ) ) ) as Profit", FALSE)
                ->from('recipe_categories')
                ->join($sp, 'recipe_categories.id = PSales.category', 'left')
                ->join($pp, 'recipe_categories.id = PCosts.category', 'left')
                ->group_by('recipe_categories.id, recipe_categories.code, recipe_categories.name')
                ->order_by('recipe_categories.code', 'asc');

            if ($category) {
                $this->db->where($this->db->dbprefix('recipe_categories') . ".id", $category);
            }
            $this->db->where($this->db->dbprefix('recipe_categories') . ".parent_id", 0);
            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('categories_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('category_code'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('category_name'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('purchased'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('sold'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('purchased_amount'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('sold_amount'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('profit_loss'));

                $row = 2;
                $sQty = 0;
                $pQty = 0;
                $sAmt = 0;
                $pAmt = 0;
                $pl = 0;
                foreach ($data as $data_row) {
                    $profit = $data_row->TotalSales - $data_row->TotalPurchase;
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->code);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->name);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->PurchasedQty);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->SoldQty);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->TotalPurchase);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->TotalSales);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $profit);
                    $pQty += $data_row->PurchasedQty;
                    $sQty += $data_row->SoldQty;
                    $pAmt += $data_row->TotalPurchase;
                    $sAmt += $data_row->TotalSales;
                    $pl += $profit;
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle("C" . $row . ":G" . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('C' . $row, $pQty);
                $this->excel->getActiveSheet()->SetCellValue('D' . $row, $sQty);
                $this->excel->getActiveSheet()->SetCellValue('E' . $row, $pAmt);
                $this->excel->getActiveSheet()->SetCellValue('F' . $row, $sAmt);
                $this->excel->getActiveSheet()->SetCellValue('G' . $row, $pl);

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('C2:G' . $row)->getAlignment()->setWrapText(true);
                $filename = 'categories_report';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {


            $this->load->library('datatables');
            $this->datatables
                ->select($this->db->dbprefix('recipe_categories') . ".id as cid,'sno', " .$this->db->dbprefix('recipe_categories') . ".code, " . $this->db->dbprefix('recipe_categories') . ".name,
                    SUM( COALESCE( PCosts.purchasedQty, 0 ) ) as PurchasedQty,
                    SUM( COALESCE( PSales.soldQty, 0 ) ) as SoldQty,
                    SUM( COALESCE( PCosts.totalPurchase, 0 ) ) as TotalPurchase,
                    SUM( COALESCE( PSales.totalSale, 0 ) ) as TotalSales,
                    (SUM( COALESCE( PSales.totalSale, 0 ) )- SUM( COALESCE( PCosts.totalPurchase, 0 ) ) ) as Profit", FALSE)
                ->from('recipe_categories')
                ->join($sp, 'recipe_categories.id = PSales.category', 'left')
                ->join($pp, 'recipe_categories.id = PCosts.category', 'left');

            if ($category) {
                $this->datatables->where('recipe_categories.id', $category);
            }
            $this->db->where($this->db->dbprefix('recipe_categories') . ".parent_id", 0);
            $this->datatables->group_by('recipe_categories.id, recipe_categories.code, recipe_categories.name, PSales.SoldQty, PSales.totalSale, PCosts.purchasedQty, PCosts.totalPurchase');
            $this->datatables->unset_column('cid');
            echo $this->datatables->generate();

        }

    }

    function brands()
    {
        $this->sma->checkPermissions();
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['brands'] = $this->site->getAllBrands();
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        if ($this->input->post('start_date')) {
            $dt = "From " . $this->input->post('start_date') . " to " . $this->input->post('end_date');
        } else {
            $dt = "Till " . $this->input->post('end_date');
        }
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('brands_report')));
        $meta = array('page_title' => lang('brands_report'), 'bc' => $bc);
        $this->page_construct('reports/brands', $meta, $this->data);
    }

    function getBrandsReport($pdf = NULL, $xls = NULL)
    {
        $this->sma->checkPermissions('brands', TRUE);
        $warehouse = $this->input->get('warehouse') ? $this->input->get('warehouse') : NULL;
        $brand = $this->input->get('brand') ? $this->input->get('brand') : NULL;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
        $end_date = $this->input->get('end_date') ? $this->input->get('end_date') : NULL;

        $pp = "( SELECT pp.brand as brand, SUM( pi.quantity ) purchasedQty, SUM( pi.subtotal ) totalPurchase from {$this->db->dbprefix('products')} pp
                left JOIN " . $this->db->dbprefix('purchase_items') . " pi ON pp.id = pi.product_id
                left join " . $this->db->dbprefix('purchases') . " p ON p.id = pi.purchase_id ";

        $sp = "( SELECT sp.brand as brand, SUM( si.quantity ) soldQty,SUM(s.total-s.total_discount+CASE WHEN (s.tax_type= 1) THEN s.total_tax ELSE 0 END) as totalSale, SUM( si.subtotal ) totalSale1 from {$this->db->dbprefix('products')} sp
                left JOIN " . $this->db->dbprefix('bil_items') . " si ON sp.id = si.recipe_id
                left join " . $this->db->dbprefix('bils') . " s ON s.id = si.bil_id ";
                /*echo $sp;die;*/
        if ($start_date || $warehouse) {
            $pp .= " WHERE ";
            $sp .= " WHERE ";
            if ($start_date) {
                $start_date = $this->sma->fld($start_date);
                $end_date = $end_date ? $this->sma->fld($end_date) : date('Y-m-d');
                $pp .= " p.date >= '{$start_date}' AND p.date < '{$end_date}' ";
                $sp .= " s.date >= '{$start_date}' AND s.date < '{$end_date}' ";
                if ($warehouse) {
                    $pp .= " AND ";
                    $sp .= " AND ";
                }
            }
            if ($warehouse) {
                $pp .= " pi.warehouse_id = '{$warehouse}' ";
                $sp .= " si.warehouse_id = '{$warehouse}' ";
            }
        }
        
        $pp .= " GROUP BY pp.brand ) PCosts";
        $sp .= " GROUP BY sp.brand ) PSales";

        if ($pdf || $xls) {

            $this->db
                ->select($this->db->dbprefix('brands') . ".name,
                    SUM( COALESCE( PCosts.purchasedQty, 0 ) ) as PurchasedQty,
                    SUM( COALESCE( PSales.soldQty, 0 ) ) as SoldQty,
                    SUM( COALESCE( PCosts.totalPurchase, 0 ) ) as TotalPurchase,
                    SUM( COALESCE( PSales.totalSale, 0 ) ) as TotalSales,
                    (SUM( COALESCE( PSales.totalSale, 0 ) )- SUM( COALESCE( PCosts.totalPurchase, 0 ) ) ) as Profit", FALSE)
                ->from('brands')
                ->join($sp, 'brands.id = PSales.brand', 'left')
                ->join($pp, 'brands.id = PCosts.brand', 'left')
                ->group_by('brands.id, brands.name')
                ->order_by('brands.code', 'asc');

            if ($brand) {
                $this->db->where($this->db->dbprefix('brands') . ".id", $brand);
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('brands_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('brands'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('purchased'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('sold'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('purchased_amount'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('sold_amount'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('profit_loss'));

                $row = 2; $sQty = 0; $pQty = 0; $sAmt = 0; $pAmt = 0; $pl = 0;
                foreach ($data as $data_row) {
                    $profit = $data_row->TotalSales - $data_row->TotalPurchase;
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->name);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->PurchasedQty);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->SoldQty);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->TotalPurchase);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->TotalSales);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $profit);
                    $pQty += $data_row->PurchasedQty;
                    $sQty += $data_row->SoldQty;
                    $pAmt += $data_row->TotalPurchase;
                    $sAmt += $data_row->TotalSales;
                    $pl += $profit;
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle("B" . $row . ":F" . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('B' . $row, $pQty);
                $this->excel->getActiveSheet()->SetCellValue('C' . $row, $sQty);
                $this->excel->getActiveSheet()->SetCellValue('D' . $row, $pAmt);
                $this->excel->getActiveSheet()->SetCellValue('E' . $row, $sAmt);
                $this->excel->getActiveSheet()->SetCellValue('F' . $row, $pl);

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('C2:G' . $row)->getAlignment()->setWrapText(true);
                $filename = 'brands_report';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {


            $this->load->library('datatables');
            $this->datatables
                ->select($this->db->dbprefix('brands') . ".id as id,'sno', " . $this->db->dbprefix('brands') . ".name,
                    SUM( COALESCE( PCosts.purchasedQty, 0 ) ) as PurchasedQty,
                    SUM( COALESCE( PSales.soldQty, 0 ) ) as SoldQty,
                    SUM( COALESCE( PCosts.totalPurchase, 0 ) ) as TotalPurchase,
                    SUM( COALESCE( PSales.totalSale, 0 ) ) as TotalSales,
                    (SUM( COALESCE( PSales.totalSale, 0 ) )- SUM( COALESCE( PCosts.totalPurchase, 0 ) ) ) as Profit", FALSE)
                ->from('brands')
                ->join($sp, 'brands.id = PSales.brand', 'left')
                ->join($pp, 'brands.id = PCosts.brand', 'left');

            if ($brand) {
                $this->datatables->where('brands.id', $brand);
            }
            $this->datatables->group_by('brands.id, brands.name, PSales.SoldQty, PSales.totalSale, PCosts.purchasedQty, PCosts.totalPurchase');
            $this->datatables->unset_column('id');
            echo $this->datatables->generate();

        }

    }

    function profit($date = NULL, $warehouse_id = NULL, $re = NULL)
    {
        if ( ! $this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            $this->sma->md();
        }
        if ( ! $date) { $date = date('Y-m-d'); }
        $this->data['costing'] = $this->reports_model->getCosting($date, $warehouse_id);
        $this->data['discount'] = $this->reports_model->getOrderDiscount($date, $warehouse_id);
        $this->data['expenses'] = $this->reports_model->getExpenses($date, $warehouse_id);
        $this->data['returns'] = $this->reports_model->getReturns($date, $warehouse_id);
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['swh'] = $warehouse_id;
        $this->data['date'] = $date;
        if ($re) {
            echo $this->load->view($this->theme . 'reports/profit', $this->data, TRUE);
            exit();
        }
        $this->load->view($this->theme . 'reports/profit', $this->data);
    }
    function monthly_profit($year, $month, $warehouse_id = NULL, $re = NULL)
    {
        if ( ! $this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            $this->sma->md();
        }

        $this->data['costing'] = $this->reports_model->getCosting(NULL, $warehouse_id, $year, $month);
        $this->data['discount'] = $this->reports_model->getOrderDiscount(NULL, $warehouse_id, $year, $month);
        $this->data['expenses'] = $this->reports_model->getExpenses(NULL, $warehouse_id, $year, $month);
        $this->data['returns'] = $this->reports_model->getReturns(NULL, $warehouse_id, $year, $month);
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['swh'] = $warehouse_id;
        $this->data['year'] = $year;
        $this->data['month'] = $month;
        $this->data['date'] = date('F Y', strtotime($year.'-'.$month.'-'.'01'));
        if ($re) {
            echo $this->load->view($this->theme . 'reports/monthly_profit', $this->data, TRUE);
            exit();
        }
        $this->load->view($this->theme . 'reports/monthly_profit', $this->data);
    }

function days_reports()
    {
        $this->sma->checkPermissions();
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->reports_model->getStaff();        
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('day_wise_sale_report')));
        $meta = array('page_title' => lang('day_wise_sale_report'), 'bc' => $bc);
        $this->settings = $this->reports_model->getSettings();
        $this->data['default_currency'] = $this->settings->default_currency;
        $this->page_construct('reports/days_reports', $meta, $this->data);
    }
      public function get_daysreports($start_date = NULL, $end_date = NULL, $warehouse_id = NULL, $defalut_currency = NULL){
        $this->sma->checkPermissions('pos_settlement',TRUE);
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $warehouse_id = $this->input->post('warehouse_id');
        $day = $this->input->post('day');
        /*echo $day;die;*/
        $limit = $this->input->post('pagelimit');        
        $offsetSegment = 4;
        $offset = $this->uri->segment($offsetSegment,0);
        $defalut_currency = $this->input->post('defalut_currency');
        $data= '';
        if ($end_date != '' && $end_date != '') {
            $data = $this->reports_model->getDaysreport($start_date,$end_date,$warehouse_id,$day,$defalut_currency,$limit,$offset);
            /*echo "<pre>";
            print_r($data);die;*/
            if (!empty($data['data'])){
                 
                 $settlements = $data['data'];
             }
             else{
                
                $settlements = 'empty';
             }
        }
        else{
            $settlements = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('reports/get_daysreports',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('settlements' => $settlements,'pagination'=>$pagination));
   } 
    function daily_sales($warehouse_id = NULL, $year = NULL, $month = NULL, $pdf = NULL, $user_id = NULL)
    {

        $this->sma->checkPermissions();

        if (!$this->Owner && !$this->Admin && $this->session->userdata('warehouse_id')) {
            $warehouse_id = $this->session->userdata('warehouse_id');
        }
        if (!$year) {
            $year = date('Y');
        }
        if (!$month) {
            $month = date('m');
        }
        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user_id = $this->session->userdata('user_id');
        }

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $config = array(
            'show_next_prev' => TRUE,
            'next_prev_url' => admin_url('reports/daily_sales/'.($warehouse_id ? $warehouse_id : 0)),
            'month_type' => 'long',
            'day_type' => 'long'
        );

        $config['template'] = '{table_open}<div class="table-responsive"><table border="0" cellpadding="0" cellspacing="0" class="table table-bordered dfTable">{/table_open}
        {heading_row_start}<tr>{/heading_row_start}
        {heading_previous_cell}<th><a href="{previous_url}">&lt;&lt;</a></th>{/heading_previous_cell}
        {heading_title_cell}<th colspan="{colspan}" id="month_year">{heading}</th>{/heading_title_cell}
        {heading_next_cell}<th><a href="{next_url}">&gt;&gt;</a></th>{/heading_next_cell}
        {heading_row_end}</tr>{/heading_row_end}
        {week_row_start}<tr>{/week_row_start}
        {week_day_cell}<td class="cl_wday">{week_day}</td>{/week_day_cell}
        {week_row_end}</tr>{/week_row_end}
        {cal_row_start}<tr class="days">{/cal_row_start}
        {cal_cell_start}<td class="day">{/cal_cell_start}
        {cal_cell_content}
        <div class="day_num">{day}</div>
        <div class="content">{content}</div>
        {/cal_cell_content}
        {cal_cell_content_today}
        <div class="day_num highlight">{day}</div>
        <div class="content">{content}</div>
        {/cal_cell_content_today}
        {cal_cell_no_content}<div class="day_num">{day}</div>{/cal_cell_no_content}
        {cal_cell_no_content_today}<div class="day_num highlight">{day}</div>{/cal_cell_no_content_today}
        {cal_cell_blank}&nbsp;{/cal_cell_blank}
        {cal_cell_end}</td>{/cal_cell_end}
        {cal_row_end}</tr>{/cal_row_end}
        {table_close}</table></div>{/table_close}';


        $this->load->library('calendar', $config);

                $sales = $user_id ? $this->reports_model->getStaffDailySales($user_id, $year, $month, $warehouse_id,$this->report_view_access,$this->report_show) : $this->reports_model->getDailySales($year, $month, $warehouse_id,$this->report_view_access,$this->report_show);
        
        if (!empty($sales)) {

            foreach ($sales as $sale) {
                $daily_sale[$sale->date] = "<table class='table table-bordered table-hover table-striped table-condensed data' style='margin:0;'><tr><td>" . lang("total") . "</td><td>" . $this->sma->formatMoney($sale->total) . "</td></tr><tr><td>" . lang("discount") . "</td><td>" . $this->sma->formatMoney($sale->discount) . "</td></tr><tr><td>" . lang("tax") . "</td><td>" . $this->sma->formatMoney($sale->tax) . "</td></tr><tr><td>" . lang("grand_total") . "</td><td>" . $this->sma->formatMoney($sale->grand_total) . "</td></tr></table>";
            }
        } else {
            $daily_sale = array();
        }

        $this->data['calender'] = $this->calendar->generate($year, $month, $daily_sale);
        $this->data['year'] = $year;
        $this->data['month'] = $month;
        if ($pdf) {
            $html = $this->load->view($this->theme . 'reports/daily', $this->data, true);
            $name = lang("daily_sales") . "_" . $year . "_" . $month . ".pdf";
            $html = str_replace('<p class="introtext">' . lang("reports_calendar_text") . '</p>', '', $html);
            $this->sma->generate_pdf($html, $name, null, null, null, null, null, 'L');
        }
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['warehouse_id'] = $warehouse_id;
        $this->data['sel_warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : NULL;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('daily_sales_report')));
        $meta = array('page_title' => lang('daily_sales_report'), 'bc' => $bc);
        $this->page_construct('reports/daily', $meta, $this->data);

    }


    function monthly_sales($warehouse_id = NULL, $year = NULL, $pdf = NULL, $user_id = NULL)
    {
        $this->sma->checkPermissions();
        if (!$this->Owner && !$this->Admin && $this->session->userdata('warehouse_id')) {
            $warehouse_id = $this->session->userdata('warehouse_id');
        }
        if (!$year) {
            $year = date('Y');
        }
        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->load->language('calendar');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['year'] = $year;
        $this->data['sales'] = $user_id ? $this->reports_model->getStaffMonthlySales($user_id, $year, $warehouse_id,$this->report_view_access,$this->report_show) : $this->reports_model->getMonthlySales($year, $warehouse_id,$this->report_view_access,$this->report_show);
        if ($pdf) {
            $html = $this->load->view($this->theme . 'reports/monthly', $this->data, true);
            $name = lang("monthly_sales") . "_" . $year . ".pdf";
            $html = str_replace('<p class="introtext">' . lang("reports_calendar_text") . '</p>', '', $html);
            $this->sma->generate_pdf($html, $name, null, null, null, null, null, 'L');
        }
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['warehouse_id'] = $warehouse_id;
        $this->data['sel_warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : NULL;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('monthly_sales_report')));
        $meta = array('page_title' => lang('monthly_sales_report'), 'bc' => $bc);
        $this->page_construct('reports/monthly', $meta, $this->data);

    }

    function sales()
    {
        $this->sma->checkPermissions('sales');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->reports_model->getStaff();
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('sales_report')));
        $meta = array('page_title' => lang('sales_report'), 'bc' => $bc);
        $this->page_construct('reports/sales', $meta, $this->data);
    }

    function getSalesReport($pdf = NULL, $xls = NULL)
    {
        
        $this->sma->checkPermissions('sales', TRUE);
        $product = $this->input->get('product') ? $this->input->get('product') : NULL;
        $user = $this->input->get('user') ? $this->input->get('user') : NULL;
        $customer = $this->input->get('customer') ? $this->input->get('customer') : NULL;
        $biller = $this->input->get('biller') ? $this->input->get('biller') : NULL;
        $warehouse = $this->input->get('warehouse') ? $this->input->get('warehouse') : NULL;
        $reference_no = $this->input->get('reference_no') ? $this->input->get('reference_no') : NULL;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
        $end_date = $this->input->get('end_date') ? $this->input->get('end_date') : NULL;
        $serial = $this->input->get('serial') ? $this->input->get('serial') : NULL;

        if ($start_date) {
            $start_date = $this->sma->fld($start_date);
            $end_date = $this->sma->fld($end_date);
        }
        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user = $this->session->userdata('user_id');
        }

        if ($pdf || $xls) {

            $this->db
                ->select("date, reference_no, biller, customer, GROUP_CONCAT(CONCAT(" . $this->db->dbprefix('bil_items') . ".recipe_name, ' (', " . $this->db->dbprefix('bil_items') . ".quantity, ')') SEPARATOR '\n') as iname, grand_total, paid, payment_status", FALSE)
                ->from('bils')
                ->join('bil_items', 'bil_items.bil_id=bils.id', 'left')
                /*->join('warehouses', 'warehouses.id=bils.warehouse_id', 'left')*/
                ->group_by('bils.id')
                ->order_by('bils.date desc');

            if ($user) {
                $this->db->where('bils.created_by', $user);
            }
            $this->db->where('bils.payment_status', 'Completed');
            if ($product) {
                $this->db->where('bil_items.recipe_id', $product);
            }
            /*if ($serial) {
                $this->db->like('bil_items.serial_no', $serial);
            }*/
            if ($biller) {
                $this->db->where('bils.biller_id', $biller);
            }
            if ($customer) {
                $this->db->where('bils.customer_id', $customer);
            }
           /* if ($warehouse) {
                $this->db->where('bils.warehouse_id', $warehouse);
            }*/
            if ($reference_no) {
                $this->db->like('bils.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->db->where($this->db->dbprefix('bils').'.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }
            if($this->report_view_access != 1){
                $this->db->where('bils.table_whitelisted', $this->report_show); 
            }
            $q = $this->db->get();
            
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {

                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {
                $h_color = $this->Settings->excel_header_color;
                $f_color = $this->Settings->excel_footer_color;
                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('sales_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                $this->excel->getActiveSheet()->getStyle('A1:I1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($h_color);
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference_no'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('biller'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('customer'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('product_qty'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('grand_total'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('paid'));
                $this->excel->getActiveSheet()->SetCellValue('H1', lang('balance'));
                $this->excel->getActiveSheet()->SetCellValue('I1', lang('payment_status'));
                
                $row = 2;
                $total = 0;
                $paid = 0;
                $balance = 0;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($data_row->date));
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->reference_no);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->biller);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->customer);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->iname);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->grand_total);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->paid);
                    $this->excel->getActiveSheet()->SetCellValue('H' . $row, ($data_row->grand_total - $data_row->paid));
                    $this->excel->getActiveSheet()->SetCellValue('I' . $row, lang($data_row->payment_status));
                    $total += $data_row->grand_total;
                    $paid += $data_row->paid;
                    $balance += ($data_row->grand_total - $data_row->paid);
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle("F" . $row . ":H" . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('F' . $row, $total);
                $this->excel->getActiveSheet()->SetCellValue('G' . $row, $paid);
                $this->excel->getActiveSheet()->SetCellValue('H' . $row, $balance);

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('E2:E' . $row)->getAlignment()->setWrapText(true);
                $filename = 'sales_report';
                $excelLastRow = $this->excel->setActiveSheetIndex(0)->getHighestRow();
                $this->excel->getActiveSheet()->getStyle('A'.$excelLastRow.':I'.$excelLastRow)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($f_color);
                $this->load->helper('excel');
                create_excel($this->excel, $filename);

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {
            $si = "( SELECT bil_id as bil_id, recipe_id as product_id from {$this->db->dbprefix('bil_items')} ";
            if ($product || $serial) { $si .= " WHERE "; }
            if ($product) {
                $si .= " {$this->db->dbprefix('bil_items')}.recipe_id = {$product} ";
            }
            if ($product && $serial) { $si .= " AND "; }
           /* if ($serial) {
                $si .= " {$this->db->dbprefix('bil_items')}.serial_no LIKe '%{$serial}%' ";
            }*/
            $si .= " GROUP BY {$this->db->dbprefix('bil_items')}.bil_id ) FSI";
            $this->load->library('datatables');
            $this->datatables
                ->select("'sno',DATE_FORMAT(date, '%d-%m-%Y') as date,{$this->db->dbprefix('warehouses')}.name as branch, reference_no, biller, customer, grand_total, paid, (grand_total-paid) as balance, payment_status, {$this->db->dbprefix('bils')}.id as id", FALSE)
                ->from('bils')
                ->join($si, 'FSI.bil_id=bils.id', 'left')
                ->join('warehouses', 'warehouses.id=bils.warehouse_id', 'left');
                // ->group_by('sales.id');

            if ($user) {
                $this->datatables->where('bils.created_by', $user);
            }
            $this->db->where('bils.payment_status', 'Completed');
            if ($product) {
                $this->datatables->where('FSI.product_id', $product);
            }
            /*if ($serial) {
                $this->datatables->like('FSI.serial_no', $serial);
            }*/
            if ($biller) {
                $this->datatables->where('bils.biller_id', $biller);
            }
            if ($customer) {
                $this->datatables->where('bils.customer_id', $customer);
            }
            /*if ($warehouse) {
                $this->datatables->where('bils.warehouse_id', $warehouse);
            }*/
            if ($reference_no) {
                $this->datatables->like('bils.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->datatables->where($this->db->dbprefix('bils').'.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }
            if($this->report_view_access != 1){
                $this->db->where('bils.table_whitelisted', $this->report_show); 
                
            }
/*echo "string";die;*/
            echo $this->datatables->generate();

        }

    }

    function getQuotesReport($pdf = NULL, $xls = NULL)
    {

        if ($this->input->get('product')) {
            $product = $this->input->get('product');
        } else {
            $product = NULL;
        }
        if ($this->input->get('user')) {
            $user = $this->input->get('user');
        } else {
            $user = NULL;
        }
        if ($this->input->get('customer')) {
            $customer = $this->input->get('customer');
        } else {
            $customer = NULL;
        }
        if ($this->input->get('biller')) {
            $biller = $this->input->get('biller');
        } else {
            $biller = NULL;
        }
        if ($this->input->get('warehouse')) {
            $warehouse = $this->input->get('warehouse');
        } else {
            $warehouse = NULL;
        }
        if ($this->input->get('reference_no')) {
            $reference_no = $this->input->get('reference_no');
        } else {
            $reference_no = NULL;
        }
        if ($this->input->get('start_date')) {
            $start_date = $this->input->get('start_date');
        } else {
            $start_date = NULL;
        }
        if ($this->input->get('end_date')) {
            $end_date = $this->input->get('end_date');
        } else {
            $end_date = NULL;
        }
        if ($start_date) {
            $start_date = $this->sma->fld($start_date);
            $end_date = $this->sma->fld($end_date);
        }
        if ($pdf || $xls) {

            $this->db
                ->select("date, reference_no, biller, customer, GROUP_CONCAT(CONCAT(" . $this->db->dbprefix('quote_items') . ".product_name, ' (', " . $this->db->dbprefix('quote_items') . ".quantity, ')') SEPARATOR '<br>') as iname, grand_total, status", FALSE)
                ->from('quotes')
                ->join('quote_items', 'quote_items.quote_id=quotes.id', 'left')
                ->join('warehouses', 'warehouses.id=quotes.warehouse_id', 'left')
                ->group_by('quotes.id');

            if ($user) {
                $this->db->where('quotes.created_by', $user);
            }
            if ($product) {
                $this->db->where('quote_items.product_id', $product);
            }
            if ($biller) {
                $this->db->where('quotes.biller_id', $biller);
            }
            if ($customer) {
                $this->db->where('quotes.customer_id', $customer);
            }
            if ($warehouse) {
                $this->db->where('quotes.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->db->like('quotes.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->db->where($this->db->dbprefix('quotes').'.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('quotes_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference_no'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('biller'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('customer'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('product_qty'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('grand_total'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('status'));

                $row = 2;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($data_row->date));
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->reference_no);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->biller);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->customer);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->iname);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->grand_total);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->status);
                    $row++;
                }

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('E2:E' . $row)->getAlignment()->setWrapText(true);
                $filename = 'quotes_report';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {

            $qi = "( SELECT quote_id, product_id, GROUP_CONCAT(CONCAT({$this->db->dbprefix('quote_items')}.product_name, '__', {$this->db->dbprefix('quote_items')}.quantity) SEPARATOR '___') as item_nane from {$this->db->dbprefix('quote_items')} ";
            if ($product) {
                $qi .= " WHERE {$this->db->dbprefix('quote_items')}.product_id = {$product} ";
            }
            $qi .= " GROUP BY {$this->db->dbprefix('quote_items')}.quote_id ) FQI";
            $this->load->library('datatables');
            $this->datatables
                ->select("'sno',date, reference_no, biller, customer, FQI.item_nane as iname, grand_total, status, {$this->db->dbprefix('quotes')}.id as id", FALSE)
                ->from('quotes')
                ->join($qi, 'FQI.quote_id=quotes.id', 'left')
                ->join('warehouses', 'warehouses.id=quotes.warehouse_id', 'left')
                ->group_by('quotes.id');

            if ($user) {
                $this->datatables->where('quotes.created_by', $user);
            }
            if ($product) {
                $this->datatables->where('FQI.product_id', $product, FALSE);
            }
            if ($biller) {
                $this->datatables->where('quotes.biller_id', $biller);
            }
            if ($customer) {
                $this->datatables->where('quotes.customer_id', $customer);
            }
            if ($warehouse) {
                $this->datatables->where('quotes.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->datatables->like('quotes.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->datatables->where($this->db->dbprefix('quotes').'.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            echo $this->datatables->generate();

        }

    }

    function getTransfersReport($pdf = NULL, $xls = NULL)
    {
        if ($this->input->get('product')) {
            $product = $this->input->get('product');
        } else {
            $product = NULL;
        }

        if ($pdf || $xls) {

            $this->db
                ->select($this->db->dbprefix('transfers') . ".date, transfer_no, (CASE WHEN " . $this->db->dbprefix('transfers') . ".status = 'completed' THEN  GROUP_CONCAT(CONCAT(" . $this->db->dbprefix('purchase_items') . ".product_name, ' (', " . $this->db->dbprefix('purchase_items') . ".quantity, ')') SEPARATOR '<br>') ELSE GROUP_CONCAT(CONCAT(" . $this->db->dbprefix('transfer_items') . ".product_name, ' (', " . $this->db->dbprefix('transfer_items') . ".quantity, ')') SEPARATOR '<br>') END) as iname, from_warehouse_name as fname, from_warehouse_code as fcode, to_warehouse_name as tname,to_warehouse_code as tcode, grand_total, " . $this->db->dbprefix('transfers') . ".status")
                ->from('transfers')
                ->join('transfer_items', 'transfer_items.transfer_id=transfers.id', 'left')
                ->join('purchase_items', 'purchase_items.transfer_id=transfers.id', 'left')
                ->group_by('transfers.id')->order_by('transfers.date desc');
            if ($product) {
                $this->db->where($this->db->dbprefix('purchase_items') . ".product_id", $product);
                $this->db->or_where($this->db->dbprefix('transfer_items') . ".product_id", $product);
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('transfers_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('transfer_no'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('product_qty'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('warehouse') . ' (' . lang('from') . ')');
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('warehouse') . ' (' . lang('to') . ')');
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('grand_total'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('status'));

                $row = 2;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($data_row->date));
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->transfer_no);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->iname);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->fname . ' (' . $data_row->fcode . ')');
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->tname . ' (' . $data_row->tcode . ')');
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->grand_total);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->status);
                    $row++;
                }

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('C2:C' . $row)->getAlignment()->setWrapText(true);
                $filename = 'transfers_report';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {

            $this->load->library('datatables');
            $this->datatables
                ->select("{$this->db->dbprefix('transfers')}.date, transfer_no, (CASE WHEN {$this->db->dbprefix('transfers')}.status = 'completed' THEN  GROUP_CONCAT(CONCAT({$this->db->dbprefix('purchase_items')}.product_name, '__', {$this->db->dbprefix('purchase_items')}.quantity) SEPARATOR '___') ELSE GROUP_CONCAT(CONCAT({$this->db->dbprefix('transfer_items')}.product_name, '__', {$this->db->dbprefix('transfer_items')}.quantity) SEPARATOR '___') END) as iname, from_warehouse_name as fname, from_warehouse_code as fcode, to_warehouse_name as tname,to_warehouse_code as tcode, grand_total, {$this->db->dbprefix('transfers')}.status, {$this->db->dbprefix('transfers')}.id as id", FALSE)
                ->from('transfers')
                ->join('transfer_items', 'transfer_items.transfer_id=transfers.id', 'left')
                ->join('purchase_items', 'purchase_items.transfer_id=transfers.id', 'left')
                ->group_by('transfers.id');
            if ($product) {
                $this->datatables->where(" (({$this->db->dbprefix('purchase_items')}.product_id = {$product}) OR ({$this->db->dbprefix('transfer_items')}.product_id = {$product})) ", NULL, FALSE);
            }
            $this->datatables->edit_column("fname", "$1 ($2)", "fname, fcode")
                ->edit_column("tname", "$1 ($2)", "tname, tcode")
                ->unset_column('fcode')
                ->unset_column('tcode');
            echo $this->datatables->generate();

        }

    }

    function purchases()
    {
        $this->sma->checkPermissions('purchases');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->reports_model->getStaff();
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('purchases_report')));
        $meta = array('page_title' => lang('purchases_report'), 'bc' => $bc);
        $this->page_construct('reports/purchases', $meta, $this->data);
    }

    function getPurchasesReport($pdf = NULL, $xls = NULL)
    {
        $this->sma->checkPermissions('purchases', TRUE);

        $product = $this->input->get('product') ? $this->input->get('product') : NULL;
        $user = $this->input->get('user') ? $this->input->get('user') : NULL;
        $supplier = $this->input->get('supplier') ? $this->input->get('supplier') : NULL;
        $warehouse = $this->input->get('warehouse') ? $this->input->get('warehouse') : NULL;
        $reference_no = $this->input->get('reference_no') ? $this->input->get('reference_no') : NULL;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
        $end_date = $this->input->get('end_date') ? $this->input->get('end_date') : NULL;

        if ($start_date) {
            $start_date = $this->sma->fld($start_date);
            $end_date = $this->sma->fld($end_date);
        }
        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user = $this->session->userdata('user_id');
        }

        if ($pdf || $xls) {

            $this->db
                ->select("" . $this->db->dbprefix('purchases') . ".date, reference_no, " . $this->db->dbprefix('warehouses') . ".name as wname, supplier, GROUP_CONCAT(CONCAT(" . $this->db->dbprefix('purchase_items') . ".product_name, ' (', " . $this->db->dbprefix('purchase_items') . ".quantity, ')') SEPARATOR '\n') as iname, grand_total, paid, " . $this->db->dbprefix('purchases') . ".status", FALSE)
                ->from('purchases')
                ->join('purchase_items', 'purchase_items.purchase_id=purchases.id', 'left')
                ->join('warehouses', 'warehouses.id=purchases.warehouse_id', 'left')
                ->group_by('purchases.id')
                ->order_by('purchases.date desc');

            if ($user) {
                $this->db->where('purchases.created_by', $user);
            }
            if ($product) {
                $this->db->where('purchase_items.product_id', $product);
            }
            if ($supplier) {
                $this->db->where('purchases.supplier_id', $supplier);
            }
            if ($warehouse) {
                $this->db->where('purchases.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->db->like('purchases.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->db->where($this->db->dbprefix('purchases').'.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('purchase_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference_no'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('warehouse'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('supplier'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('product_qty'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('grand_total'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('paid'));
                $this->excel->getActiveSheet()->SetCellValue('H1', lang('balance'));
                $this->excel->getActiveSheet()->SetCellValue('I1', lang('status'));

                $row = 2;
                $total = 0;
                $paid = 0;
                $balance = 0;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($data_row->date));
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->reference_no);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->wname);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->supplier);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->iname);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->grand_total);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->paid);
                    $this->excel->getActiveSheet()->SetCellValue('H' . $row, ($data_row->grand_total - $data_row->paid));
                    $this->excel->getActiveSheet()->SetCellValue('I' . $row, $data_row->status);
                    $total += $data_row->grand_total;
                    $paid += $data_row->paid;
                    $balance += ($data_row->grand_total - $data_row->paid);
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle("F" . $row . ":H" . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('F' . $row, $total);
                $this->excel->getActiveSheet()->SetCellValue('G' . $row, $paid);
                $this->excel->getActiveSheet()->SetCellValue('H' . $row, $balance);

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('E2:E' . $row)->getAlignment()->setWrapText(true);
                $filename = 'purchase_report';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {

            $pi = "( SELECT purchase_id, product_id, (GROUP_CONCAT(CONCAT({$this->db->dbprefix('purchase_items')}.product_name, '__', {$this->db->dbprefix('purchase_items')}.quantity) SEPARATOR '___')) as item_nane from {$this->db->dbprefix('purchase_items')} ";
            if ($product) {
                $pi .= " WHERE {$this->db->dbprefix('purchase_items')}.product_id = {$product} ";
            }
            $pi .= " GROUP BY {$this->db->dbprefix('purchase_items')}.purchase_id ) FPI";

            $this->load->library('datatables');
            $this->datatables
                ->select("'sno',DATE_FORMAT({$this->db->dbprefix('purchases')}.date, '%Y-%m-%d %T') as date, reference_no, {$this->db->dbprefix('warehouses')}.name as wname, supplier, (FPI.item_nane) as iname, grand_total, paid, (grand_total-paid) as balance, {$this->db->dbprefix('purchases')}.status, {$this->db->dbprefix('purchases')}.id as id", FALSE)
                ->from('purchases')
                ->join($pi, 'FPI.purchase_id=purchases.id', 'left')
                ->join('warehouses', 'warehouses.id=purchases.warehouse_id', 'left');
                // ->group_by('purchases.id');

            if ($user) {
                $this->datatables->where('purchases.created_by', $user);
            }
            if ($product) {
                $this->datatables->where('FPI.product_id', $product, FALSE);
            }
            if ($supplier) {
                $this->datatables->where('purchases.supplier_id', $supplier);
            }
            if ($warehouse) {
                $this->datatables->where('purchases.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->datatables->like('purchases.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->datatables->where($this->db->dbprefix('purchases').'.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            echo $this->datatables->generate();

        }

    }

    function payments()
    {
        $this->sma->checkPermissions('payments');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->reports_model->getStaff();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $this->data['pos_settings'] = POS ? $this->reports_model->getPOSSetting('biller') : FALSE;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('payments_report')));
        $meta = array('page_title' => lang('payments_report'), 'bc' => $bc);
        $this->page_construct('reports/payments', $meta, $this->data);
    }

    function getPaymentsReport($pdf = NULL, $xls = NULL)
    {
        $this->sma->checkPermissions('payments', TRUE);

        $user = $this->input->get('user') ? $this->input->get('user') : NULL;
        $supplier = $this->input->get('supplier') ? $this->input->get('supplier') : NULL;
        $customer = $this->input->get('customer') ? $this->input->get('customer') : NULL;
        $biller = $this->input->get('biller') ? $this->input->get('biller') : NULL;
        $payment_ref = $this->input->get('payment_ref') ? $this->input->get('payment_ref') : NULL;
        $paid_by = $this->input->get('paid_by') ? $this->input->get('paid_by') : NULL;
        $sale_ref = $this->input->get('sale_ref') ? $this->input->get('sale_ref') : NULL;
        $purchase_ref = $this->input->get('purchase_ref') ? $this->input->get('purchase_ref') : NULL;
        $card = $this->input->get('card') ? $this->input->get('card') : NULL;
        $cheque = $this->input->get('cheque') ? $this->input->get('cheque') : NULL;
        $transaction_id = $this->input->get('tid') ? $this->input->get('tid') : NULL;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
        $end_date = $this->input->get('end_date') ? $this->input->get('end_date') : NULL;
        $defalut_currency = $this->Settings->default_currency;
        if ($start_date) {
            $start_date = $this->sma->fsd($start_date);
            $end_date = $this->sma->fsd($end_date);
        }
        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user = $this->session->userdata('user_id');
        }
        if ($pdf || $xls) {


 /*->select("DATE_FORMAT({$this->db->dbprefix('payments')}.date, '%Y-%m-%d %T') as date,  " . $this->db->dbprefix('bils') . ".reference_no as sale_ref, " . $this->db->dbprefix('payments') . ".paid_by as paid_by ,( COALESCE(sum(amount), 0) + COALESCE(sum(amount_usd*4000), 0) - COALESCE(sum(pos_balance), 0)) as amount, type, {$this->db->dbprefix('payments')}.id as id")
                ->from('bils')
                ->join('payments', 'payments.bill_id = bils.id')
               
                ->group_by('payments.id');*/

/*GROUP_CONCAT(CONCAT(" . $this->db->dbprefix('payments') . ".paid_by, ' (', " . $this->db->dbprefix('purchase_items') . ".quantity, ')') SEPARATOR '\n') as paid_by,*/


/*(GROUP_CONCAT(CONCAT({$this->db->dbprefix('payments')}.paid_by, ) SEPARATOR ',')) as paid_by*/

            $this->db
                //->select("DATE_FORMAT({$this->db->dbprefix('payments')}.date, '%Y-%m-%d %T') as date, " . $this->db->dbprefix('bils') . ".reference_no as sale_ref, GROUP_CONCAT(CONCAT({$this->db->dbprefix('payments')}.paid_by, ) SEPARATOR ',') as paid_by,( COALESCE(sum(amount), 0) + COALESCE(sum(amount_usd*4000), 0) - COALESCE(sum(pos_balance), 0)) as amount, type")
                ->select("DATE_FORMAT({$this->db->dbprefix('payments')}.date, '%Y-%m-%d %T') as date, " . $this->db->dbprefix('bils') . ".reference_no as sale_ref, CONCAT('cash - ',SUM(DISTINCT CASE  WHEN ((srampos_payments.paid_by = 'cash') AND (srampos_sale_currency.currency_id=2) AND (srampos_sale_currency.amount!='')) THEN srampos_payments.amount 
WHEN ((srampos_payments.paid_by = 'cash' AND srampos_sale_currency.currency_id=1 AND srampos_sale_currency.amount!='')) THEN (srampos_sale_currency.amount*srampos_sale_currency.currency_rate) ELSE 0 END),' | CC - ',SUM(DISTINCT CASE
WHEN " . $this->db->dbprefix('payments') . ".paid_by = 'CC'  THEN {$this->db->dbprefix('payments')}.amount

ELSE 0 END),' | credit - ',SUM(DISTINCT CASE
WHEN " . $this->db->dbprefix('payments') . ".paid_by = 'credit'  THEN {$this->db->dbprefix('payments')}.amount

ELSE 0 END)) paid_by,{$this->db->dbprefix('bils')}.paid  as amount,type")
                ->from('bils')
                ->join('payments', 'payments.bill_id = bils.id','left')
               ->join('sale_currency', 'sale_currency.bil_id = bils.id')
                ->group_by('payments.id')
                ->order_by('payments.date desc');

            if ($user) {
                $this->db->where('payments.created_by', $user);
            }
            if ($card) {
                $this->db->like('payments.cc_no', $card, 'both');
            }
            if ($cheque) {
                $this->db->where('payments.cheque_no', $cheque);
            }
            if ($transaction_id) {
                $this->db->where('payments.transaction_id', $transaction_id);
            }
            if ($customer) {
                $this->db->where('sales.customer_id', $customer);
            }
           /* if ($supplier) {
                $this->db->where('purchases.supplier_id', $supplier);
            }*/
            if ($biller) {
                $this->db->where('sales.biller_id', $biller);
            }
            if ($customer) {
                $this->db->where('sales.customer_id', $customer);
            }
            if ($payment_ref) {
                $this->db->like('payments.reference_no', $payment_ref, 'both');
            }
            if ($paid_by) {
                $this->db->where('payments.paid_by', $paid_by);
            }
            if ($sale_ref) {
                $this->db->like('sales.reference_no', $sale_ref, 'both');
            }
            /*if ($purchase_ref) {
                $this->db->like('purchases.reference_no', $purchase_ref, 'both');
            }*/
            if ($start_date) {
                $this->db->where($this->db->dbprefix('payments').'.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }
            if($this->report_view_access != 1){
                $this->db->where('bils.table_whitelisted', $this->report_show); 
            }

            /*if(!$this->Owner && !$this->Admin){
            $this->db->where('bils.table_whitelisted', 0); 
            }*/

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('payments_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
               /* $this->excel->getActiveSheet()->SetCellValue('B1', lang('payment_reference'));*/
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('sale_reference'));
               /* $this->excel->getActiveSheet()->SetCellValue('D1', lang('purchase_reference'));*/
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('paid_by'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('amount'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('type'));

                $row = 2;
                $total = 0;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($data_row->date));
                   /* $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->payment_ref);*/
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->sale_ref);
                   /* $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->purchase_ref);*/
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, lang($data_row->paid_by));
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->amount);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->type);
                    if ($data_row->type == 'returned' || $data_row->type == 'sent') {
                        $total -= $data_row->amount;
                    } else {
                        $total += $data_row->amount;
                    }
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle("F" . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('F' . $row, $total);

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $filename = 'payments_report';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {

/*            GROUP_CONCAT(CONCAT(" . $this->db->dbprefix('payments') . ".paid_by, ' (', " . $this->db->dbprefix('payments') . ".quantity, ')') SEPARATOR '\n') as iname,*/

/*GROUP_CONCAT(CONCAT(" . $this->db->dbprefix('payments') . ".paid_by, ' (', " . $this->db->dbprefix('payments') . ".amount, ')') SEPARATOR '\n') as paid_by,*/


            $this->load->library('datatables');
            //$this->datatables
                //->select("DATE_FORMAT({$this->db->dbprefix('payments')}.date, '%Y-%m-%d %T') as date,  " . $this->db->dbprefix('bils') . ".reference_no as sale_ref, GROUP_CONCAT(CONCAT(" . $this->db->dbprefix('payments') . ".paid_by, ' (', " . $this->db->dbprefix('payments') . ".amount, ')') SEPARATOR '\n') as paid_by,( COALESCE(sum(amount), 0) + COALESCE(sum(amount_usd*4000), 0) - COALESCE(sum(pos_balance), 0)) as amount, type, {$this->db->dbprefix('payments')}.id as id")
                //->from('bils')
                //->join('payments', 'payments.bill_id = bils.id')
                //
                //->group_by('bils.id');
                
                //GROUP_CONCAT(CONCAT(" . $this->db->dbprefix('payments') . ".paid_by, ' (', " . $this->db->dbprefix('payments') . ".amount, ')') SEPARATOR '\n') as paid_by,
                
                //SUM(DISTINCT CASE WHEN ((" . $this->db->dbprefix('payments') . ".paid_by = 'cash') AND ({$this->db->dbprefix('sale_currency')}.currency_id != ".$defalut_currency.")) THEN amount_exchange*currency_rate ELSE {$this->db->dbprefix('bils')}.paid END) as For_Ex
                
               $this->datatables
                ->select("'sno',".$this->db->dbprefix('warehouses') . ".name as branch, DATE_FORMAT({$this->db->dbprefix('payments')}.date, '%Y-%m-%d %T') as date, bill_number, " . $this->db->dbprefix('bils') . ".reference_no as sale_ref, CONCAT('cash - ',SUM(DISTINCT CASE  WHEN ((srampos_payments.paid_by = 'cash') AND (srampos_sale_currency.currency_id=2) AND (srampos_sale_currency.amount!='')) THEN srampos_payments.amount 
WHEN ((srampos_payments.paid_by = 'cash' AND srampos_sale_currency.currency_id=1 AND srampos_sale_currency.amount!='')) THEN (srampos_sale_currency.amount*srampos_sale_currency.currency_rate) ELSE 0 END),' | CC - ',SUM(DISTINCT CASE
WHEN " . $this->db->dbprefix('payments') . ".paid_by = 'CC'  THEN {$this->db->dbprefix('payments')}.amount

ELSE 0 END),'| credit - ',SUM(DISTINCT CASE  WHEN ((srampos_payments.paid_by = 'credit') AND (srampos_sale_currency.currency_id=2) AND (srampos_sale_currency.amount!='')) THEN srampos_payments.amount 
WHEN ((srampos_payments.paid_by = 'credit' AND srampos_sale_currency.currency_id=1 AND srampos_sale_currency.amount!='')) THEN (srampos_sale_currency.amount*srampos_sale_currency.currency_rate) ELSE 0 END)) paid_by,{$this->db->dbprefix('bils')}.paid  as For_Ex,{$this->db->dbprefix('bils')}.balance,type, {$this->db->dbprefix('payments')}.id as id,{$this->db->dbprefix('payments')}.bill_id")
                ->from('bils')
                ->join('payments', 'payments.bill_id = bils.id')
                ->join('sale_currency', 'sale_currency.bil_id = bils.id')
                ->join('warehouses', 'warehouses.id = bils.warehouse_id')
                //->order_by('bils.id','ASC')
                ->group_by('bils.id');


            if ($user) {
                $this->datatables->where('payments.created_by', $user);
            }
            if ($card) {
                $this->datatables->like('payments.cc_no', $card, 'both');
            }
            if ($cheque) {
                $this->datatables->where('payments.cheque_no', $cheque);
            }
            if ($transaction_id) {
                $this->datatables->where('payments.transaction_id', $transaction_id);
            }
            if ($customer) {
                $this->datatables->where('bils.customer_id', $customer);
            }
            if ($supplier) {
                $this->datatables->where('purchases.supplier_id', $supplier);
            }
            if ($biller) {
                $this->datatables->where('bils.biller_id', $biller);
            }
            if ($customer) {
                $this->datatables->where('bils.customer_id', $customer);
            }
           /* if ($payment_ref) {
                $this->datatables->like('payments.reference_no', $payment_ref, 'both');
            }*/
            if ($paid_by) {
                $this->datatables->where('payments.paid_by', $paid_by);
            }
            if ($sale_ref) {
                $this->datatables->like('bils.reference_no', $sale_ref, 'both');
            }
          /*  if ($purchase_ref) {
                $this->datatables->like('purchases.reference_no', $purchase_ref, 'both');
            }*/
            if ($start_date) {
                $this->datatables->where($this->db->dbprefix('payments').'.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }
            if($this->report_view_access != 1){
                $this->db->where('bils.table_whitelisted', $this->report_show); 
            }

            
            //print_r($this->datatables);exit;
            /*echo "<pre>";
print_r($this->datatables->generate());die;  */
/*print_r($this->db->error());die;*/
            echo $this->datatables->generate();

        }

    }

    function customers()
    {
        $this->sma->checkPermissions('customers');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('customers_report')));
        $meta = array('page_title' => lang('customers_report'), 'bc' => $bc);
        $this->page_construct('reports/customers', $meta, $this->data);
    }

    function getCustomers($pdf = NULL, $xls = NULL)
    {
        $this->sma->checkPermissions('customers', TRUE);

        if ($pdf || $xls) {

            $this->db
                ->select($this->db->dbprefix('companies') . ".id as id, company, name, phone, email, count(" . $this->db->dbprefix('bils') . ".id) as total, COALESCE(sum(grand_total), 0) as total_amount, COALESCE(sum(paid), 0) as paid, ( COALESCE(sum(grand_total), 0) - COALESCE(sum(paid), 0)) as balance", FALSE)
                ->from("companies")
                ->join('bils', 'bils.customer_id=companies.id')
                ->where('companies.group_name', 'customer')
                ->where('bils.payment_status', 'Completed')
                ->order_by('companies.company asc')
                ->group_by('companies.id');

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('customers_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('company'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('name'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('phone'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('email'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('total_sales'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('total_amount'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('paid'));
                $this->excel->getActiveSheet()->SetCellValue('H1', lang('balance'));

                $row = 2;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->company);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->name);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->phone);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->email);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->total);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $this->sma->formatMoney($data_row->total_amount));
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $this->sma->formatMoney($data_row->paid));
                    $this->excel->getActiveSheet()->SetCellValue('H' . $row, $this->sma->formatMoney($data_row->balance));
                    $row++;
                }

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $filename = 'customers_report';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {

            $s = "( SELECT customer_id,payment_status, count(" . $this->db->dbprefix('bils') . ".id) as total, COALESCE(sum(grand_total), 0) as total_amount, COALESCE(sum(paid), 0) as paid, ( COALESCE(sum(grand_total), 0) - COALESCE(sum(paid), 0)) as balance from {$this->db->dbprefix('bils')} GROUP BY {$this->db->dbprefix('bils')}.customer_id ) FS";

            $this->load->library('datatables');
            $this->datatables
                ->select($this->db->dbprefix('companies') . ".id as id,'sno', company, name, phone, email, FS.total, FS.total_amount, FS.paid, FS.balance", FALSE)
                ->from("companies")
                ->join($s, 'FS.customer_id=companies.id')
                ->where('companies.group_name', 'customer')
                ->where('FS.payment_status', 'Completed')
                ->group_by('companies.id')
                ->add_column("Actions", "<div class='text-center'><a class=\"tip\" title='" . lang("view_report") . "' href='" . admin_url('reports/customer_report/$1') . "'><span class='label label-primary'>" . lang("view_report") . "</span></a></div>", "id")
                ->unset_column('id');
            echo $this->datatables->generate();

        }

    }

    function customer_report($user_id = NULL)
    {
        $this->sma->checkPermissions('customers', TRUE);
        if (!$user_id) {
            $this->session->set_flashdata('error', lang("no_customer_selected"));
            admin_redirect('reports/customers');
        }

        $this->data['sales'] = $this->reports_model->getSalesTotals($user_id);
        $this->data['total_sales'] = $this->reports_model->getCustomerSales($user_id);
        $this->data['total_quotes'] = $this->reports_model->getCustomerQuotes($user_id);
        $this->data['total_returns'] = $this->reports_model->getCustomerReturns($user_id);
        $this->data['users'] = $this->reports_model->getStaff();
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');

        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $this->data['user_id'] = $user_id;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('customers_report')));
        $meta = array('page_title' => lang('customers_report'), 'bc' => $bc);
        $this->page_construct('reports/customer_report', $meta, $this->data);

    }

    function suppliers()
    {
        $this->sma->checkPermissions('suppliers');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('suppliers_report')));
        $meta = array('page_title' => lang('suppliers_report'), 'bc' => $bc);
        $this->page_construct('reports/suppliers', $meta, $this->data);
    }

    function getSuppliers($pdf = NULL, $xls = NULL)
    {
        $this->sma->checkPermissions('suppliers', TRUE);

        if ($pdf || $xls) {

            $this->db
                ->select($this->db->dbprefix('companies') . ".id as id, company, name, phone, email, count({$this->db->dbprefix('purchases')}.id) as total, COALESCE(sum(grand_total), 0) as total_amount, COALESCE(sum(paid), 0) as paid, ( COALESCE(sum(grand_total), 0) - COALESCE(sum(paid), 0)) as balance", FALSE)
                ->from("companies")
                ->join('purchases', 'purchases.supplier_id=companies.id')
                ->where('companies.group_name', 'supplier')
                ->order_by('companies.company asc')
                ->group_by('companies.id');

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('suppliers_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('company'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('name'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('phone'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('email'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('total_purchases'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('total_amount'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('paid'));
                $this->excel->getActiveSheet()->SetCellValue('H1', lang('balance'));

                $row = 2;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->company);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->name);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->phone);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->email);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->total);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->total_amount);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->paid);
                    $this->excel->getActiveSheet()->SetCellValue('H' . $row, $data_row->balance);
                    $row++;
                }

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $filename = 'suppliers_report';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {

            $p = "( SELECT supplier_id, count(" . $this->db->dbprefix('purchases') . ".id) as total, COALESCE(sum(grand_total), 0) as total_amount, COALESCE(sum(paid), 0) as paid, ( COALESCE(sum(grand_total), 0) - COALESCE(sum(paid), 0)) as balance from {$this->db->dbprefix('purchases')} GROUP BY {$this->db->dbprefix('purchases')}.supplier_id ) FP";

            $this->load->library('datatables');
            $this->datatables
                ->select($this->db->dbprefix('companies') . ".id as id, 'sno',company, name, phone, email, FP.total, FP.total_amount, FP.paid, FP.balance", FALSE)
                ->from("companies")
                ->join($p, 'FP.supplier_id=companies.id')
                ->where('companies.group_name', 'supplier')
                ->group_by('companies.id')
                ->add_column("Actions", "<div class='text-center'><a class=\"tip\" title='" . lang("view_report") . "' href='" . admin_url('reports/supplier_report/$1') . "'><span class='label label-primary'>" . lang("view_report") . "</span></a></div>", "id")
                ->unset_column('id');
            echo $this->datatables->generate();

        }

    }

    function supplier_report($user_id = NULL)
    {

        $this->sma->checkPermissions('suppliers', TRUE);
        if (!$user_id) {
            $this->session->set_flashdata('error', lang("no_supplier_selected"));
            admin_redirect('reports/suppliers');
        }

        $this->data['purchases'] = $this->reports_model->getPurchasesTotals($user_id);
        
        $this->data['total_purchases'] = $this->reports_model->getSupplierPurchases($user_id);
        $this->data['users'] = $this->reports_model->getStaff();
        $this->data['warehouses'] = $this->site->getAllWarehouses();

        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $this->data['user_id'] = $user_id;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('suppliers_report')));
        $meta = array('page_title' => lang('suppliers_report'), 'bc' => $bc);
        $this->page_construct('reports/supplier_report', $meta, $this->data);

    }

    function users()
    {
        $this->sma->checkPermissions();
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('staff_report')));
        $meta = array('page_title' => lang('staff_report'), 'bc' => $bc);
        $this->page_construct('reports/users', $meta, $this->data);
    }

    function getUsers()
    {
        $this->sma->checkPermissions('users',TRUE);
        $this->load->library('datatables');
        $this->datatables
            ->select("'sno',".$this->db->dbprefix('warehouses').".name as branch,".$this->db->dbprefix('users').".id as id, first_name, last_name, ".$this->db->dbprefix('users').".email, company, ".$this->db->dbprefix('groups').".name, active")
            ->from("users")
            ->join('groups', 'users.group_id=groups.id', 'left')
            ->join('warehouses', 'users.warehouse_id=warehouses.id', 'left')
            ->group_by('users.id')
            ->where('company_id', NULL);
        if (!$this->Owner) {
            $this->datatables->where('group_id !=', 1);
        }
        $this->datatables
            ->edit_column('active', '$1__$2', 'active, id')
            ->add_column("Actions", "<div class='text-center'><a class=\"tip\" title='" . lang("view_report") . "' href='" . admin_url('reports/staff_report/$1') . "'><span class='label label-primary'>" . lang("view_report") . "</span></a></div>", "id")
            ->unset_column('id');
        echo $this->datatables->generate();
    }

    function staff_report($user_id = NULL, $year = NULL, $month = NULL, $pdf = NULL, $cal = 0)
    {

        if (!$user_id) {
            $this->session->set_flashdata('error', lang("no_user_selected"));
            admin_redirect('reports/users');
        }
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $this->data['purchases'] = $this->reports_model->getStaffPurchases($user_id);
        $this->data['sales'] = $this->reports_model->getStaffSales($user_id);        
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $this->data['warehouses'] = $this->site->getAllWarehouses();

        if (!$year) {
            $year = date('Y');
        }
        if (!$month || $month == '#monthly-con') {
            $month = date('m');
        }
        if ($pdf) {
            if ($cal) {
                $this->monthly_sales($year, $pdf, $user_id);
            } else {
                $this->daily_sales($year, $month, $pdf, $user_id);
            }
        }
        $config = array(
            'show_next_prev' => TRUE,
            'next_prev_url' => admin_url('reports/staff_report/'.$user_id),
            'month_type' => 'long',
            'day_type' => 'long'
        );

        $config['template'] = '{table_open}<div class="table-responsive"><table border="0" cellpadding="0" cellspacing="0" class="table table-bordered dfTable reports-table">{/table_open}
        {heading_row_start}<tr>{/heading_row_start}
        {heading_previous_cell}<th class="text-center"><a href="{previous_url}">&lt;&lt;</a></th>{/heading_previous_cell}
        {heading_title_cell}<th class="text-center" colspan="{colspan}" id="month_year">{heading}</th>{/heading_title_cell}
        {heading_next_cell}<th class="text-center"><a href="{next_url}">&gt;&gt;</a></th>{/heading_next_cell}
        {heading_row_end}</tr>{/heading_row_end}
        {week_row_start}<tr>{/week_row_start}
        {week_day_cell}<td class="cl_wday">{week_day}</td>{/week_day_cell}
        {week_row_end}</tr>{/week_row_end}
        {cal_row_start}<tr class="days">{/cal_row_start}
        {cal_cell_start}<td class="day">{/cal_cell_start}
        {cal_cell_content}
        <div class="day_num">{day}</div>
        <div class="content">{content}</div>
        {/cal_cell_content}
        {cal_cell_content_today}
        <div class="day_num highlight">{day}</div>
        <div class="content">{content}</div>
        {/cal_cell_content_today}
        {cal_cell_no_content}<div class="day_num">{day}</div>{/cal_cell_no_content}
        {cal_cell_no_content_today}<div class="day_num highlight">{day}</div>{/cal_cell_no_content_today}
        {cal_cell_blank}&nbsp;{/cal_cell_blank}
        {cal_cell_end}</td>{/cal_cell_end}
        {cal_row_end}</tr>{/cal_row_end}
        {table_close}</table></div>{/table_close}';

        $this->load->library('calendar', $config);
        $sales = $this->reports_model->getStaffDailySales($user_id, $year, $month);

        if (!empty($sales)) {
            foreach ($sales as $sale) {
                $daily_sale[$sale->date] = "<table class='table table-bordered table-hover table-striped table-condensed data' style='margin:0;'><tr><td>" . lang("total") . "</td><td>" . $this->sma->formatMoney($sale->total) . "</td></tr><tr><td>" . lang("discount") . "</td><td>" . $this->sma->formatMoney($sale->discount) . "</td></tr><tr><td>" . lang("tax") . "</td><td>" . $this->sma->formatMoney($sale->tax) . "</td></tr><tr><td>" . lang("total") . "</td><td>" . $this->sma->formatMoney($sale->total-$sale->discount+$sale->tax) . "</td></tr></table>";
            }
        } else {
            $daily_sale = array();
        }
        $this->data['calender'] = $this->calendar->generate($year, $month, $daily_sale);
        if ($this->input->get('pdf')) {

        }
        $this->data['year'] = $year;
        $this->data['month'] = $month;
        $this->data['msales'] = $this->reports_model->getStaffMonthlySales($user_id, $year);
        $this->data['user_id'] = $user_id;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('staff_report')));
        $meta = array('page_title' => lang('staff_report'), 'bc' => $bc);
        $this->page_construct('reports/staff_report', $meta, $this->data);

    }

    function getUserLogins($id = NULL, $pdf = NULL, $xls = NULL)
    {
        if ($this->input->get('start_date')) {
            $login_start_date = $this->input->get('start_date');
        } else {
            $login_start_date = NULL;
        }
        if ($this->input->get('end_date')) {
            $login_end_date = $this->input->get('end_date');
        } else {
            $login_end_date = NULL;
        }
        if ($login_start_date) {
            $login_start_date = $this->sma->fld($login_start_date);
            $login_end_date = $login_end_date ? $this->sma->fld($login_end_date) : date('Y-m-d H:i:s');
        }
        if ($pdf || $xls) {

            $this->db
                ->select("login, ip_address, time")
                ->from("user_logins")
                ->where('user_id', $id)
                ->order_by('time desc');
            if ($login_start_date) {
                $this->db->where("time BETWEEN '{$login_start_date}' and '{$login_end_date}'", NULL, FALSE);
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('staff_login_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('email'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('ip_address'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('time'));

                $row = 2;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->login);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->ip_address);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $this->sma->hrld($data_row->time));
                    $row++;
                }

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(35);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('C2:C' . $row)->getAlignment()->setWrapText(true);
                $filename = 'staff_login_report';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {

            $this->load->library('datatables');
            $this->datatables
                ->select("login, ip_address, DATE_FORMAT(time, '%Y-%m-%d %T') as time")
                ->from("user_logins")
                ->where('user_id', $id);
            if ($login_start_date) {
                $this->datatables->where("time BETWEEN '{$login_start_date}' and '{$login_end_date}'", NULL, FALSE);
            }
            echo $this->datatables->generate();

        }

    }

    function getCustomerLogins($id = NULL)
    {
        if ($this->input->get('login_start_date')) {
            $login_start_date = $this->input->get('login_start_date');
        } else {
            $login_start_date = NULL;
        }
        if ($this->input->get('login_end_date')) {
            $login_end_date = $this->input->get('login_end_date');
        } else {
            $login_end_date = NULL;
        }
        if ($login_start_date) {
            $login_start_date = $this->sma->fld($login_start_date);
            $login_end_date = $login_end_date ? $this->sma->fld($login_end_date) : date('Y-m-d H:i:s');
        }
        $this->load->library('datatables');
        $this->datatables
            ->select("login, ip_address, time")
            ->from("user_logins")
            ->where('customer_id', $id);
        if ($login_start_date) {
            $this->datatables->where('time BETWEEN "' . $login_start_date . '" and "' . $login_end_date . '"');
        }
        echo $this->datatables->generate();
    }

    function profit_loss($start_date = NULL, $end_date = NULL)
    {
        $this->sma->checkPermissions('profit_loss');
        if (!$start_date) {
            $start = $this->db->escape(date('Y-m') . '-1');
            $start_date = date('Y-m') . '-1';
        } else {
            $start = $this->db->escape(urldecode($start_date));
        }
        if (!$end_date) {
            $end = $this->db->escape(date('Y-m-d H:i'));
            $end_date = date('Y-m-d H:i');
        } else {
            $end = $this->db->escape(urldecode($end_date));
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        $this->data['total_purchases'] = $this->reports_model->getTotalPurchases($start, $end);
        $this->data['total_sales'] = $this->reports_model->getTotalSales($start, $end);
        $this->data['total_expenses'] = $this->reports_model->getTotalExpenses($start, $end);
        $this->data['total_paid'] = $this->reports_model->getTotalPaidAmount($start, $end);
        $this->data['total_received'] = $this->reports_model->getTotalReceivedAmount($start, $end);
        $this->data['total_received_cash'] = $this->reports_model->getTotalReceivedCashAmount($start, $end);
        $this->data['total_received_cc'] = $this->reports_model->getTotalReceivedCCAmount($start, $end);
        $this->data['total_received_cheque'] = $this->reports_model->getTotalReceivedChequeAmount($start, $end);
        $this->data['total_received_ppp'] = $this->reports_model->getTotalReceivedPPPAmount($start, $end);
        $this->data['total_received_stripe'] = $this->reports_model->getTotalReceivedStripeAmount($start, $end);
        $this->data['total_returned'] = $this->reports_model->getTotalReturnedAmount($start, $end);
        $this->data['start'] = urldecode($start_date);
        $this->data['end'] = urldecode($end_date);

        $warehouses = $this->site->getAllWarehouses();
        foreach ($warehouses as $warehouse) {
            $total_purchases = $this->reports_model->getTotalPurchases($start, $end, $warehouse->id);
            $total_sales = $this->reports_model->getTotalSales($start, $end, $warehouse->id);
            $total_expenses = $this->reports_model->getTotalExpenses($start, $end, $warehouse->id);
            $warehouses_report[] = array(
                'warehouse' => $warehouse,
                'total_purchases' => $total_purchases,
                'total_sales' => $total_sales,
                'total_expenses' => $total_expenses,
                );
        }
        $this->data['warehouses_report'] = $warehouses_report;

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('profit_loss')));
        $meta = array('page_title' => lang('profit_loss'), 'bc' => $bc);
        $this->page_construct('reports/profit_loss', $meta, $this->data);
    }

    function profit_loss_pdf($start_date = NULL, $end_date = NULL)
    {
        $this->sma->checkPermissions('profit_loss');
        if (!$start_date) {
            $start = $this->db->escape(date('Y-m') . '-1');
            $start_date = date('Y-m') . '-1';
        } else {
            $start = $this->db->escape(urldecode($start_date));
        }
        if (!$end_date) {
            $end = $this->db->escape(date('Y-m-d H:i'));
            $end_date = date('Y-m-d H:i');
        } else {
            $end = $this->db->escape(urldecode($end_date));
        }

        $this->data['total_purchases'] = $this->reports_model->getTotalPurchases($start, $end);
        $this->data['total_sales'] = $this->reports_model->getTotalSales($start, $end);
        $this->data['total_expenses'] = $this->reports_model->getTotalExpenses($start, $end);
        $this->data['total_paid'] = $this->reports_model->getTotalPaidAmount($start, $end);
        $this->data['total_received'] = $this->reports_model->getTotalReceivedAmount($start, $end);
        $this->data['total_received_cash'] = $this->reports_model->getTotalReceivedCashAmount($start, $end);
        $this->data['total_received_cc'] = $this->reports_model->getTotalReceivedCCAmount($start, $end);
        $this->data['total_received_cheque'] = $this->reports_model->getTotalReceivedChequeAmount($start, $end);
        $this->data['total_received_ppp'] = $this->reports_model->getTotalReceivedPPPAmount($start, $end);
        $this->data['total_received_stripe'] = $this->reports_model->getTotalReceivedStripeAmount($start, $end);
        $this->data['total_returned'] = $this->reports_model->getTotalReturnedAmount($start, $end);
        $this->data['start'] = urldecode($start_date);
        $this->data['end'] = urldecode($end_date);

        $warehouses = $this->site->getAllWarehouses();
        foreach ($warehouses as $warehouse) {
            $total_purchases = $this->reports_model->getTotalPurchases($start, $end, $warehouse->id);
            $total_sales = $this->reports_model->getTotalSales($start, $end, $warehouse->id);
            $warehouses_report[] = array(
                'warehouse' => $warehouse,
                'total_purchases' => $total_purchases,
                'total_sales' => $total_sales,
                );
        }
        $this->data['warehouses_report'] = $warehouses_report;

        $html = $this->load->view($this->theme . 'reports/profit_loss_pdf', $this->data, true);
        $name = lang("profit_loss") . "-" . str_replace(array('-', ' ', ':'), '_', $this->data['start']) . "-" . str_replace(array('-', ' ', ':'), '_', $this->data['end']) . ".pdf";
        $this->sma->generate_pdf($html, $name, false, false, false, false, false, 'L');
    }

    function register()
    {
        $this->sma->checkPermissions('register');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->reports_model->getStaff();
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('register_report')));
        $meta = array('page_title' => lang('register_report'), 'bc' => $bc);
        $this->page_construct('reports/register', $meta, $this->data);
    }

    function getRrgisterlogs($pdf = NULL, $xls = NULL)
    {
        $this->sma->checkPermissions('register', TRUE);
        if ($this->input->get('user')) {
            $user = $this->input->get('user');
        } else {
            $user = NULL;
        }
        if ($this->input->get('start_date')) {
            $start_date = $this->input->get('start_date');
        } else {
            $start_date = NULL;
        }
        if ($this->input->get('end_date')) {
            $end_date = $this->input->get('end_date');
        } else {
            $end_date = NULL;
        }
        if ($start_date) {
            $start_date = $this->sma->fld($start_date);
            $end_date = $this->sma->fld($end_date);
        }

        if ($pdf || $xls) {

            $this->db
                ->select("date, closed_at, CONCAT(" . $this->db->dbprefix('users') . ".first_name, ' ', " . $this->db->dbprefix('users') . ".last_name, ' (', users.email, ')') as user, cash_in_hand, total_cc_slips, total_cheques, total_cash, total_cc_slips_submitted, total_cheques_submitted,total_cash_submitted, note", FALSE)
                ->from("pos_register")
                ->join('users', 'users.id=pos_register.user_id', 'left')
                ->order_by('date desc');
            //->where('status', 'close');

            if ($user) {
                $this->db->where('pos_register.user_id', $user);
            }
            if ($start_date) {
                $this->db->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('register_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('open_time'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('close_time'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('user'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('cash_in_hand'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('cc_slips'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('cheques'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('total_cash'));
                $this->excel->getActiveSheet()->SetCellValue('H1', lang('cc_slips_submitted'));
                $this->excel->getActiveSheet()->SetCellValue('I1', lang('cheques_submitted'));
                $this->excel->getActiveSheet()->SetCellValue('J1', lang('total_cash_submitted'));
                $this->excel->getActiveSheet()->SetCellValue('K1', lang('note'));

                $row = 2;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($data_row->date));
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->closed_at);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->user);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->cash_in_hand);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->total_cc_slips);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->total_cheques);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->total_cash);
                    $this->excel->getActiveSheet()->SetCellValue('H' . $row, $data_row->total_cc_slips_submitted);
                    $this->excel->getActiveSheet()->SetCellValue('I' . $row, $data_row->total_cheques_submitted);
                    $this->excel->getActiveSheet()->SetCellValue('J' . $row, $data_row->total_cash_submitted);
                    $this->excel->getActiveSheet()->SetCellValue('K' . $row, $data_row->note);
                    if($data_row->total_cash_submitted < $data_row->total_cash || $data_row->total_cheques_submitted < $data_row->total_cheques || $data_row->total_cc_slips_submitted < $data_row->total_cc_slips) {
                        $this->excel->getActiveSheet()->getStyle('A'.$row.':K'.$row)->applyFromArray(
                                array( 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'F2DEDE')) )
                                );
                    }
                    $row++;
                }

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(35);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $filename = 'register_report';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {

            $this->load->library('datatables');
            $this->datatables
                ->select("date, closed_at, CONCAT(" . $this->db->dbprefix('users') . ".first_name, ' ', " . $this->db->dbprefix('users') . ".last_name, '<br>', " . $this->db->dbprefix('users') . ".email) as user, cash_in_hand, CONCAT(total_cc_slips, ' (', total_cc_slips_submitted, ')'), CONCAT(total_cheques, ' (', total_cheques_submitted, ')'), CONCAT(total_cash, ' (', total_cash_submitted, ')'), note", FALSE)
                ->from("pos_register")
                ->join('users', 'users.id=pos_register.user_id', 'left');

            if ($user) {
                $this->datatables->where('pos_register.user_id', $user);
            }
            if ($start_date) {
                $this->datatables->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            echo $this->datatables->generate();

        }

    }

    public function expenses($id = null)
    { 
        $this->sma->checkPermissions();
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->reports_model->getStaff();
        $this->data['categories'] = $this->reports_model->getExpenseCategories();
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('expenses')));
        $meta = array('page_title' => lang('expenses'), 'bc' => $bc);
        $this->page_construct('reports/expenses', $meta, $this->data);
    }

    public function getExpensesReport($pdf = NULL, $xls = NULL)
    {
        $this->sma->checkPermissions('expenses',true);

        $reference_no = $this->input->get('reference_no') ? $this->input->get('reference_no') : NULL;
        $category = $this->input->get('category') ? $this->input->get('category') : NULL;
        $note = $this->input->get('note') ? $this->input->get('note') : NULL;
        $user = $this->input->get('user') ? $this->input->get('user') : NULL;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
        $end_date = $this->input->get('end_date') ? $this->input->get('end_date') : NULL;

        if ($start_date) {
            $start_date = $this->sma->fld($start_date);
            $end_date = $this->sma->fld($end_date);
        }

        if ($pdf || $xls) {

            $this->db
                ->select("date, reference, {$this->db->dbprefix('expense_categories')}.name as category, amount, note, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as user, attachment, {$this->db->dbprefix('expenses')}.id as id", false)
            ->from('expenses')
            ->join('users', 'users.id=expenses.created_by', 'left')
            ->join('expense_categories', 'expense_categories.id=expenses.category_id', 'left')
            ->group_by('expenses.id');

            if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
                $this->db->where('created_by', $this->session->userdata('user_id'));
            }

            if ($note) {
                $this->db->like('note', $note, 'both');
            }
            if ($reference_no) {
                $this->db->like('reference', $reference_no, 'both');
            }
            if ($category) {
                $this->db->where('category_id', $category);
            }
            if ($user) {
                $this->db->where('created_by', $user);
            }
            if ($start_date) {
                $this->db->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('expenses_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference_no'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('category'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('amount'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('note'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('created_by'));

                $row = 2; $total = 0;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($data_row->date));
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->reference);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->category);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->amount);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->note);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->created_by);
                    $total += $data_row->amount;
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle("D" . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('D' . $row, $total);

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $filename = 'expenses_report';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {

            $this->load->library('datatables');
            $this->datatables
            ->select("DATE_FORMAT(date, '%Y-%m-%d %T') as date, reference, {$this->db->dbprefix('expense_categories')}.name as category, amount, note, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as user, attachment, {$this->db->dbprefix('expenses')}.id as id", false)
            ->from('expenses')
            ->join('users', 'users.id=expenses.created_by', 'left')
            ->join('expense_categories', 'expense_categories.id=expenses.category_id', 'left')
            ->group_by('expenses.id');

            if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
                $this->datatables->where('created_by', $this->session->userdata('user_id'));
            }

            if ($note) {
                $this->datatables->like('note', $note, 'both');
            }
            if ($reference_no) {
                $this->datatables->like('reference', $reference_no, 'both');
            }
            if ($category) {
                $this->datatables->where('category_id', $category);
            }
            if ($user) {
                $this->datatables->where('created_by', $user);
            }
            if ($start_date) {
                $this->datatables->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            echo $this->datatables->generate();
        }
    }

    function daily_purchases($warehouse_id = NULL, $year = NULL, $month = NULL, $pdf = NULL, $user_id = NULL)
    {
        $this->sma->checkPermissions();
        if (!$this->Owner && !$this->Admin && $this->session->userdata('warehouse_id')) {
            $warehouse_id = $this->session->userdata('warehouse_id');
        }
        if (!$year) {
            $year = date('Y');
        }
        if (!$month) {
            $month = date('m');
        }
        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $config = array(
            'show_next_prev' => TRUE,
            'next_prev_url' => admin_url('reports/daily_purchases/'.($warehouse_id ? $warehouse_id : 0)),
            'month_type' => 'long',
            'day_type' => 'long'
        );

        $config['template'] = '{table_open}<div class="table-responsive"><table border="0" cellpadding="0" cellspacing="0" class="table table-bordered dfTable">{/table_open}
        {heading_row_start}<tr>{/heading_row_start}
        {heading_previous_cell}<th><a href="{previous_url}">&lt;&lt;</a></th>{/heading_previous_cell}
        {heading_title_cell}<th colspan="{colspan}" id="month_year">{heading}</th>{/heading_title_cell}
        {heading_next_cell}<th><a href="{next_url}">&gt;&gt;</a></th>{/heading_next_cell}
        {heading_row_end}</tr>{/heading_row_end}
        {week_row_start}<tr>{/week_row_start}
        {week_day_cell}<td class="cl_wday">{week_day}</td>{/week_day_cell}
        {week_row_end}</tr>{/week_row_end}
        {cal_row_start}<tr class="days">{/cal_row_start}
        {cal_cell_start}<td class="day">{/cal_cell_start}
        {cal_cell_content}
        <div class="day_num">{day}</div>
        <div class="content">{content}</div>
        {/cal_cell_content}
        {cal_cell_content_today}
        <div class="day_num highlight">{day}</div>
        <div class="content">{content}</div>
        {/cal_cell_content_today}
        {cal_cell_no_content}<div class="day_num">{day}</div>{/cal_cell_no_content}
        {cal_cell_no_content_today}<div class="day_num highlight">{day}</div>{/cal_cell_no_content_today}
        {cal_cell_blank}&nbsp;{/cal_cell_blank}
        {cal_cell_end}</td>{/cal_cell_end}
        {cal_row_end}</tr>{/cal_row_end}
        {table_close}</table></div>{/table_close}';

        $this->load->library('calendar', $config);
        $purchases = $user_id ? $this->reports_model->getStaffDailyPurchases($user_id, $year, $month, $warehouse_id) : $this->reports_model->getDailyPurchases($year, $month, $warehouse_id);

        if (!empty($purchases)) {
            foreach ($purchases as $purchase) {
                $daily_purchase[$purchase->date] = "<table class='table table-bordered table-hover table-striped table-condensed data' style='margin:0;'><tr><td>" . lang("discount") . "</td><td>" . $this->sma->formatMoney($purchase->discount) . "</td></tr><tr><td>" . lang("shipping") . "</td><td>" . $this->sma->formatMoney($purchase->shipping) . "</td></tr><tr><td>" . lang("product_tax") . "</td><td>" . $this->sma->formatMoney($purchase->tax1) . "</td></tr><tr><td>" . lang("order_tax") . "</td><td>" . $this->sma->formatMoney($purchase->tax2) . "</td></tr><tr><td>" . lang("total") . "</td><td>" . $this->sma->formatMoney($purchase->total) . "</td></tr></table>";
            }
        } else {
            $daily_purchase = array();
        }

        $this->data['calender'] = $this->calendar->generate($year, $month, $daily_purchase);
        $this->data['year'] = $year;
        $this->data['month'] = $month;
        if ($pdf) {
            $html = $this->load->view($this->theme . 'reports/daily', $this->data, true);
            $name = lang("daily_purchases") . "_" . $year . "_" . $month . ".pdf";
            $html = str_replace('<p class="introtext">' . lang("reports_calendar_text") . '</p>', '', $html);
            $this->sma->generate_pdf($html, $name, null, null, null, null, null, 'L');
        }
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['warehouse_id'] = $warehouse_id;
        $this->data['sel_warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : NULL;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('daily_purchases_report')));
        $meta = array('page_title' => lang('daily_purchases_report'), 'bc' => $bc);
        $this->page_construct('reports/daily_purchases', $meta, $this->data);

    }


    function monthly_purchases($warehouse_id = NULL, $year = NULL, $pdf = NULL, $user_id = NULL)
    {
        $this->sma->checkPermissions();
        if (!$this->Owner && !$this->Admin && $this->session->userdata('warehouse_id')) {
            $warehouse_id = $this->session->userdata('warehouse_id');
        }
        if (!$year) {
            $year = date('Y');
        }
        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->load->language('calendar');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['year'] = $year;
        $this->data['purchases'] = $user_id ? $this->reports_model->getStaffMonthlyPurchases($user_id, $year, $warehouse_id) : $this->reports_model->getMonthlyPurchases($year, $warehouse_id);
        if ($pdf) {
            $html = $this->load->view($this->theme . 'reports/monthly', $this->data, true);
            $name = lang("monthly_purchases") . "_" . $year . ".pdf";
            $html = str_replace('<p class="introtext">' . lang("reports_calendar_text") . '</p>', '', $html);
            $this->sma->generate_pdf($html, $name, null, null, null, null, null, 'L');
        }
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['warehouse_id'] = $warehouse_id;
        $this->data['sel_warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : NULL;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('monthly_purchases_report')));
        $meta = array('page_title' => lang('monthly_purchases_report'), 'bc' => $bc);
        $this->page_construct('reports/monthly_purchases', $meta, $this->data);

    }

    function adjustments($warehouse_id = NULL)
    {
        $this->sma->checkPermissions();

        $this->data['users'] = $this->reports_model->getStaff();
        $this->data['warehouses'] = $this->site->getAllWarehouses();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('adjustments_report')));
        $meta = array('page_title' => lang('adjustments_report'), 'bc' => $bc);
        $this->page_construct('reports/adjustments', $meta, $this->data);
    }

    function getAdjustmentReport($pdf = NULL, $xls = NULL)
    {
        $this->sma->checkPermissions('adjustments', TRUE);

        $product = $this->input->get('product') ? $this->input->get('product') : NULL;
        $warehouse = $this->input->get('warehouse') ? $this->input->get('warehouse') : NULL;
        $user = $this->input->get('user') ? $this->input->get('user') : NULL;
        $reference_no = $this->input->get('reference_no') ? $this->input->get('reference_no') : NULL;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
        $end_date = $this->input->get('end_date') ? $this->input->get('end_date') : NULL;
        $serial = $this->input->get('serial') ? $this->input->get('serial') : NULL;

        if ($start_date) {
            $start_date = $this->sma->fld($start_date);
            $end_date = $this->sma->fld($end_date);
        }
        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user = $this->session->userdata('user_id');
        }

        if ($pdf || $xls) {

            $ai = "( SELECT adjustment_id, product_id, serial_no, GROUP_CONCAT(CONCAT({$this->db->dbprefix('products')}.name, ' (', (CASE WHEN {$this->db->dbprefix('adjustment_items')}.type  = 'subtraction' THEN (0-{$this->db->dbprefix('adjustment_items')}.quantity) ELSE {$this->db->dbprefix('adjustment_items')}.quantity END), ')') SEPARATOR '\n') as item_nane from {$this->db->dbprefix('adjustment_items')} LEFT JOIN {$this->db->dbprefix('products')} ON {$this->db->dbprefix('products')}.id={$this->db->dbprefix('adjustment_items')}.product_id GROUP BY {$this->db->dbprefix('adjustment_items')}.adjustment_id ) FAI";

            $this->db->select("DATE_FORMAT(date, '%Y-%m-%d %T') as date, reference_no, warehouses.name as wh_name, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as created_by, note, FAI.item_nane as iname, {$this->db->dbprefix('adjustments')}.id as id", FALSE)
            ->from('adjustments')
            ->join($ai, 'FAI.adjustment_id=adjustments.id', 'left')
            ->join('users', 'users.id=adjustments.created_by', 'left')
            ->join('warehouses', 'warehouses.id=adjustments.warehouse_id', 'left');

            if ($user) {
                $this->db->where('adjustments.created_by', $user);
            }
            if ($product) {
                $this->db->where('FAI.product_id', $product);
            }
            if ($serial) {
                $this->db->like('FAI.serial_no', $serial);
            }
            if ($warehouse) {
                $this->db->where('adjustments.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->db->like('adjustments.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->db->where($this->db->dbprefix('adjustments').'.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('adjustments_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference_no'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('warehouse'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('created_by'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('note'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('products'));

                $row = 2;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($data_row->date));
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->reference_no);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->wh_name);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->created_by);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $this->sma->decode_html($data_row->note));
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->iname);
                    $row++;
                }

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('E2:E' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->getStyle('F2:F' . $row)->getAlignment()->setWrapText(true);
                $filename = 'adjustments_report';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {

            $ai = "( SELECT adjustment_id, product_id, serial_no, GROUP_CONCAT(CONCAT({$this->db->dbprefix('products')}.name, '__', (CASE WHEN {$this->db->dbprefix('adjustment_items')}.type  = 'subtraction' THEN (0-{$this->db->dbprefix('adjustment_items')}.quantity) ELSE {$this->db->dbprefix('adjustment_items')}.quantity END)) SEPARATOR '___') as item_nane from {$this->db->dbprefix('adjustment_items')} LEFT JOIN {$this->db->dbprefix('products')} ON {$this->db->dbprefix('products')}.id={$this->db->dbprefix('adjustment_items')}.product_id ";
            if ($product || $serial) { $ai .= " WHERE "; }
            if ($product) {
                $ai .= " {$this->db->dbprefix('adjustment_items')}.product_id = {$product} ";
            }
            if ($product && $serial) { $ai .= " AND "; }
            if ($serial) {
                $ai .= " {$this->db->dbprefix('adjustment_items')}.serial_no LIKe '%{$serial}%' ";
            }
            $ai .= " GROUP BY {$this->db->dbprefix('adjustment_items')}.adjustment_id ) FAI";
            $this->load->library('datatables');
            $this->datatables
            ->select("DATE_FORMAT(date, '%Y-%m-%d %T') as date, reference_no, warehouses.name as wh_name, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as created_by, note, FAI.item_nane as iname, {$this->db->dbprefix('adjustments')}.id as id", FALSE)
            ->from('adjustments')
            ->join($ai, 'FAI.adjustment_id=adjustments.id', 'left')
            ->join('users', 'users.id=adjustments.created_by', 'left')
            ->join('warehouses', 'warehouses.id=adjustments.warehouse_id', 'left');

            if ($user) {
                $this->datatables->where('adjustments.created_by', $user);
            }
            if ($product) {
                $this->datatables->where('FAI.product_id', $product);
            }
            if ($serial) {
                $this->datatables->like('FAI.serial_no', $serial);
            }
            if ($warehouse) {
                $this->datatables->where('adjustments.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->datatables->like('adjustments.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->datatables->where($this->db->dbprefix('adjustments').'.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            echo $this->datatables->generate();
        }

    }

    function get_deposits($company_id = NULL)
    {
        $this->sma->checkPermissions('customers', TRUE);
        $this->load->library('datatables');
        $this->datatables
            ->select("'sno',date, credit_amount, paid_by, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as created_by, note", false)
            ->from("deposits")
            ->join('users', 'users.id=deposits.created_by', 'left')
            ->where($this->db->dbprefix('deposits').'.company_id', $company_id);
            
        echo $this->datatables->generate();
    }

    function recipesearch()
    {
        $term = $this->input->get('term', TRUE);
        if (strlen($term) < 1) {
            die();
        }

        $rows = $this->reports_model->getRecipeNames($term);
        if ($rows) {
            foreach ($rows as $row) {
                $pr[] = array('id' => $row->id, 'label' => $row->name . " (" . $row->code . ")");

            }
            $this->sma->send_json($pr);
        } else {
            echo FALSE;
        }
    }
     function hourly_wise()
    {
        $this->sma->checkPermissions();
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->reports_model->getStaff();        
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('hourly_wise')));
        $meta = array('page_title' => lang('hourly_wise'), 'bc' => $bc);
        
        $this->page_construct('reports/hourly_wise', $meta, $this->data);
    }
  public function get_HourlySummaryreports($start_date = NULL, $end_date = NULL, $warehouse_id = NULL, $time_range = NULL){
        $this->sma->checkPermissions('hourly_wise',TRUE);
        $start = $this->input->post('start_date');
        $end = $this->input->post('end_date');
        $limit = $this->input->post('pagelimit');        
        $warehouse_id = $this->input->post('warehouse_id');
        $offsetSegment = 4;
        $offset = $this->uri->segment($offsetSegment,0);
        $time_range = $this->input->post('time_range');

        $this->session->set_userdata('start_date', $this->input->post('start_date'));
        $this->session->set_userdata('end_date', $this->input->post('end_date'));

        $data= '';
        if ($start != '' && $end != '') {
            $data = $this->reports_model->getHourlysummaryReport($start,$end,$warehouse_id,$time_range,$limit,$offset,$this->report_view_access,$this->report_show);
           
             if (!empty($data['data'])) {                 
                 $hourlysummary = $data['data'];
             }
             else{                
                $hourlysummary = 'empty';
             }
            
        }
        else{
            $hourlysummary = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('reports/get_HourlySummaryreports',$limit,$offsetSegment,$total);
        //echo $daysummary;
        $this->sma->send_json(array('hourlysummary' => $hourlysummary,'pagination'=>$pagination));
   } 


  function feedback_details()
    {
        /*$this->sma->checkPermissions('feedback_details');*/
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('feedback_details')), array('link' => '#', 'page' => lang('feedback_details')));
        $meta = array('page_title' => lang('feedback_details'), 'bc' => $bc);
        $this->page_construct('reports/feedback_details', $meta, $this->data);
    }


  function get_feedback_details($warehouse_id = NULL)
    {

        /*$this->sma->checkPermissions('feedback_details', TRUE);*/
        $date = date('Y-m-d', strtotime('+3 months'));
        if (!$this->Owner && !$warehouse_id) {
            $user = $this->site->getUser();
            $warehouse_id = $user->warehouse_id;
        }
        $this->load->library('datatables');
        if ($warehouse_id) {
            $this->datatables
                ->select("'sno',".$this->db->dbprefix('recipe') . ".name as recipe_name,".$this->db->dbprefix('restaurant_tables') . ".name as table_name, ".$this->db->dbprefix('feedback') . ".status as status,".$this->db->dbprefix('companies') . ".customer_group_name as customer_group_name,message,".$this->db->dbprefix('warehouses') . ".name as warehouses", FALSE)
                ->from("feedback")
                ->join('companies', 'companies.id=feedback.customer_id')
                ->join('restaurant_tables', 'restaurant_tables.id=feedback.table_id')
                ->join('recipe', 'recipe.id = feedback.item_id')
                ->join('warehouses', 'warehouses.id=feedback.warehouse_id');
                
        } else {
            $this->datatables
                ->select("'sno',".$this->db->dbprefix('recipe') . ".name as recipe_name,".$this->db->dbprefix('restaurant_tables') . ".name as table_name, ".$this->db->dbprefix('feedback') . ".status as status,".$this->db->dbprefix('companies') . ".customer_group_name as customer_group_name,message,".$this->db->dbprefix('warehouses') . ".name as warehouses", FALSE)
                ->from("feedback")
                ->join('companies', 'companies.id=feedback.customer_id')
                ->join('restaurant_tables', 'restaurant_tables.id=feedback.table_id')
                ->join('recipe', 'recipe.id = feedback.item_id')
                ->join('warehouses', 'warehouses.id=feedback.warehouse_id');
                
        }
        echo $this->datatables->generate();
    }
    
    function feedback()
    {
        /*$this->sma->checkPermissions('feedback_details');*/
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('feedback_details')), array('link' => '#', 'page' => lang('feedback_details')));
        $meta = array('page_title' => lang('feedback_details'), 'bc' => $bc);
        $this->data['warehouses'] = $this->site->getAllWarehouses();    
        $this->page_construct('reports/feedback', $meta, $this->data);
    }



   public function get_feedback(){
        $this->sma->checkPermissions('recipe',TRUE);
        $start = $this->input->post('start');
        $end = $this->input->post('end');
        $warehouse_id = $this->input->post('warehouse_id');
        $limit = $this->input->post('pagelimit');    
        $offsetSegment = 4;
        $offset = $this->uri->segment($offsetSegment,0);
        $data= '';
        $total =0;
        if ($start != '' && $end != '') {
            $data = $this->reports_model->getFeedBackReports($start,$end,$warehouse_id,$limit,$offset);
            $total = @$data['total'];
             if ($data != false) {             
                 $data = $data['data'];
             }
             else{                
                $data = 'empty';
             }            
        }
        else{
            $data = 'error';
        }
        //$total = $data['total'];
        $pagination = $this->pagination('reports/get_feedback',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('feedback' => $data,'pagination'=>$pagination));

        // $this->sma->send_json(array('feedback' => $data));
   }
    public function feedback_view($split_id = null)
    {
        
        // $split_id = $this->input->get('split_id');
        // var_dump($split_id);die;
        // $this->sma->checkPermissions('index', true);

      /*  if ($this->input->get('split_id')) {
            $split_id = $this->input->get('split_id');
        }*/
        // $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        // $inv = $this->purchases_model->getPurchaseByID($purchase_id);
    /*    if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($inv->created_by, true);
        }*/
        $this->data['item_feedback'] = $this->reports_model->getFeedBackItems($split_id);
        $this->data['company_feedback'] = $this->reports_model->getFeedBackAboutCompany($split_id);
        $this->data['supplier'] = $this->reports_model->getCustomerInfo($split_id);
        $this->data['overallcomment'] = $this->reports_model->geOverallComment($split_id);
        /*print_r($this->data['overallcomment']);die;*/
        /*echo "<pre>";
        print_r($this->data['rows']);die;*/
        /*$this->data['supplier'] = $this->site->getCompanyByID($inv->supplier_id);
        $this->data['warehouse'] = $this->site->getWarehouseByID($inv->warehouse_id);
        $this->data['inv'] = $inv;
        $this->data['payments'] = $this->purchases_model->getPaymentsForPurchase($purchase_id);
        $this->data['created_by'] = $this->site->getUser($inv->created_by);
        $this->data['updated_by'] = $inv->updated_by ? $this->site->getUser($inv->updated_by) : null;
        $this->data['return_purchase'] = $inv->return_id ? $this->purchases_model->getPurchaseByID($inv->return_id) : NULL;
        $this->data['return_rows'] = $inv->return_id ? $this->purchases_model->getAllPurchaseItems($inv->return_id) : NULL;*/

        $this->load->view($this->theme . 'reports/feedback_view', $this->data);
// sales/payment_note/
    }   
    function postpaid_bills()
    {
        $this->sma->checkPermissions();
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->reports_model->getStaff();        
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('postpaid_bills')));
        $meta = array('page_title' => lang('postpaid_bills'), 'bc' => $bc);
        
        $this->page_construct('reports/postpaid_bills', $meta, $this->data);
    }
  public function postpaid_bills_report($start_date = NULL, $end_date = NULL, $warehouse_id = NULL){
        $this->sma->checkPermissions('postpaid_bills',TRUE);
        $start = $this->input->post('start_date');
        $end = $this->input->post('end_date');
        $limit = $this->input->post('pagelimit');        
        $warehouse_id = $this->input->post('warehouse_id');
        $dayrange = $this->input->post('day_range');
        $customer_id = $this->input->post('customer_id');
        $offsetSegment = 4;
        $offset = $this->uri->segment($offsetSegment,0);
        $data= '';
        //if ($start != '' && $end != '') {
            $data = $this->reports_model->postpaid_bills_report($warehouse_id,$limit,$offset,$customer_id);
           
             if (!empty($data['data'])) {                 
                 $postpaid_bills = $data['data'];
             }
             else{                
                $postpaid_bills = 'empty';
             }
            
        //}
        //else{
        //    $postpaid_bills = 'error';
        //}
        $total = $data['total'];
        $customer_details = $data['customer_details'];
        $pagination = $this->pagination('reports/postpaid_bills_report',$limit,$offsetSegment,$total);
        //echo $daysummary;
        $this->sma->send_json(array('postpaid_bills' => $postpaid_bills,'customer_details'=>$customer_details,'pagination'=>$pagination));
   }
   function customer_postpaid_bills($id){
    
        $this->sma->checkPermissions();
       
        $this->data['customer'] = $this->reports_model->getCustomerDetails($id);
        $this->data['customer_id'] = $id;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => 'reports/postpaid_bills', 'page' => lang('postpaid_bills')),array('link' => '#', 'page' => @$this->data['customer']->name.' '.lang('bills')));
       $meta = array('page_title' => @$this->data['customer']->name.' '.lang('bills'), 'bc' => $bc);
        
        $this->page_construct('reports/customer_postpaid_bills', $meta, $this->data);
   }
   function getCustomerPostpaid_bills(){
    $this->sma->checkPermissions('postpaid_bills',TRUE);
        $start = $this->input->post('start_date');
        $end = $this->input->post('end_date');
        $limit = $this->input->post('pagelimit');        
        $warehouse_id = $this->input->post('warehouse_id');
        $customer_id = $this->input->post('customer_id');
        $offsetSegment = 4;
        $offset = $this->uri->segment($offsetSegment,0);
        $data= '';
        //if ($start != '' && $end != '') {
            $data = $this->reports_model->customer_postpaid_bills($customer_id,$limit,$offset);
            $totalAmount = $this->reports_model->GetPostpaid_BillDetails($customer_id);
             if (!empty($data['data'])) {                 
                 $postpaid_bills = $data['data'];
             }
             else{                
                $postpaid_bills = 'empty';
             }
            
        //}
        //else{
        //    $postpaid_bills = 'error';
        //}
        $total = $data['total'];
        $pagination = $this->pagination('reports/getCustomerPostpaid_bills',$limit,$offsetSegment,$total);
        //echo $daysummary;
        $this->sma->send_json(array('postpaid_bills' => $postpaid_bills,'total_amount'=>$totalAmount,'pagination'=>$pagination));
   }
   function postpaid_payment($id,$billId=false){
     $this->form_validation->set_rules('amount-paid', lang("amount"), 'required');
        $this->form_validation->set_rules('paid_by', lang("paid_by"), 'required');
        if ($this->form_validation->run() == true) {
           
                $date = date('Y-m-d H:i:s');
            
            $payment = array(
                'date' => $date,
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
                'company_id' => $id,
            );
        }elseif ($this->input->post('add_payment')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }

        if ($this->form_validation->run() == true && $this->reports_model->postpaid_Addpayment($payment,$billId)) {
            $this->session->set_flashdata('message', lang("payment_added"));
            redirect($_SERVER["HTTP_REFERER"]);
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['bills'] = $this->reports_model->GetPostpaid_BillDetails($id,$billId);
            $this->data['bill_id'] = $billId;
            $this->load->view($this->theme . 'reports/postpaid_payment', $this->data);
        }
   }

    function open_close_register()
    {
        $this->sma->checkPermissions();
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('open_close_register_reports')));
        $meta = array('page_title' => lang('open_close_register_reports'), 'bc' => $bc);
        $this->page_construct('reports/open_close_register', $meta, $this->data);
    }    
    public function get_open_close_reports($start = NULL, $end = NULL, $open_close = NULL){
        /*$this->sma->checkPermissions('popular_analysis',TRUE);*/
        $start = $this->input->post('start_date');
        $end = $this->input->post('end_date');
        $open_close = $this->input->post('open_close');
        /*$warehouse_id = $this->input->post('warehouse_id');*/
        $limit = $this->input->post('pagelimit');        
        $offsetSegment = 4;
        $offset = $this->uri->segment($offsetSegment,0);
        $data= '';
        if ($start != '' && $end != '') {
           if($open_close == 'open') 
             {                
                $data = $this->reports_model->getopenregisterReport($start,$end,$limit,$offset);
             }
             else
             {
              $data = $this->reports_model->getcloseregisterReport($start,$end,$limit,$offset);
             }        
            if (!empty($data['data'])){
                 $open_close = $data['data'];
             }
             else{
                $open_close = 'empty';
             }
        }
        else{
            $open_close = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('reports/get_open_close_reports',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('open_close_data' => $open_close,'pagination'=>$pagination));
   }   

   function post_feedback($splitid){
    $this->load->library('Emoji');
    $this->load->library('FB');
    $this->upload_path = base_url().'/assets/uploads/testimonial/';
    $testimonial = $this->reports_model->getTestimonialData($splitid);
    $picture = $this->upload_path.$testimonial->photo;
    $data['description']  = "Feedback By <strong>".$testimonial->customer_name."</strong> :<br>".$this->emoji->decode($testimonial->comment);
    //$image = file_get_contents($picture);
    /*$picture ='data:image/jpg;base64,'.base64_encode($image);*/
    $data['picture']  = $picture;//'D:/xampp/htdocs/suki/assets/uploads/testimonial/'.$testimonial->photo;//'http://bm17.co.za/sukki/assets/uploads/f2f5445b68953fbb7849440d6a28d4d7.jpg';
    $response = $this->fb->post($data);
    $decode_response = json_decode($response);
    if($decode_response->status=="success"){
        $d  = $decode_response->data;
        $this->reports_model->updateFBPostid($testimonial->id,$d->post_id);
    }
    echo $response;//ALTER TABLE `srampos_testimonial` ADD `fb_postid` VARCHAR(200) NOT NULL AFTER `split_id`;
   }
   function pagination($url,$per,$segment,$total){
        $config['base_url'] = admin_url($url);
        $config['per_page'] = $per;
        $config['uri_segment'] = $segment;
        $config['total_rows'] = $total;
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['prev_link'] = 'Previous';
        $config['next_link'] = 'Next';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
       //$config['num_links'] = 3;
        $config['first_link']  = FALSE;
        $config['last_link']   = FALSE;
        $limit = $config['per_page'];
        $offset = $this->uri->segment($config['uri_segment'],0);
        $offset = ($offset>1)?(($offset-1) * $limit):0;
        
        $this->pagination->initialize($config);
        return $this->pagination->create_links();
   }

    function bbq_reports()
    {
        $this->sma->checkPermissions();
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->reports_model->getStaff();        
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('bbq_reports')));
        $meta = array('page_title' => lang('bbq_reports'), 'bc' => $bc);
        
        $this->page_construct('reports/bbq_reports', $meta, $this->data);
    }

   public function get_bbqrports(){

        // $this->sma->checkPermissions('bbq',TRUE);        
        $start = $this->input->post('start_date');
        $end = $this->input->post('end_date');
        $warehouse_id = $this->input->post('warehouse_id');
        $summary_items = $this->input->post('summary_items');
        $limit = $this->input->post('pagelimit');    
        $offsetSegment = 4;
        $offset = $this->uri->segment($offsetSegment,0);

        $this->session->set_userdata('start_date', $this->input->post('start_date'));
        $this->session->set_userdata('end_date', $this->input->post('end_date'));

        $data= '';
        if ($start != '' && $end != '') {
            if($summary_items == 'bbq_summary'){
                 $data = $this->reports_model->getBBQDetailsReport($start,$end,$warehouse_id,$summary_items,$limit,$offset);

            }elseif($summary_items == 'bbq_bills'){
                $data = $this->reports_model->getBBQBillDetailsReport($start,$end,$warehouse_id,$summary_items,$limit,$offset);                
            }else{
                $data = $this->reports_model->getBBQitemsDetailsReport($start,$end,$warehouse_id,$summary_items,$limit,$offset);
            }
            $round_tot = $this->reports_model->getRoundamount($start,$end,$warehouse_id);            
             if (!empty($data['data'])) {                 
                 $bbqrports = $data['data'];
             }
             else{                
                $bbqrports = 'empty';
             }
             if ($round_tot != false) {                 
                 $round = $round_tot;
             }
             else{                
                $round = 'empty';
             }
        }
        else{
            $bbqrports = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('reports/get_bbqrports',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('bbqrports' => $bbqrports,'round' => $round,'pagination'=>$pagination));
   }
   
   function bbq_notification_reports()
    {
        $this->sma->checkPermissions();
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->reports_model->getStaff();        
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('bbq_cover_validtion_request_notification_report')));
        $meta = array('page_title' => lang('bbq_cover_validtion_request_notification_report'), 'bc' => $bc);
        
        $this->page_construct('reports/bbq_notification', $meta, $this->data);
    }

   public function get_bbqnotificationrports(){

        // $this->sma->checkPermissions('bbq',TRUE);        
        $start = $this->input->post('start_date');
        $end = $this->input->post('end_date');
        $warehouse_id = $this->input->post('warehouse_id');
        $limit = $this->input->post('pagelimit');    
        $offsetSegment = 4;
        $offset = $this->uri->segment($offsetSegment,0);

        $this->session->set_userdata('start_date', $this->input->post('start_date'));
        $this->session->set_userdata('end_date', $this->input->post('end_date'));

        $data= '';
        if ($start != '' && $end != '') {
            
                 $data = $this->reports_model->get_bbqnotificationrports($start,$end,$warehouse_id,$limit,$offset);

                  
             if (!empty($data['data'])) {                 
                 $bbqNotifyreports = $data['data'];
             }
             else{                
                $bbqNotifyreports = 'empty';
             }
             
        }
        else{
            $bbqNotifyreports = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('reports/get_bbqnotificationrports',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('bbqrports' => $bbqNotifyreports,'pagination'=>$pagination));
   } 

    function loyalty_points()
    {
        // $this->sma->checkPermissions('loyalty_points');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->reports_model->getStaff();
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('loyalty_points')));
        $meta = array('page_title' => lang('loyalty_points'), 'bc' => $bc);
        $this->page_construct('reports/loyalty_points', $meta, $this->data);
    }   
    function getLoyalpointsReport($warehouse_id = NULL)
    {
        // $this->sma->checkPermissions('loyalty_points', TRUE);        

        if (!$this->Owner && !$warehouse_id) {
            $user = $this->site->getUser();
            $warehouse_id = $user->warehouse_id;
        }

        $this->load->library('datatables');
        if ($warehouse_id) {
            $this->datatables
                ->select("C.id as customer_id,'sno',loyalty_points.loyalty_card_no, C.name, C.address, C.city, C.state, C.phone,loyalty_points.total_points")
                ->from('loyalty_points')
                ->join('companies C', 'C.id=loyalty_points.customer_id', 'left')                           
                ->where('loyalty_points.loyalty_card_no !=', '')   
                ->add_column("Actions", "<div class='text-center'><a class=\"tip\" title='" . lang("view_report") . "' href='" . admin_url('reports/loyalty_point_summary/$1') . "'><span class='label label-primary'>" . lang("view_report") . "</span></a></div>", "customer_id")
                ->unset_column('customer_id');
                /*->join('warehouses', 'warehouses.id=loyalty_points.warehouse_id', 'left')
                ->where('warehouse_id', $warehouse_id);         */       
        } else {
            $this->datatables
                ->select("C.id as customer_id,'sno',loyalty_points.loyalty_card_no, C.name, C.address, C.city, C.state, C.phone,loyalty_points.total_points,")
                ->from('loyalty_points')
                ->join('companies C', 'C.id=loyalty_points.customer_id', 'left')     
                ->where('loyalty_points.loyalty_card_no !=', '')           
                ->add_column("Actions", "<div class='text-center'><a class=\"tip\" title='" . lang("view_report") . "' href='" . admin_url('reports/loyalty_point_summary/$1') . "'><span class='label label-primary'>" . lang("view_report") . "</span></a></div>", "customer_id")
                ->unset_column('customer_id');
                // ->join('warehouses', 'warehouses.id=loyalty_points.warehouse_id', 'left');                
        }
        // print_r($this->datatables);exit;
        echo $this->datatables->generate();
    }  
 function loyalty_point_summary($customer_id = null)
    {
        // $this->sma->checkPermissions('loyalty_points');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->reports_model->getStaff();
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('loyalty_point_summary')));
        $meta = array('page_title' => lang('loyalty_point_summary'), 'bc' => $bc);
        $this->page_construct('reports/loyalty_point_summary', $meta, $this->data);
    }


    function getLoyaltySummary($customer = null)
    {
        $customer = $this->input->get('customer') ? $this->input->get('customer') : NULL;
        
        // $this->sma->checkPermissions('loyalty_points', TRUE);    
        $this->load->library('datatables');
        //{$this->db->dbprefix('loyalty_points_details')}
           $this->datatables
                ->select("'sno',(CASE
                            WHEN (({$this->db->dbprefix('loyalty_points_details')}.accumulation_points !=0.00 AND {$this->db->dbprefix('loyalty_points_details')}.redemption_points =0.00)) THEN 'Accumulation'
                            WHEN (({$this->db->dbprefix('loyalty_points_details')}.redemption_points !=0.00 AND {$this->db->dbprefix('loyalty_points_details')}.accumulation_points =0.00)) THEN 'Redemtion'                                                     
                            ELSE 'Accumulation With Redemtion '
                            END) AS table_status,DATE_FORMAT(B.date,'%d-%m-%Y') AS bill_date,B.bill_number,SUM(B.total-B.total_discount+CASE WHEN (B.tax_type= 1) THEN B.total_tax ELSE 0 END) AS grand_total,loyalty_points_details.accumulation_points,loyalty_points_details.redemption_points,loyalty_points_details.id,loyalty_points_details.id")
                ->from("loyalty_points_details")
                ->join("bils B","B.id = loyalty_points_details.bill_id")                
                ->where("B.customer_id",$customer)
                ->group_by("B.id, loyalty_points_details.identify")
                ->order_by('B.id ASC');
            
            // $total = $this->db->get();

        echo $this->datatables->generate();
    }
     function item_stock()
    {
        
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('stock_report')));
        $meta = array('page_title' => lang('stock_report'), 'bc' => $bc);
        $this->page_construct('reports/stock_report', $meta, $this->data);
    }   
    function item_stock_details($warehouse_id = NULL)
    {
        // $this->sma->checkPermissions('loyalty_points', TRUE);        

        if (!$this->Owner && !$warehouse_id) {
            $user = $this->site->getUser();
            $warehouse_id = $user->warehouse_id;
        }

        $this->load->library('datatables');
	    
        $this->datatables
                ->select("'sno',".$this->db->dbprefix('recipe') . ".name as item_name,".$this->db->dbprefix('warehouses') . ".name as store_name,stock_in,stock_out,batch,cost_price,selling_price,expiry_date", FALSE)
                ->from('pro_stock_master')
		->join('warehouses','warehouses.id=pro_stock_master.store_id')
                ->join('recipe','recipe.id=pro_stock_master.product_id');
                
        echo $this->datatables->generate();
    }  

       function modal_view()
    {        
        /*$this->sma->checkPermissions('index', TRUE);*/
        $this->data['modal_js'] = $this->site->modal_js();        
        $this->load->view($this->theme.'reports/modal_view', $this->data);
    }   
    public function report_view_access()
    {
        $pass_code = $this->input->post('pass_code');
         $data = $this->reports_model->check_reportview_access($pass_code); 

         if($data != 0)  {
                 $this->session->set_userdata('report_view_access', $data);
                 $this->sma->send_json($data);     
         }else{
           $this->sma->send_json(0);
         }
    }
    
    
    function store_request_reports()
    {
        $this->sma->checkPermissions();
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->reports_model->getStaff();        
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('store_request')));
        $meta = array('page_title' => lang('store_request'), 'bc' => $bc);
        
        $this->page_construct('reports/pro_store_request_reports', $meta, $this->data);
    }

   public function get_store_request_rports(){

        // $this->sma->checkPermissions('bbq',TRUE);        
        $start = $this->input->post('start_date');
        $end = $this->input->post('end_date');
        $warehouse_id = $this->input->post('warehouse_id');
        $limit = $this->input->post('pagelimit');    
        $offsetSegment = 4;
        $offset = $this->uri->segment($offsetSegment,0);

        $this->session->set_userdata('start_date', $this->input->post('start_date'));
        $this->session->set_userdata('end_date', $this->input->post('end_date'));

        $data= '';
        if ($start != '' && $end != '') {
            
            $data = $this->reports_model->getStoreRequest_Report($start,$end,$warehouse_id,$limit,$offset);

                      
             if (!empty($data['data'])) {                 
                 $report_details = $data['data'];
             }
             else{                
                $report_details = 'empty';
             }
            
        }
        else{
            $report_details = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('reports/get_store_request_rports',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('report' => $report_details,'pagination'=>$pagination));
   }
   function quotes_request_reports()
    {
        $this->sma->checkPermissions();
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->reports_model->getStaff();        
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('quotes_request')));
        $meta = array('page_title' => lang('quotes_request'), 'bc' => $bc);
        
        $this->page_construct('reports/pro_quotes_request_reports', $meta, $this->data);
    }

   public function get_quotes_request_reports(){

        // $this->sma->checkPermissions('bbq',TRUE);        
        $start = $this->input->post('start_date');
        $end = $this->input->post('end_date');
        $warehouse_id = $this->input->post('warehouse_id');
        $limit = $this->input->post('pagelimit');    
        $offsetSegment = 4;
        $offset = $this->uri->segment($offsetSegment,0);

        $this->session->set_userdata('start_date', $this->input->post('start_date'));
        $this->session->set_userdata('end_date', $this->input->post('end_date'));

        $data= '';
        if ($start != '' && $end != '') {
            
            $data = $this->reports_model->getQuotesRequest_Report($start,$end,$warehouse_id,$limit,$offset);

                      
             if (!empty($data['data'])) {                 
                 $report_details = $data['data'];
             }
             else{                
                $report_details = 'empty';
             }
            
        }
        else{
            $report_details = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('reports/get_quotes_request_reports',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('report' => $report_details,'pagination'=>$pagination));
   }
    function quotation_reports()
    {
        $this->sma->checkPermissions();
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->reports_model->getStaff();        
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('quotation')));
        $meta = array('page_title' => lang('quotation'), 'bc' => $bc);
        
        $this->page_construct('reports/pro_quotation_reports', $meta, $this->data);
    }

   public function get_quotation_reports(){

        // $this->sma->checkPermissions('bbq',TRUE);        
        $start = $this->input->post('start_date');
        $end = $this->input->post('end_date');
        $warehouse_id = $this->input->post('warehouse_id');
        $limit = $this->input->post('pagelimit');    
        $offsetSegment = 4;
        $offset = $this->uri->segment($offsetSegment,0);

        $this->session->set_userdata('start_date', $this->input->post('start_date'));
        $this->session->set_userdata('end_date', $this->input->post('end_date'));

        $data= '';
        if ($start != '' && $end != '') {
            
            $data = $this->reports_model->getQuotation_Report($start,$end,$warehouse_id,$limit,$offset);

                      
             if (!empty($data['data'])) {                 
                 $report_details = $data['data'];
             }
             else{                
                $report_details = 'empty';
             }
            
        }
        else{
            $report_details = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('reports/get_quotation_reports',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('report' => $report_details,'pagination'=>$pagination));
   }
    function purchase_order_reports()
    {
        $this->sma->checkPermissions();
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->reports_model->getStaff();        
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('purchase_order_reports')));
        $meta = array('page_title' => lang('purchase_order_reports'), 'bc' => $bc);
        
        $this->page_construct('reports/pro_purchase_order_reports', $meta, $this->data);
    }

   public function get_purchase_order_reports(){

        // $this->sma->checkPermissions('bbq',TRUE);        
        $start = $this->input->post('start_date');
        $end = $this->input->post('end_date');
        $warehouse_id = $this->input->post('warehouse_id');
        $limit = $this->input->post('pagelimit');    
        $offsetSegment = 4;
        $offset = $this->uri->segment($offsetSegment,0);

        $this->session->set_userdata('start_date', $this->input->post('start_date'));
        $this->session->set_userdata('end_date', $this->input->post('end_date'));

        $data= '';
        if ($start != '' && $end != '') {
            
            $data = $this->reports_model->getPurchaseOrder_Report($start,$end,$warehouse_id,$limit,$offset);

                      
             if (!empty($data['data'])) {                 
                 $report_details = $data['data'];
             }
             else{                
                $report_details = 'empty';
             }
            
        }
        else{
            $report_details = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('reports/get_purchase_order_reports',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('report' => $report_details,'pagination'=>$pagination));
   }
   function purchase_invoice_reports()
    {
        $this->sma->checkPermissions();
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->reports_model->getStaff();        
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('purchase_invoice_reports')));
        $meta = array('page_title' => lang('purchase_invoice_reports'), 'bc' => $bc);
        
        $this->page_construct('reports/pro_purchase_invoice_reports', $meta, $this->data);
    }

   public function get_purchase_invoice_reports(){

        // $this->sma->checkPermissions('bbq',TRUE);        
        $start = $this->input->post('start_date');
        $end = $this->input->post('end_date');
        $warehouse_id = $this->input->post('warehouse_id');
        $limit = $this->input->post('pagelimit');    
        $offsetSegment = 4;
        $offset = $this->uri->segment($offsetSegment,0);

        $this->session->set_userdata('start_date', $this->input->post('start_date'));
        $this->session->set_userdata('end_date', $this->input->post('end_date'));

        $data= '';
        if ($start != '' && $end != '') {
            
            $data = $this->reports_model->getPurchaseInvoice_Report($start,$end,$warehouse_id,$limit,$offset);

                      
             if (!empty($data['data'])) {                 
                 $report_details = $data['data'];
             }
             else{                
                $report_details = 'empty';
             }
            
        }
        else{
            $report_details = 'error';
        }
        $total = $data['total'];
        $pagination = $this->pagination('reports/get_purchase_invoice_reports',$limit,$offsetSegment,$total);
        $this->sma->send_json(array('report' => $report_details,'pagination'=>$pagination));
   }
/*SELECT PD.id,DATE_FORMAT(B.date,'%d-%m-%Y') AS bill_date,B.bill_number,(CASE WHEN(PD.identify =1) THEN 'Accumulation' ELSE 'Redemtion'  END) AS table_status,PD.identify,SUM(B.total-B.total_discount+CASE WHEN (B.tax_type= 1) THEN B.total_tax ELSE 0 END) AS grand_total,PD.points
FROM `srampos_loyalty_points_details` PD 
JOIN srampos_bils B ON B.id = PD.bill_id
WHERE B.customer_id = 13
GROUP BY B.id, PD.identify*/
}
