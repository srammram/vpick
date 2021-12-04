<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Reports_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getProductNames($term, $limit = 5)
    {
        $this->db->select('id, code, name')
            ->like('name', $term, 'both')->or_like('code', $term, 'both');
        $this->db->limit($limit);
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
  function getSettings()
    {
        $q = $this->db->get('settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getStaff()
    {
        if ($this->Admin) {
            $this->db->where('group_id !=', 1);
        }
        $this->db->where('group_id !=', 3)->where('group_id !=', 4);
        $q = $this->db->get('users');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getSalesTotals($customer_id)
    {

        $this->db->select('SUM(COALESCE('.$this->db->dbprefix('sales').'.grand_total, 0)) as total_amount, SUM(COALESCE('.$this->db->dbprefix('sales').'.paid, 0)) as paid', FALSE)
        ->join('bils b','b.sales_id=sales.id')
            ->where('sales.customer_id', $customer_id);
            if(!$this->Owner){
                $this->db->where('b.table_whitelisted', 0); 
            }
        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getCustomerSales($customer_id)
    {
        $this->db->from('sales')->where('sales.customer_id', $customer_id);
        $this->db->join('bils b','b.sales_id=sales.id');
        if(!$this->Owner && !$this->Admin){
                $this->db->where('b.table_whitelisted', 0); 
            }
        return $this->db->count_all_results();
    }

    public function getCustomerQuotes($customer_id)
    {
        $this->db->from('quotes')->where('customer_id', $customer_id);
        return $this->db->count_all_results();
    }

    public function getCustomerReturns($customer_id)
    {
        $this->db->from('sales')->where('customer_id', $customer_id)->where('sale_status', 'returned');
        return $this->db->count_all_results();
    }

    public function getStockValue()
    {
        $q = $this->db->query("SELECT SUM(by_price) as stock_by_price, SUM(by_cost) as stock_by_cost FROM ( Select COALESCE(sum(" . $this->db->dbprefix('warehouses_products') . ".quantity), 0)*price as by_price, COALESCE(sum(" . $this->db->dbprefix('warehouses_products') . ".quantity), 0)*cost as by_cost FROM " . $this->db->dbprefix('products') . " JOIN " . $this->db->dbprefix('warehouses_products') . " ON " . $this->db->dbprefix('warehouses_products') . ".product_id=" . $this->db->dbprefix('products') . ".id GROUP BY " . $this->db->dbprefix('products') . ".id )a");
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getWarehouseStockValue($id)
    {
        $q = $this->db->query("SELECT SUM(by_price) as stock_by_price, SUM(by_cost) as stock_by_cost FROM ( Select sum(COALESCE(" . $this->db->dbprefix('warehouses_products') . ".quantity, 0))*price as by_price, sum(COALESCE(" . $this->db->dbprefix('warehouses_products') . ".quantity, 0))*cost as by_cost FROM " . $this->db->dbprefix('products') . " JOIN " . $this->db->dbprefix('warehouses_products') . " ON " . $this->db->dbprefix('warehouses_products') . ".product_id=" . $this->db->dbprefix('products') . ".id WHERE " . $this->db->dbprefix('warehouses_products') . ".warehouse_id = ? GROUP BY " . $this->db->dbprefix('products') . ".id )a", array($id));
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    // public function getmonthlyPurchases()
    // {
    //     $myQuery = "SELECT (CASE WHEN date_format( date, '%b' ) Is Null THEN 0 ELSE date_format( date, '%b' ) END) as month, SUM( COALESCE( total, 0 ) ) AS purchases FROM purchases WHERE date >= date_sub( now( ) , INTERVAL 12 MONTH ) GROUP BY date_format( date, '%b' ) ORDER BY date_format( date, '%m' ) ASC";
    //     $q = $this->db->query($myQuery);
    //     if ($q->num_rows() > 0) {
    //         foreach (($q->result()) as $row) {
    //             $data[] = $row;
    //         }
    //         return $data;
    //     }
    //     return FALSE;
    // }

    public function getChartData()
    {
        $myQuery = "SELECT S.month,
        COALESCE(S.sales, 0) as sales,
        COALESCE( P.purchases, 0 ) as purchases,
        COALESCE(S.tax1, 0) as tax1,
        COALESCE(S.tax2, 0) as tax2,
        COALESCE( P.ptax, 0 ) as ptax
        FROM (  SELECT  date_format(date, '%Y-%m') Month,
                SUM(total) Sales,
                SUM(recipe_tax) tax1,
                SUM(order_tax) tax2
                FROM " . $this->db->dbprefix('bils') . "
                WHERE date >= date_sub( now( ) , INTERVAL 12 MONTH )
                GROUP BY date_format(date, '%Y-%m')) S
            LEFT JOIN ( SELECT  date_format(date, '%Y-%m') Month,
                        SUM(product_tax) ptax,
                        SUM(order_tax) otax,
                        SUM(total) purchases
                        FROM " . $this->db->dbprefix('purchases') . "
                        GROUP BY date_format(date, '%Y-%m')) P
            ON S.Month = P.Month
            ORDER BY S.Month";
            
        $q = $this->db->query($myQuery);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getDailySales($year, $month, $warehouse_id = NULL,$report_view_access,$report_show)
    {

        $myQuery = "SELECT DATE_FORMAT( date,  '%e' ) AS date, SUM( COALESCE( total_tax, 0 ) ) AS tax,SUM( COALESCE( recipe_tax, 0 ) ) AS tax1, SUM( COALESCE( order_tax, 0 ) ) AS tax2, SUM( COALESCE( grand_total, 0 ) ) AS grand_total123,SUM( COALESCE( total, 0 ) ) AS total123, SUM( COALESCE( total_discount, 0 ) ) AS discount, SUM( COALESCE( shipping, 0 ) ) AS shipping,SUM(total-CASE WHEN (tax_type= 0) THEN total_tax ELSE 0 END) AS total,SUM(total-total_discount+CASE WHEN (tax_type= 1) THEN total_tax ELSE 0 END) AS grand_total
            FROM " . $this->db->dbprefix('bils') . " WHERE payment_status ='Completed'AND ";

        if ($warehouse_id) {
            $myQuery .= " warehouse_id = {$warehouse_id} AND ";
        }

        if($report_view_access != 1){
            $myQuery .= " table_whitelisted =".$report_show." AND";
        }

        $myQuery .= " DATE_FORMAT( date,  '%Y-%m' ) =  '{$year}-{$month}'
            GROUP BY DATE_FORMAT( date,  '%e' )";
           
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getMonthlySales($year, $warehouse_id = NULL,$report_view_access,$report_show)
    {
        $myQuery = "SELECT DATE_FORMAT( date,  '%c' ) AS date,SUM( COALESCE( total_tax, 0 ) ) AS tax, SUM( COALESCE( recipe_tax, 0 ) ) AS tax1, SUM( COALESCE( order_tax, 0 ) ) AS tax2, SUM( COALESCE( grand_total, 0 ) ) AS total,SUM(total-CASE WHEN (tax_type= 0) THEN total_tax ELSE 0 END) AS total, SUM( COALESCE( total_discount, 0 ) ) AS discount, SUM( COALESCE( shipping, 0 ) ) AS shipping,SUM(total-total_discount+CASE WHEN (tax_type= 1) THEN total_tax ELSE 0 END) AS grand_total
            FROM " . $this->db->dbprefix('bils') . " WHERE payment_status ='Completed' AND ";
        if ($warehouse_id) {
            $myQuery .= " warehouse_id = {$warehouse_id} AND ";
        }        
        if($report_view_access != 1){
            $myQuery .= " table_whitelisted = ".$report_show." AND";
        }
        $myQuery .= " DATE_FORMAT( date,  '%Y' ) =  '{$year}'
            GROUP BY date_format( date, '%c' ) ORDER BY date_format( date, '%c' ) ASC";
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

 public function getDaysreport($start,$end,$warehouse_id,$day,$defalut_currency,$limit,$offset)
    {  

        $where ='';
        if($warehouse_id != 0)
        {
            $where = "AND P.warehouse_id =".$warehouse_id."";
        }
        if(isset($day) &&  $day != '0')
        {
            $where .= "AND DATE_FORMAT(P.date, '%W' ) ='".$day."'";
        }        
        if(!$this->Owner && !$this->Admin){
            $where .= " AND P.table_whitelisted =0";
        }
 
         $User = "SELECT U.id,DATE_FORMAT(P.date, '%W' ) as day
            FROM " . $this->db->dbprefix('bils') . " P
            JOIN " . $this->db->dbprefix('users') . " U ON P.created_by = U.id
            JOIN " . $this->db->dbprefix('payments') . " PM ON PM.bill_id = P.id
            JOIN " . $this->db->dbprefix('warehouses') . " WH ON WH.id = P.warehouse_id
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
             P.payment_status ='Completed' ".$where." GROUP By ( CASE DAYOFWEEK(P.date) WHEN 1 THEN 7 ELSE DAYOFWEEK(P.date) END )  ORDER BY ( CASE DAYOFWEEK(P.date) WHEN 1 THEN 7 ELSE DAYOFWEEK(P.date) END )";

            $limit_q = " limit $offset,$limit";
            $t = $this->db->query($User);
            if($limit!=0) $User .=$limit_q;
            $u = $this->db->query($User);
        
        if ($u->num_rows() > 0) {
            foreach (($u->result()) as $uow) {
               
                    $myQuery = "SELECT DATE_FORMAT(P.date, '%Y-%m-%d') as bill_date,DATE_FORMAT(P.date, '%H:%i') as bill_time,WH.name as warehouse,ST.name as bill_type,U.first_name AS username,P.bill_number AS Bill_No,SUM(DISTINCT CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id = ".$defalut_currency.")) THEN PM.amount ELSE 0 END) AS Cash,SUM(DISTINCT CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id != ".$defalut_currency.")) THEN amount_exchange*currency_rate ELSE 0 END) as For_Ex,SUM(DISTINCT CASE WHEN PM.paid_by = 'CC' THEN PM.amount ELSE 0 END) AS Credit_Card,SUM(DISTINCT CASE WHEN PM.paid_by = 'credit' THEN PM.amount ELSE 0 END) AS credit,SUM(DISTINCT P.paid) AS Bill_amt,SUM(DISTINCT P.balance) AS return_balance,SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id != ".$defalut_currency.")) THEN SC.amount ELSE 0 END) as ForEx
                    FROM " . $this->db->dbprefix('bils') . " P
                    JOIN " . $this->db->dbprefix('users') . " U ON P.created_by = U.id
                    JOIN " . $this->db->dbprefix('payments') . " PM ON PM.bill_id = P.id
                    JOIN " . $this->db->dbprefix('sale_currency') . " SC ON SC.bil_id = P.id
                    JOIN " . $this->db->dbprefix('warehouses') . " WH ON WH.id = P.warehouse_id
                    LEFT JOIN " . $this->db->dbprefix('sales_type') . " ST ON ST.id = P.bill_type
                        WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
                         P.payment_status ='Completed' AND DATE_FORMAT(P.date, '%W' ) ='".$uow->day."' 
                        ".$where." GROUP BY PM.bill_id ";   
                    /*echo   $myQuery;die;                                         */
                    $q = $this->db->query($myQuery);
                    if ($q->num_rows() > 0) {
                        foreach (($q->result()) as $row) {
                            $user[$uow->day][] = $row;
                        }
                        $uow->user = $user[$uow->day];
                        $data[] = $uow;
                    }
                }
            return array('data'=>$data,'total'=>$t->num_rows());
        }
        return FALSE;
    }
    public function getStaffDailySales($user_id, $year, $month, $warehouse_id = NULL,$report_view_access,$report_show)
    {
        $myQuery = "SELECT DATE_FORMAT( date,  '%e' ) AS date, SUM( COALESCE( total_tax, 0 ) ) AS tax,SUM( COALESCE( recipe_tax, 0 ) ) AS tax1, SUM( COALESCE( order_tax, 0 ) ) AS tax2, SUM( COALESCE( grand_total, 0 ) ) AS grand_total, SUM( COALESCE( total, 0 ) ) AS total, SUM( COALESCE( total_discount, 0 ) ) AS discount, SUM( COALESCE( shipping, 0 ) ) AS shipping
            FROM " . $this->db->dbprefix('bils')." WHERE ";

        if ($warehouse_id) {
            $myQuery .= " warehouse_id = {$warehouse_id} AND ";
        }
        if($report_view_access != 1 ){
            $myQuery .= " table_whitelisted =".$report_show." AND";
        }

        $myQuery .= " created_by = {$user_id} AND DATE_FORMAT( date,  '%Y-%m' ) =  '{$year}-{$month}'
            GROUP BY DATE_FORMAT( date,  '%e' )";
             
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getStaffMonthlySales($user_id, $year, $warehouse_id = NULL,$report_view_access,$report_show)
    {
        $myQuery = "SELECT DATE_FORMAT( date,  '%c' ) AS date, SUM( COALESCE( total_tax, 0 ) ) AS tax1, SUM( COALESCE( order_tax, 0 ) ) AS tax2, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( total_discount, 0 ) ) AS discount, SUM( COALESCE( shipping, 0 ) ) AS shipping
            FROM " . $this->db->dbprefix('sales') . " WHERE ";
        if ($warehouse_id) {
            $myQuery .= " warehouse_id = {$warehouse_id} AND ";
        }
        if($report_view_access != 1 ){
            $myQuery .= " table_whitelisted = ".$report_show." AND";
        }
        $myQuery .= " created_by = {$user_id} AND DATE_FORMAT( date,  '%Y' ) =  '{$year}'
            GROUP BY date_format( date, '%c' ) ORDER BY date_format( date, '%c' ) ASC";
            
        $q = $this->db->query($myQuery, false); 
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getPurchasesTotals($supplier_id)
    {
        $this->db->select('SUM(COALESCE(grand_total, 0)) as total_amount, SUM(COALESCE(paid, 0)) as paid', FALSE)
            ->where('supplier_id', $supplier_id);
        $q = $this->db->get('purchases');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getSupplierPurchases($supplier_id)
    {
        $this->db->from('purchases')->where('supplier_id', $supplier_id);
        return $this->db->count_all_results();
    }

    public function getStaffPurchases($user_id)
    {
        $this->db->select('count(id) as total, SUM(COALESCE(grand_total, 0)) as total_amount, SUM(COALESCE(paid, 0)) as paid', FALSE)
            ->where('created_by', $user_id);
        $q = $this->db->get('purchases');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getStaffSales($user_id)
    {
        $this->db->select('count(id) as total, SUM(COALESCE(grand_total, 0)) as total_amount, SUM(COALESCE(paid, 0)) as paid', FALSE)
            ->where('created_by', $user_id);
        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getTotalSales($start, $end, $warehouse_id = NULL)
    {
        $this->db->select('count(id) as total, sum(COALESCE(grand_total, 0)) as total_amount, SUM(COALESCE(paid, 0)) as paid, SUM(COALESCE(total_tax, 0)) as tax', FALSE)
            ->where('sale_status !=', 'pending')
            ->where('date BETWEEN ' . $start . ' and ' . $end);
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getTotalPurchases($start, $end, $warehouse_id = NULL)
    {
        $this->db->select('count(id) as total, sum(COALESCE(grand_total, 0)) as total_amount, SUM(COALESCE(paid, 0)) as paid, SUM(COALESCE(total_tax, 0)) as tax', FALSE)
            ->where('status !=', 'pending')
            ->where('date BETWEEN ' . $start . ' and ' . $end);
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $q = $this->db->get('purchases');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getTotalExpenses($start, $end, $warehouse_id = NULL)
    {
        $this->db->select('count(id) as total, sum(COALESCE(amount, 0)) as total_amount', FALSE)
            ->where('date BETWEEN ' . $start . ' and ' . $end);
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $q = $this->db->get('expenses');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getTotalPaidAmount($start, $end)
    {
        $this->db->select('count(id) as total, SUM(COALESCE(amount, 0)) as total_amount', FALSE)
            ->where('type', 'sent')
            ->where('date BETWEEN ' . $start . ' and ' . $end);
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getTotalReceivedAmount($start, $end)
    {
        $this->db->select('count(id) as total, SUM(COALESCE(amount, 0)) as total_amount', FALSE)
            ->where('type', 'received')
            ->where('date BETWEEN ' . $start . ' and ' . $end);
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getTotalReceivedCashAmount($start, $end)
    {
        $this->db->select('count(id) as total, SUM(COALESCE(amount, 0)) as total_amount', FALSE)
            ->where('type', 'received')->where('paid_by', 'cash')
            ->where('date BETWEEN ' . $start . ' and ' . $end);
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getTotalReceivedCCAmount($start, $end)
    {
        $this->db->select('count(id) as total, SUM(COALESCE(amount, 0)) as total_amount', FALSE)
            ->where('type', 'received')->where('paid_by', 'CC')
            ->where('date BETWEEN ' . $start . ' and ' . $end);
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getTotalReceivedChequeAmount($start, $end)
    {
        $this->db->select('count(id) as total, SUM(COALESCE(amount, 0)) as total_amount', FALSE)
            ->where('type', 'received')->where('paid_by', 'Cheque')
            ->where('date BETWEEN ' . $start . ' and ' . $end);
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getTotalReceivedPPPAmount($start, $end)
    {
        $this->db->select('count(id) as total, SUM(COALESCE(amount, 0)) as total_amount', FALSE)
            ->where('type', 'received')->where('paid_by', 'ppp')
            ->where('date BETWEEN ' . $start . ' and ' . $end);
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getTotalReceivedStripeAmount($start, $end)
    {
        $this->db->select('count(id) as total, SUM(COALESCE(amount, 0)) as total_amount', FALSE)
            ->where('type', 'received')->where('paid_by', 'stripe')
            ->where('date BETWEEN ' . $start . ' and ' . $end);
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getTotalReturnedAmount($start, $end)
    {
        $this->db->select('count(id) as total, SUM(COALESCE(amount, 0)) as total_amount', FALSE)
            ->where('type', 'returned')
            ->where('date BETWEEN ' . $start . ' and ' . $end);
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getWarehouseTotals($warehouse_id = NULL)
    {
        $this->db->select('sum(quantity) as total_quantity, count(id) as total_items', FALSE);
        $this->db->where('quantity !=', 0);
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $q = $this->db->get('warehouses_products');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getCosting($date, $warehouse_id = NULL, $year = NULL, $month = NULL)
    {
        $this->db->select('SUM( COALESCE( purchase_unit_cost, 0 ) * quantity ) AS cost, SUM( COALESCE( sale_unit_price, 0 ) * quantity ) AS sales, SUM( COALESCE( purchase_net_unit_cost, 0 ) * quantity ) AS net_cost, SUM( COALESCE( sale_net_unit_price, 0 ) * quantity ) AS net_sales', FALSE);
        if ($date) {
            $this->db->where('costing.date', $date);
        } elseif ($month) {
            $this->load->helper('date');
            $last_day = days_in_month($month, $year);
            $this->db->where('costing.date >=', $year.'-'.$month.'-01 00:00:00');
            $this->db->where('costing.date <=', $year.'-'.$month.'-'.$last_day.' 23:59:59');
        }

        if ($warehouse_id) {
            $this->db->join('sales', 'sales.id=costing.sale_id')
            ->where('sales.warehouse_id', $warehouse_id);
        }

        $q = $this->db->get('costing');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getExpenses($date, $warehouse_id = NULL, $year = NULL, $month = NULL)
    {
        $sdate = $date.' 00:00:00';
        $edate = $date.' 23:59:59';
        $this->db->select('SUM( COALESCE( amount, 0 ) ) AS total', FALSE);
        if ($date) {
            $this->db->where('date >=', $sdate)->where('date <=', $edate);
        } elseif ($month) {
            $this->load->helper('date');
            $last_day = days_in_month($month, $year);
            $this->db->where('date >=', $year.'-'.$month.'-01 00:00:00');
            $this->db->where('date <=', $year.'-'.$month.'-'.$last_day.' 23:59:59');
        }


        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }

        $q = $this->db->get('expenses');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getReturns($date, $warehouse_id = NULL, $year = NULL, $month = NULL)
    {
        $sdate = $date.' 00:00:00';
        $edate = $date.' 23:59:59';
        $this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total', FALSE)
        ->where('sale_status', 'returned');
        if ($date) {
            $this->db->where('date >=', $sdate)->where('date <=', $edate);
        } elseif ($month) {
            $this->load->helper('date');
            $last_day = days_in_month($month, $year);
            $this->db->where('date >=', $year.'-'.$month.'-01 00:00:00');
            $this->db->where('date <=', $year.'-'.$month.'-'.$last_day.' 23:59:59');
        }

        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }

        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getOrderDiscount($date, $warehouse_id = NULL, $year = NULL, $month = NULL)
    {
        $sdate = $date.' 00:00:00';
        $edate = $date.' 23:59:59';
        $this->db->select('SUM( COALESCE( order_discount, 0 ) ) AS order_discount', FALSE);
        if ($date) {
            $this->db->where('date >=', $sdate)->where('date <=', $edate);
        } elseif ($month) {
            $this->load->helper('date');
            $last_day = days_in_month($month, $year);
            $this->db->where('date >=', $year.'-'.$month.'-01 00:00:00');
            $this->db->where('date <=', $year.'-'.$month.'-'.$last_day.' 23:59:59');
        }

        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }

        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getExpenseCategories()
    {
        $q = $this->db->get('expense_categories');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getDailyPurchases($year, $month, $warehouse_id = NULL)
    {
        $myQuery = "SELECT DATE_FORMAT( date,  '%e' ) AS date, SUM( COALESCE( product_tax, 0 ) ) AS tax1, SUM( COALESCE( order_tax, 0 ) ) AS tax2, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( total_discount, 0 ) ) AS discount, SUM( COALESCE( shipping, 0 ) ) AS shipping
            FROM " . $this->db->dbprefix('purchases') . " WHERE ";
        if ($warehouse_id) {
            $myQuery .= " warehouse_id = {$warehouse_id} AND ";
        }
        $myQuery .= " DATE_FORMAT( date,  '%Y-%m' ) =  '{$year}-{$month}'
            GROUP BY DATE_FORMAT( date,  '%e' )";
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getMonthlyPurchases($year, $warehouse_id = NULL)
    {
        $myQuery = "SELECT DATE_FORMAT( date,  '%c' ) AS date, SUM( COALESCE( product_tax, 0 ) ) AS tax1, SUM( COALESCE( order_tax, 0 ) ) AS tax2, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( total_discount, 0 ) ) AS discount, SUM( COALESCE( shipping, 0 ) ) AS shipping
            FROM " . $this->db->dbprefix('purchases') . " WHERE ";
        if ($warehouse_id) {
            $myQuery .= " warehouse_id = {$warehouse_id} AND ";
        }
        $myQuery .= " DATE_FORMAT( date,  '%Y' ) =  '{$year}'
            GROUP BY date_format( date, '%c' ) ORDER BY date_format( date, '%c' ) ASC";
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getStaffDailyPurchases($user_id, $year, $month, $warehouse_id = NULL)
    {
        $myQuery = "SELECT DATE_FORMAT( date,  '%e' ) AS date, SUM( COALESCE( product_tax, 0 ) ) AS tax1, SUM( COALESCE( order_tax, 0 ) ) AS tax2, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( total_discount, 0 ) ) AS discount, SUM( COALESCE( shipping, 0 ) ) AS shipping
            FROM " . $this->db->dbprefix('purchases')." WHERE ";
        if ($warehouse_id) {
            $myQuery .= " warehouse_id = {$warehouse_id} AND ";
        }
        $myQuery .= " created_by = {$user_id} AND DATE_FORMAT( date,  '%Y-%m' ) =  '{$year}-{$month}'
            GROUP BY DATE_FORMAT( date,  '%e' )";
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getStaffMonthlyPurchases($user_id, $year, $warehouse_id = NULL)
    {
        $myQuery = "SELECT DATE_FORMAT( date,  '%c' ) AS date, SUM( COALESCE( product_tax, 0 ) ) AS tax1, SUM( COALESCE( order_tax, 0 ) ) AS tax2, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( total_discount, 0 ) ) AS discount, SUM( COALESCE( shipping, 0 ) ) AS shipping
            FROM " . $this->db->dbprefix('purchases') . " WHERE ";
        if ($warehouse_id) {
            $myQuery .= " warehouse_id = {$warehouse_id} AND ";
        }
        $myQuery .= " created_by = {$user_id} AND DATE_FORMAT( date,  '%Y' ) =  '{$year}'
            GROUP BY date_format( date, '%c' ) ORDER BY date_format( date, '%c' ) ASC";
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getBestSeller($start_date, $end_date, $warehouse_id = NULL)
    {
        $this->db
            ->select("recipe_name, recipe_code")->select_sum('quantity')
            ->join('bils', 'bils.id = bil_items.bil_id', 'left')
            ->where('date >=', $start_date)->where('date <=', $end_date)
            ->group_by('recipe_name, recipe_code')->order_by('sum(quantity)', 'desc')->limit(10);
        if ($warehouse_id) {
            $this->db->where('bil_items.warehouse_id', $warehouse_id);
        }
        $q = $this->db->get('bil_items');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    function getPOSSetting()
    {
        $q = $this->db->get('pos_settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getRecipeNames($term, $limit = 5)
    {
        $this->db->select('id, code, name')
            ->like('name', $term, 'both')->or_like('code', $term, 'both');
        $this->db->limit($limit);
        $q = $this->db->get('recipe');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    } 
  public function getProducts()
    {
        $this->db->select('id, code, name');
        $q = $this->db->get('products');

        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }    

public function getItemSaleReports($start,$end,$warehouse_id,$limit,$offset,$report_view_access,$report_show){


       $where ='';
        if($warehouse_id != 0)
        {
            $where = "AND P.warehouse_id =".$warehouse_id."";
        }

        if($report_view_access != 1)
         {
             $where .= " AND B.table_whitelisted = ".$report_show." ";
         }

        if(!$this->Owner && !$this->Admin){
            $where .= " OR P.table_whitelisted =0";
         }

       $category = "SELECT RC.id AS cate_id,RC.name as category, 'split_order' 
        FROM " . $this->db->dbprefix('recipe_categories') . " RC
        JOIN " . $this->db->dbprefix('recipe') . " R ON  R.category_id = RC.id
        JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.recipe_id = R.id
        JOIN " . $this->db->dbprefix('bils') . " B ON B.id = BI.bil_id        
        WHERE DATE(B.date) BETWEEN '".$start."' AND '".$end."' AND
         B.payment_status ='Completed' ".$where." GROUP BY RC.id";

            $limit_q = " limit $offset,$limit";
            $total = $this->db->query($category);
            if($limit!=0) $category .=$limit_q;
            $t = $this->db->query($category);

        if ($t->num_rows() > 0) {
            
            foreach ($t->result() as $row) {                  

                $this->db->select("recipe_categories.id AS sub_id,recipe_categories.name AS sub_category,bils.total_tax, 'order'")
                ->join('recipe', 'recipe.subcategory_id = recipe_categories.id')
                ->join('bil_items', 'bil_items.recipe_id = recipe.id')
                ->join('bils', 'bils.id = bil_items.bil_id')
                ->where('bils.payment_status', 'Completed')
                
                ->where('recipe.category_id', $row->cate_id);
                if(!$this->Owner && !$this->Admin){
                    $this->db->where('bils.table_whitelisted', 0); 
                }
                $this->db->group_by('recipe.subcategory_id');
                    
                $s = $this->db->get('recipe_categories');
               /*$s = $this->db->query($subcategory);*/
            if ($s->num_rows() > 0) {
                foreach ($s->result() as $sow) {
                    
                    $where = '';

                    if(!$this->Owner && !$this->Admin){
                        $where .= " AND B.table_whitelisted =0";
                    }

                    $myQuery = "SELECT R.price AS rate,WH.name as warehouse,R.name,SUM(BI.item_discount) AS item_discount,SUM(BI.off_discount) AS off_discount,SUM(BI.input_discount) AS input_discount,SUM(BI.quantity) AS quantity,SUM(BI.tax) AS tax1,SUM(BI.subtotal-CASE WHEN (BI.tax_type= 0) THEN BI.tax ELSE 0 END) as subtotal,SUM(BI.subtotal-BI.item_discount-BI.off_discount-BI.input_discount+CASE WHEN (BI.tax_type = 1) THEN BI.tax ELSE 0 END) as amt,SUM(CASE WHEN BI.tax_type = 1 THEN BI.tax ELSE 0 END) AS tax,SUM(CASE WHEN BI.tax_type = 0 THEN BI.tax ELSE 0 END) AS inclusive_tax,SUM(CASE WHEN BI.tax_type = 1 THEN BI.tax ELSE 0 END) AS exclusive_tax
                    FROM " . $this->db->dbprefix('bil_items') . " BI
                    JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
                    JOIN " . $this->db->dbprefix('bils') . " B ON B.id = BI.bil_id
                    JOIN " . $this->db->dbprefix('warehouses') . " WH ON WH.id = B.warehouse_id
                    WHERE DATE(B.date) BETWEEN '".$start."' AND '".$end."' AND
                    R.subcategory_id =".$sow->sub_id." AND  B.payment_status ='Completed'" .$where. " GROUP BY R.id " ; 
                    /*echo $myQuery;die;*/
                    $o = $this->db->query($myQuery);
                                                            
                        $split[$row->cate_id][] = $sow;
                        if ($o->num_rows() > 0) {                                    
                            foreach($o->result() as $oow){
                                $order[$sow->sub_id][] = $oow;
                            }
                        }
                        $sow->order = $order[$sow->sub_id];                   
                }                    
                        $row->split_order = $split[$row->cate_id];
        }else{
            $row->split_order = array();
        }                
        $data[] = $row;

            }
            //echo $total->num_rows();
           // print_R($data);exit;
            return array('data'=>$data,'total'=>$total->num_rows());
        }        
        return FALSE;   
    }   
 public function getPosSettlementReport($start,$end,$warehouse_id,$defalut_currency,$limit,$offset,$report_view_access,$report_show)
    {  
        $where ='';
        if($warehouse_id != 0)
        {
            $where = "AND P.warehouse_id =".$warehouse_id."";
        }
        if($report_view_access != 0)
         {
             $where .= " AND P.table_whitelisted = ".$report_show." ";
         }
         /*elseif ($report_view_access == 3) {
             $where .= " AND P.table_whitelisted = ".$report_show."";
         }*/

        /*if(!$this->Owner && !$this->Admin){
            $where .= " AND P.table_whitelisted =0";
        }*/

         $User = "SELECT U.id
            FROM " . $this->db->dbprefix('bils') . " P
            JOIN " . $this->db->dbprefix('users') . " U ON P.created_by = U.id
            JOIN " . $this->db->dbprefix('payments') . " PM ON PM.bill_id = P.id
            JOIN " . $this->db->dbprefix('warehouses') . " WH ON WH.id = P.warehouse_id
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
             P.payment_status ='Completed' ".$where."
            GROUP BY U.id ORDER BY U.username ASC";
            $limit_q = " limit $offset,$limit";
            $t = $this->db->query($User);
            if($limit!=0) $User .=$limit_q;
            $u = $this->db->query($User);
        
        if ($u->num_rows() > 0) {
            foreach (($u->result()) as $uow) {

                /*$myQuery = "SELECT DATE_FORMAT(P.date, '%H:%i') as bill_time,U.username,P.bill_number AS Bill_No,SUM(CASE WHEN PM.paid_by = 'cash' THEN PM.amount ELSE 0 END) AS Cash, SUM(CASE WHEN PM.paid_by = 'cash' THEN (PM.amount_usd*4000) ELSE 0 END) AS For_Ex, SUM(CASE WHEN PM.paid_by = 'cash' THEN PM.amount_usd ELSE 0 END) AS USD,SUM(CASE WHEN PM.paid_by = 'CC' THEN PM.amount ELSE 0 END) AS Credit_Card, P.paid AS Bill_amt,P.balance AS return_balance
                    FROM " . $this->db->dbprefix('bils') . " P
                    JOIN " . $this->db->dbprefix('users') . " U ON P.created_by = U.id
                    JOIN " . $this->db->dbprefix('payments') . " PM ON PM.bill_id = P.id
                        WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
                         P.payment_status ='Completed' AND U.id='".$uow->id."'
                        ".$where." GROUP BY PM.bill_id ORDER BY U.username ASC";*/
                    $myQuery = "SELECT DATE_FORMAT(P.date, '%Y-%m-%d') as bill_date,DATE_FORMAT(P.date, '%H:%i') as bill_time,WH.name as warehouse,ST.name as bill_type,U.first_name AS username,P.bill_number AS Bill_No,SUM(DISTINCT CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id = ".$defalut_currency.")) THEN PM.amount ELSE 0 END) AS Cash,SUM(DISTINCT CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id != ".$defalut_currency.")) THEN amount_exchange*currency_rate ELSE 0 END) as For_Ex,SUM(DISTINCT CASE WHEN PM.paid_by = 'CC' THEN PM.amount ELSE 0 END) AS Credit_Card,SUM(DISTINCT CASE WHEN PM.paid_by = 'credit' THEN PM.amount ELSE 0 END) AS credit,SUM(DISTINCT P.paid) AS Bill_amt,SUM(DISTINCT P.balance) AS return_balance,SUM(CASE WHEN ((PM.paid_by = 'cash') AND (SC.currency_id != ".$defalut_currency.")) THEN SC.amount ELSE 0 END) as ForEx
                    FROM " . $this->db->dbprefix('bils') . " P
                    JOIN " . $this->db->dbprefix('users') . " U ON P.created_by = U.id
                    JOIN " . $this->db->dbprefix('payments') . " PM ON PM.bill_id = P.id
                    JOIN " . $this->db->dbprefix('sale_currency') . " SC ON SC.bil_id = P.id
                    JOIN " . $this->db->dbprefix('warehouses') . " WH ON WH.id = P.warehouse_id
                    LEFT JOIN " . $this->db->dbprefix('sales_type') . " ST ON ST.id = P.bill_type
                        WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
                         P.payment_status ='Completed' AND U.id='".$uow->id."'
                        ".$where." GROUP BY PM.bill_id ORDER BY U.username ASC";                        
                    /*echo $myQuery;die;*/
                    $q = $this->db->query($myQuery);
                    if ($q->num_rows() > 0) {
                        foreach (($q->result()) as $row) {
                            $user[$uow->id][] = $row;
                        }
                        $uow->user = $user[$uow->id];
                        $data[] = $uow;
                    }
                }
            return array('data'=>$data,'total'=>$t->num_rows());
        }
        return FALSE;
    }
     public function getDaysummaryReport($start, $warehouse_id,$limit,$offset,$report_view_access,$report_show)
    {   
        $where ='';
        if($warehouse_id != 0)
        {
            $where = "AND P.warehouse_id =".$warehouse_id."";
        }
        if($report_view_access != 0){
             $where .= " AND P.table_whitelisted = ".$report_show." ";
        }
            $billQuery = "SELECT P.id AS bill_id,RC.name ,GROUP_CONCAT(DISTINCT  RC.id) cateids, P.bill_number,RC.id,(CASE WHEN (BI.tax_type = 1) THEN total_tax ELSE 0 END) as tax1,P.total_tax as tax,P.total_discount,(P.total-P.total_discount+CASE WHEN (P.tax_type= 1) THEN P.total_tax ELSE 0 END) as grand_total,SUM(P.total_tax) VAT,W.name branch
            FROM " . $this->db->dbprefix('bils') . " P
            LEFT JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.bil_id = P.id
            LEFT JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
            LEFT JOIN " . $this->db->dbprefix('recipe_categories') . " RC ON RC.id = R.category_id
            LEFT JOIN " . $this->db->dbprefix('warehouses') . " W ON W.id = P.warehouse_id
            WHERE DATE(P.date) = '".$start."' AND 
            P.payment_status ='Completed'  ".$where."
            GROUP BY P.bill_number ORDER BY BI.bil_id ASC";
            if($limit!=0) $billQuery .=" limit $offset,$limit";
            
            $billQuery_total = "SELECT P.id AS bill_id
            FROM " . $this->db->dbprefix('bils') . " P
            LEFT JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.bil_id = P.id
            LEFT JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
            LEFT JOIN " . $this->db->dbprefix('recipe_categories') . " RC ON RC.id = R.category_id
            WHERE DATE(P.date) = '".$start."' AND 
            P.payment_status ='Completed'  ".$where."
            GROUP BY P.bill_number ORDER BY BI.bil_id ASC";
        /* $billQuery = "SELECT P.id AS bill_id,RC.name ,GROUP_CONCAT(DISTINCT  rc.id) cateids, P.bill_number,RC.id,(CASE WHEN (BI.tax_type = 1) THEN total_tax ELSE 0 END) as tax,P.total_tax,P.total_discount,P.grand_total,SUM(P.total_tax) VAT
            FROM " . $this->db->dbprefix('bils') . " P
            LEFT JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.bil_id = P.id
            LEFT JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
            LEFT JOIN " . $this->db->dbprefix('recipe_categories') . " RC ON RC.id = R.category_id
            WHERE DATE(P.date) = '".$start."' AND 
            P.payment_status ='Completed'  ".$where."
            GROUP BY P.bill_number ORDER BY BI.bil_id ASC";*///ORDER BY RC.id ASC"; // GROUP BY P.bill_number ORDER BY RC.id ASC"; 
//GROUP_CONCAT(DISTINCT  rc.id) cateids,
        $b = $this->db->query($billQuery);
        $t = $this->db->query($billQuery_total);
        
        if($b->num_rows()==0){return false;}
        $billnumbers = "'".implode("','",array_column($b->result_array(), 'bill_number'))."'";
        $myQuery = "SELECT RC.name,RC.id,P.bill_number,P.total_tax,P.total_discount,P.grand_total
            FROM " . $this->db->dbprefix('bils') . " P
            JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.bil_id = P.id
            JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
            JOIN " . $this->db->dbprefix('recipe_categories') . " RC ON RC.id = R.category_id
            WHERE P.bill_number in (".$billnumbers.") AND DATE(P.date) = '".$start."' AND 
            P.payment_status ='Completed'  ".$where."
            GROUP BY RC.id ORDER BY RC.id ASC";//GROUP BY RC.id ORDER BY RC.id ASC";
            
        $categoryQuery = "SELECT P.bill_number,RC.name,RC.id cateids,SUM(BI.unit_price) categoryTotal
            FROM " . $this->db->dbprefix('bils') . " P
            LEFT JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.bil_id = P.id
            LEFT JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
            LEFT JOIN " . $this->db->dbprefix('recipe_categories') . " RC ON RC.id = R.category_id
            WHERE P.bill_number in (".$billnumbers.") AND DATE(P.date) = '".$start."' AND 
            P.payment_status ='Completed'  ".$where."
            GROUP BY RC.id,P.bill_number ORDER BY RC.id ASC";       
        
        $c =  $this->db->query($categoryQuery);
        $q = $this->db->query($myQuery);
        $categories = $c->result_array();
       
        $AllcategoryIds = array_unique(array_column($categories, 'cateids'));
       // print_R($AllcategoryIds);
        if ($q->num_rows() > 0) {
            $daywise = "<thead><tr>";
            $daywise .="<th>".lang('s.no')."</th>";
            $daywise .="<th>".lang('bill_no')."</th>";
            $daywise .="<th>".lang('branch')."</th>";
            foreach (($q->result()) as $row) {

                  $daywise .="<th>".$row->name."</th>";
            }
            $daywise .="<th>".lang('vat')."</th>";
            $daywise .="<th>".lang('discount')."</th>";
            $daywise .="<th>".lang('bill_amt')."</th>";
            $daywise .= "</tr></thead>";

            $daywise .= "<tbody>";
            $row_index = ($offset)?$offset+1:1;
            foreach (($b->result()) as $bill) {
                $daywise .="<tr><td>".$row_index."</td>";
                $daywise .="<td>".$bill->bill_number."</td>";
                $daywise .="<td>".$bill->branch."</td>";
                $categoryIds = explode(',',$bill->cateids);
               
                //print_r($categoryIds);
                foreach($AllcategoryIds as $k){
                    if(in_array($k,$categoryIds)){
                        $daywise .="<td>".$this->sma->formatMoney($this->site->getDayCategorySale($start,$k,$bill->bill_id,$warehouse_id))."</td>";
                    }
                   else{
                       $daywise .="<td>-</td>";
                    }
                }
                $daywise .="<td>".$this->sma->formatMoney($bill->tax)."</td>";
                $daywise .="<td>".$this->sma->formatMoney($bill->total_discount)."</td>";
                $daywise .="<td>".$this->sma->formatMoney($bill->grand_total)."</td>";
                $daywise .="</tr>";
                
                $row_index++;
            }//exit;
            $daywise .= "</tbody>";
            
            return array('data'=>$daywise,'total'=>$t->num_rows());
        }
        return FALSE;
    } 
    public function getMonthlyReport($start,$warehouse_id,$limit,$offset,$report_view_access,$report_show)
    {   
        $where ='';
        if($warehouse_id != 0)
        {
            $where = "AND P.warehouse_id =".$warehouse_id."";
        }
        if($report_view_access != 1){
             $where .= " AND P.table_whitelisted = ".$report_show." ";
         }

        $billQuery = "SELECT P.id AS bill_id,RC.name ,GROUP_CONCAT(DISTINCT  RC.id) cateids, P.bill_number,RC.id,(CASE WHEN (BI.tax_type = 1) THEN total_tax ELSE 0 END) as tax1,P.total_tax as tax,P.total_discount,(P.total-P.total_discount+CASE WHEN (P.tax_type= 1) THEN P.total_tax ELSE 0 END) as grand_total,SUM(P.total_tax) VAT,W.name branch
            FROM " . $this->db->dbprefix('bils') . " P
            LEFT JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.bil_id = P.id
            LEFT JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
            LEFT JOIN " . $this->db->dbprefix('recipe_categories') . " RC ON RC.id = R.category_id
            LEFT JOIN " . $this->db->dbprefix('warehouses') . " W ON W.id = P.warehouse_id
            WHERE DATE_FORMAT( P.date,  '%Y-%m' ) =  '".$start."' AND 
            P.payment_status ='Completed'  ".$where."
            GROUP BY P.bill_number ORDER BY BI.bil_id ASC";//ORDER BY RC.id ASC";
        if($limit!=0) $billQuery .=" limit $offset,$limit";
        $billQuery_total = "SELECT P.id AS bill_id
            FROM " . $this->db->dbprefix('bils') . " P
            LEFT JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.bil_id = P.id
            LEFT JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
            LEFT JOIN " . $this->db->dbprefix('recipe_categories') . " RC ON RC.id = R.category_id
            WHERE DATE_FORMAT( P.date,  '%Y-%m' ) =  '".$start."' AND 
            P.payment_status ='Completed'  ".$where."
            GROUP BY P.bill_number ORDER BY BI.bil_id ASC";
        $b = $this->db->query($billQuery);
        $t = $this->db->query($billQuery_total);
        if($b->num_rows()==0){return false;}
        $billnumbers = "'".implode("','",array_column($b->result_array(), 'bill_number'))."'";
        
        
        $myQuery = "SELECT RC.name,RC.id,P.bill_number,P.total_tax,P.total_discount,P.grand_total
            FROM " . $this->db->dbprefix('bils') . " P
            JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.bil_id = P.id
            JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
            JOIN " . $this->db->dbprefix('recipe_categories') . " RC ON RC.id = R.category_id
            WHERE P.bill_number in (".$billnumbers.") AND DATE_FORMAT( P.date,  '%Y-%m' ) =  '".$start."' AND 
            P.payment_status ='Completed'  ".$where."
            GROUP BY RC.id ORDER BY RC.id ASC";
            
        $categoryQuery = "SELECT RC.id cateids,SUM(BI.unit_price) categoryTotal
            FROM " . $this->db->dbprefix('bils') . " P
            LEFT JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.bil_id = P.id
            LEFT JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
            LEFT JOIN " . $this->db->dbprefix('recipe_categories') . " RC ON RC.id = R.category_id
            WHERE P.bill_number in (".$billnumbers.") AND DATE_FORMAT( P.date,  '%Y-%m' ) =  '".$start."' AND 
            P.payment_status ='Completed'  ".$where."
            GROUP BY RC.id ORDER BY RC.id ASC";
            
        $c =  $this->db->query($categoryQuery);
        $q = $this->db->query($myQuery);
        $categories = $c->result_array();
        
        $AllcategoryIds = array_column($c->result_array(), 'cateids');
        if ($q->num_rows() > 0) {
            $Monthlywise = "<thead><tr>";
            $Monthlywise .="<th>".lang('s.no')."</th>";
            $Monthlywise .="<th>".lang('bill_no')."</th>";
            $Monthlywise .="<th>".lang('branch')."</th>";
            foreach (($q->result()) as $row) {

                  $Monthlywise .="<th>".$row->name."</th>";
            }
            $Monthlywise .="<th>".lang('vat')."</th>";
            $Monthlywise .="<th>".lang('discount')."</th>";
            $Monthlywise .="<th>".lang('bill_amt')."</th>";
            $Monthlywise .= "</tr></thead>";

            $Monthlywise .= "<tbody>";
            $row_index = ($offset)?$offset+1:1;
            foreach (($b->result()) as $bill) {
                $Monthlywise .="<tr class='text-right'>";
                $Monthlywise .="<td>".$row_index."</td>";
                $Monthlywise .="<td class='text-center'>".$bill->bill_number."</td>";
                $Monthlywise .="<td>".$bill->branch."</td>";
                $categoryIds = explode(',',$bill->cateids);
               
                
                foreach($AllcategoryIds as $k){
                    if(in_array($k,$categoryIds)){
                        $Monthlywise .="<td>".$this->site->getMonthlyCategorySale($start,$k,$warehouse_id,$bill->bill_id)."</td>";
                    }
                   else{
                       $Monthlywise .="<td>-</td>";
                    }
                }
                $Monthlywise .="<td>".$this->sma->formatMoney($bill->tax)."</td>";
                $Monthlywise .="<td>".$this->sma->formatMoney($bill->total_discount)."</td>";
                $Monthlywise .="<td>".$this->sma->formatMoney($bill->grand_total)."</td>";
                $Monthlywise .="</tr>";
                
                $row_index++;
            }
            $Monthlywise .= "</tbody>"; 
        
            return array('data'=>$Monthlywise,'total'=>$t->num_rows());
        }
        return FALSE;
    }     
   public function getMonthlyReport1($start, $end, $warehouse_id)
    {   
        $where ='';
        if($warehouse_id != 0)
        {
            $where = "AND P.warehouse_id =".$warehouse_id."";
        }
        $myQuery = "SELECT RC.name,RC.id,P.bill_number,P.total_tax,P.total_discount,P.grand_total
            FROM " . $this->db->dbprefix('bils') . " P
            JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.bil_id = P.id
            JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
            JOIN " . $this->db->dbprefix('recipe_categories') . " RC ON RC.id = R.category_id
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND 
            P.payment_status ='Completed'  ".$where."
            GROUP BY RC.id ORDER BY RC.id ASC";


         $billQuery = "SELECT P.bill_number,RC.id,P.total_tax,P.total_discount,P.grand_total
            FROM " . $this->db->dbprefix('bils') . " P
            LEFT JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.bil_id = P.id
            LEFT JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
            LEFT JOIN " . $this->db->dbprefix('recipe_categories') . " RC ON RC.id = R.category_id
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND 
            P.payment_status ='Completed'  ".$where."
            GROUP BY P.bill_number ORDER BY RC.id ASC"; 

        $b = $this->db->query($billQuery);  
            
        $q = $this->db->query($myQuery);
        if ($q->num_rows() > 0) {
            $Monthlywise = "<thead><tr>";
            $Monthlywise .="<th>".lang('bill_no')."</th>";
            foreach (($q->result()) as $row) {

                  $Monthlywise .="<th>".$row->name."</th>";
            }
            $Monthlywise .="<th>".lang('vat')."</th>";
            $Monthlywise .="<th>".lang('discount')."</th>";
            $Monthlywise .="<th>".lang('bill_amt')."</th>";
            $Monthlywise .= "</tr></thead>";

            $Monthlywise .= "<tbody>";
            foreach (($b->result()) as $bill) {
               $Monthlywise .="<tr><td>".$bill->bill_number."</td>";
                 foreach (($b->result()) as $row) {
                   $Monthlywise .="<td>".$this->site->getMonthlyCategorySale($start,$end,$row->id,$warehouse_id)."</td>";
                 }   
                 $Monthlywise .="</tr>";
           
                $Monthlywise .="<td>".$bill->tax."</td>";
                $Monthlywise .="<td>".$bill->total_discount."</td>";
                $Monthlywise .="<td>".$bill->grand_total."</td>";
                $Monthlywise .="</tr>";
             }
            $Monthlywise .= "</tbody>";
            return $Monthlywise;
        }
        return FALSE;
    }    
 //public function getDaysummaryReport($start, $warehouse_id)
 //   {   
 //       $where ='';
 //       if($warehouse_id != 0)
 //       {
 //           $where = "AND P.warehouse_id =".$warehouse_id."";
 //       }
 //
 //      /* $myQuery = "SELECT U.username,P.bill_number AS Bill_No,SUM(CASE WHEN C.currency_id=2 THEN C.amount ELSE 0 END) AS Cash, SUM(CASE WHEN C.currency_id=1 THEN (C.currency_rate*4000) ELSE 0 END) AS For_Ex, SUM(CASE WHEN C.currency_id=1 THEN C.currency_rate ELSE 0 END) AS USD, SUM(P.grand_total) AS Bill_amt
 //       FROM srampos_bils P
 //            LEFT JOIN srampos_users U ON P.created_by = U.id
 //            LEFT JOIN srampos_sale_currency C ON C.bil_id = P.id
 //           WHERE DATE(P.date) = '".$start."' AND
 //            P.payment_status ='Completed' 
 //           GROUP BY P.bill_number ORDER BY U.username ASC";*/
 //
 //           /*SELECT RC.name,RC.id
 //            FROM srampos_bils B
 //            JOIN srampos_bil_items BI ON BI.bil_id = B.id
 //            JOIN srampos_recipe R ON R.id = BI.recipe_id
 //            JOIN srampos_recipe_categories RC ON RC.id = R.category_id       
 //            WHERE DATE(B.date) = '2018-02-17' AND
 //            B.payment_status ='Completed'
 //            GROUP BY RC.id ORDER BY RC.id*/
 //            
 //
 //      /* $myQuery = "SELECT DATE_FORMAT(P.date, '%H:%i') as bill_time,U.username,P.bill_number AS Bill_No,SUM(CASE WHEN PM.paid_by = 'cash' THEN PM.amount ELSE 0 END) AS Cash, SUM(CASE WHEN PM.paid_by = 'cash' THEN (PM.amount_usd*4000) ELSE 0 END) AS For_Ex, SUM(CASE WHEN PM.paid_by = 'cash' THEN PM.amount_usd ELSE 0 END) AS USD,SUM(CASE WHEN PM.paid_by = 'CC' THEN PM.amount ELSE 0 END) AS Credit_Card, SUM(P.paid) AS Bill_amt,SUM(P.balance) AS return_balance
 //            FROM " . $this->db->dbprefix('bils') . " P
 //           JOIN " . $this->db->dbprefix('users') . " U ON P.created_by = U.id
 //           JOIN " . $this->db->dbprefix('payments') . " PM ON PM.bill_id = P.id
 //                       WHERE DATE(P.date) = '".$start."' AND
 //                        P.payment_status ='Completed'  ".$where."
 //                       GROUP BY P.bill_number ORDER BY U.username ASC";    
 //                       echo $myQuery;die;*/
 //
 //           $myQuery = "SELECT RC.name,RC.id,P.bill_number,P.total_tax,P.total_discount,P.grand_total
 //           FROM " . $this->db->dbprefix('bils') . " P
 //           JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.bil_id = P.id
 //           JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
 //           JOIN " . $this->db->dbprefix('recipe_categories') . " RC ON RC.id = R.category_id
 //           WHERE DATE(P.date) = '".$start."' AND 
 //           P.payment_status ='Completed'  ".$where."
 //           GROUP BY RC.id ORDER BY RC.id ASC";
 //
 //
 //        $billQuery = "SELECT P.bill_number,RC.id,P.total_tax,P.total_discount,P.grand_total
 //           FROM " . $this->db->dbprefix('bils') . " P
 //           LEFT JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.bil_id = P.id
 //           LEFT JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
 //           LEFT JOIN " . $this->db->dbprefix('recipe_categories') . " RC ON RC.id = R.category_id
 //           WHERE DATE(P.date) = '".$start."' AND 
 //           P.payment_status ='Completed'  ".$where."
 //           GROUP BY P.bill_number ORDER BY RC.id ASC"; 
 //
 //       $b = $this->db->query($billQuery);       
 //         /*echo $myQuery;die;    */            
 //           
 //       $q = $this->db->query($myQuery);
 //       if ($q->num_rows() > 0) {
 //           $daywise = "<thead><tr>";
 //           $daywise .="<th>".lang('bill_no')."</th>";
 //           foreach (($q->result()) as $row) {
 //
 //                 $daywise .="<th>".$row->name."</th>";
 //           }
 //           $daywise .="<th>".lang('vat')."</th>";
 //           $daywise .="<th>".lang('discount')."</th>";
 //           $daywise .="<th>".lang('bill_amt')."</th>";
 //           $daywise .= "</tr></thead>";
 //
 //           $daywise .= "<tbody>";
 //           foreach (($b->result()) as $bill) {
 //              $daywise .="<tr><td>".$bill->bill_number."</td>";
 //                foreach (($b->result()) as $row) {
 //                  $daywise .="<td>".$this->site->getDayCategorySale($start,$row->id,$warehouse_id)."</td>";
 //                }   
 //                $daywise .="</tr>";
 //              
 //           }
 //           
 //           $daywise .= "</tbody>";
 //           return $daywise;
 //       }
 //       return FALSE;
 //   } 
public function getKotDetailsReport($start,$end,$warehouse_id,$limit,$offset)
    {  
        $where ='';
        if($warehouse_id != 0)
        {
            $where = "AND P.warehouse_id =".$warehouse_id."";
        }
        if(!$this->Owner && !$this->Admin){
            $where .= " AND P.table_whitelisted =0";
        }
        
        $User = "SELECT U.id,SUM(P.grand_total - round_total) AS round
            FROM " . $this->db->dbprefix('bils') . "  P
            JOIN " . $this->db->dbprefix('users') . " U ON P.created_by = U.id
            JOIN " . $this->db->dbprefix('payments') . " PM ON PM.bill_id = P.id
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
             P.payment_status ='Completed' ".$where."
            GROUP BY U.id ORDER BY U.username ASC";
            
            $limit_q = " limit $offset,$limit";
            $t = $this->db->query($User);
            if($limit!=0) $User .=$limit_q;
            $u = $this->db->query($User);
        if ($u->num_rows() > 0) {
            foreach (($u->result()) as $uow) {

                    $KotQuery = "SELECT K.id AS kitchenno,DATE_FORMAT(O.date, '%d-%m-%Y') AS kot_date,T.name AS table_name, U.first_name as username,OU.first_name AS steward,R.name AS item,BI.quantity,P.bill_number AS Bill_No, BI.subtotal AS Bill_amt1,(BI.subtotal-BI.item_discount-BI.off_discount-BI.input_discount+CASE WHEN (BI.tax_type = 1) THEN BI.tax ELSE 0 END) as Bill_amt,DATE_FORMAT(O.date, '%H:%i') as kot_time,BI.item_discount,BI.off_discount,BI.input_discount,(CASE WHEN (BI.tax_type = 1) THEN BI.tax ELSE 0 END) as tax,BI.tax as tax1,TY.name AS order_type,P.grand_total,P.round_total
                    FROM " . $this->db->dbprefix('bils') . " P
                    JOIN " . $this->db->dbprefix('sales') . " S ON S.id = P.sales_id
                    JOIN " . $this->db->dbprefix('orders') . " O ON O.split_id = S.sales_split_id
                    JOIN " . $this->db->dbprefix('order_items') . " OI ON OI.sale_id = O.id
                    JOIN  " . $this->db->dbprefix('sales_type') . " TY ON TY.id = O.order_type
                    JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.bil_id = P.id
                    JOIN " . $this->db->dbprefix('recipe') . " R ON OI.recipe_id = R.id
                    LEFT JOIN " . $this->db->dbprefix('restaurant_tables') . " T ON T.id = O.table_id
                    JOIN " . $this->db->dbprefix('kitchen_orders') . " K ON K.sale_id = O.id
                    JOIN " . $this->db->dbprefix('users') . " U ON U.id = P.created_by
                    JOIN " . $this->db->dbprefix('users') . " OU ON OU.id = O.created_by
                    WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
                    P.payment_status ='Completed' ".$where." AND OI.order_item_cancel_status= 0 AND U.id='".$uow->id."' GROUP BY BI.id ORDER BY U.username ASC ";    
                    /*echo $KotQuery;die;*/
                    $q = $this->db->query($KotQuery);
                    if ($q->num_rows() > 0) {
                        foreach (($q->result()) as $row) {
                            $user[$uow->id][] = $row;
                        }
                        $uow->user = $user[$uow->id];
                        $data[] = $uow;
                    }
                }
            return array('data'=>$data,'total'=>$t->num_rows());
        }
        return FALSE;
    }      
public function getKotCancelReport($start,$end,$warehouse_id,$limit,$offset)
    {   
        $where ='';
$where1 ='';
         if($warehouse_id != 0)
        {
            $where = "AND P.warehouse_id =".$warehouse_id."";
        }
        if(!$this->Owner && !$this->Admin){
            $where1 .= " AND O.table_whitelisted =0";
        }
        $KotCancel = "SELECT DATE_FORMAT(O.date, '%d-%m-%Y') date,OI.id,R.name AS recipename,OI.order_item_cancel_note,T.name AS table_name,U.username
        FROM " . $this->db->dbprefix('orders') . " O
        JOIN " . $this->db->dbprefix('order_items') . " OI ON OI.sale_id = O.id
        JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = OI.recipe_id 
        JOIN " . $this->db->dbprefix('kitchen_orders') . " K ON K.id = OI.kitchen_id 
        JOIN " . $this->db->dbprefix('users') . " U ON U.id = O.created_by 
        JOIN " . $this->db->dbprefix('restaurant_tables') . " T ON T.id = O.table_id
            WHERE DATE(O.date) BETWEEN '".$start."' AND '".$end."' AND OI.order_item_cancel_status= 1 ".$where1."";
        $limit_q = " limit $offset,$limit";        
        $t = $this->db->query($KotCancel);
        if($limit!=0) $KotCancel .=$limit_q;
        $q = $this->db->query($KotCancel);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return array('data'=>$data,'total'=>$t->num_rows());
        }
        return FALSE;
    }
public function getKotPendingReport($start,$end,$warehouse_id,$limit,$offset)
    {   
        $where ='';$where1='';
        if($warehouse_id != 0)
        {
            $where = "AND P.warehouse_id =".$warehouse_id."";
        }
if(!$this->Owner && !$this->Admin){
            $where1 .= " AND O.table_whitelisted =0";
        }
        $KotPending = "SELECT DATE_FORMAT(O.date, '%d-%m-%Y') AS Orderdate,O.id,U.username,OI.quantity,R.name AS recipename,T.name AS table_name,OI.subtotal
        FROM " . $this->db->dbprefix('orders') . "  O        
        JOIN " . $this->db->dbprefix('order_items') . " OI ON OI.sale_id = O.id
        JOIN " . $this->db->dbprefix('recipe') . " R ON OI.recipe_id = R.id
        LEFT JOIN " . $this->db->dbprefix('restaurant_tables') . " T ON T.id = O.table_id
        JOIN " . $this->db->dbprefix('kitchen_orders') . " K ON K.sale_id = O.id
        JOIN " . $this->db->dbprefix('users') . " U ON O.created_by = U.id
        WHERE DATE(O.date) BETWEEN '".$start."' AND '".$end."' AND  O.split_id NOT IN (SELECT sales_split_id FROM " . $this->db->dbprefix('sales') . " ) AND OI.order_item_cancel_status != 1 ".$where1." AND O.order_type != 4";
        
        $limit_q = " limit $offset,$limit";
        $t = $this->db->query($KotPending);
        if($limit!=0) $KotPending .=$limit_q;
        $q = $this->db->query($KotPending);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return array('data'=>$data,'total'=>$t->num_rows());
        }
        return FALSE;
    } 
public function getVoidBillsReport($start,$end,$warehouse_id,$limit,$offset,$report_view_access,$report_show)
    {   
        $where ='';
        if($warehouse_id != 0)
        {
            $where = "AND B.warehouse_id =".$warehouse_id."";
        }
        if($report_view_access != 1)
         {
             $where .= " AND B.table_whitelisted = ".$report_show." ";
         }

        $Void_Bills = "SELECT B.bill_number,DATE_FORMAT(B.date, '%d-%m-%Y') date,(BI.subtotal-BI.item_discount-BI.off_discount-BI.input_discount+CASE WHEN (BI.tax_type = 1) THEN BI.tax ELSE 0 END) as bill_amt,BI.quantity,OI.id,BI.recipe_name  AS recipename,OI.order_item_cancel_note,UO.username AS created_by,U.username AS Canceled,OI.unit_price,K.id AS kot,W.name as branch
        FROM " . $this->db->dbprefix('bils') . " B
       JOIN  " . $this->db->dbprefix('bil_items') . " BI ON BI.bil_id =B.id
       JOIN  " . $this->db->dbprefix('sales') . " S ON S.id =B.sales_id
       JOIN  " . $this->db->dbprefix('orders') . " O ON O.split_id =S.sales_split_id
        JOIN " . $this->db->dbprefix('order_items') . " OI ON OI.sale_id = O.id
        JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = OI.recipe_id 
        JOIN " . $this->db->dbprefix('kitchen_orders') . " K ON K.id = OI.kitchen_id 
        JOIN " . $this->db->dbprefix('users') . " U ON U.id = OI.order_item_cancel_id 
        JOIN " . $this->db->dbprefix('users') . " UO ON UO.id = O.created_by 
       
        JOIN " . $this->db->dbprefix('warehouses') . " W ON W.id = B.warehouse_id
            WHERE DATE(B.date) BETWEEN '".$start."' AND '".$end."' AND B.bil_status= 'Cancelled' ".$where." GROUP BY BI.id";
        
        $limit_q = " limit $offset,$limit";
        $t = $this->db->query($Void_Bills);
        if($limit!=0) $Void_Bills .=$limit_q;
        
        $q = $this->db->query($Void_Bills);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return array('data'=>$data,'total'=>$t->num_rows());
        }
        return FALSE;
    }                    
  public function getCashierReport($start,$end,$user,$limit,$offset,$group=null)
    {
        $where ='';if(!$this->Owner && !$this->Admin){
            $where = " AND P.table_whitelisted =0";
        }
        if(!empty($user)){
            $where .= " AND P.created_by = $user";
        }
        if(!empty($group)){
            $where .= " AND U.group_id = $group";
        }        

        /*( DATE(NOW()) >= DATE(DC.from_date) and DATE(NOW()) <= DATE(DC.to_date))*/
        $user_report = "SELECT DATE_FORMAT(P.date, '%d-%m-%Y') AS billdate,P.tax_type,P.total_tax,P.total_discount,P.total,U.username, P.grand_total,P.bill_number,ST.name AS order_type,(CASE WHEN (O.order_type = 1 ) THEN T.name ELSE ST.name END) AS table_name
        FROM " . $this->db->dbprefix('bils') . "  P
         JOIN " . $this->db->dbprefix('sales') . " S ON S.id = P.sales_id
         JOIN " . $this->db->dbprefix('orders') . " O ON  O.split_id = S.sales_split_id
         JOIN " . $this->db->dbprefix('sales_type') . " ST ON ST.id = O.order_type
         LEFT JOIN " . $this->db->dbprefix('restaurant_tables') . " T ON T.id = O.table_id
         JOIN " . $this->db->dbprefix('users') . " U ON U.id = P.created_by
         JOIN " . $this->db->dbprefix('groups') . " G ON G.id = U.group_id
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
             P.payment_status ='Completed'". $where." GROUP BY P.id";          
        $limit_q = " limit $offset,$limit";
        $t = $this->db->query($user_report);
        if($limit!=0) $user_report .=$limit_q;
        $q = $this->db->query($user_report);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return array('data'=>$data,'total'=>$t->num_rows());
        }
        return FALSE;
    }
 public function HomedeliveryCostomer() {

    $HomeDelivery = "SELECT C.id,C.name
        FROM " . $this->db->dbprefix('companies') . " C        
        JOIN " . $this->db->dbprefix('orders') . " O ON O.customer_id = C.id
            WHERE  O.order_type = 3   GROUP BY C.id";        
        $q = $this->db->query($HomeDelivery);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getHomedeliveryReport($start,$end,$warehouse_id,$customer,$limit,$offset)
    {
        $where ='';
        if($warehouse_id != 0)
        {
            $where = "AND P.warehouse_id =".$warehouse_id."";
        }
         if($customer != 0)
        {
            $where .= "AND P.customer_id =".$customer."";
        }

        $HomeDelivery = "SELECT DATE_FORMAT(P.date, '%d-%m-%Y') AS Orderdate,U.username,P.bill_number AS Bill_No,W.name branch,SUM(P.total-P.total_discount+CASE WHEN (P.tax_type= 1) THEN P.total_tax ELSE 0 END) AS grand_total,P.paid grand_total,CP.address,CP.name,SUM(P.grand_total - round_total) AS round,CONCAT('cash - ',SUM(DISTINCT CASE  WHEN ((PA.paid_by = 'cash') AND (SC.currency_id=2) AND (SC.amount!='')) THEN PA.amount 
WHEN ((PA.paid_by = 'cash' AND SC.currency_id=1 AND SC.amount!='')) THEN (SC.amount*SC.currency_rate) ELSE 0 END),' | CC - ',SUM(DISTINCT CASE
WHEN PA.paid_by = 'CC'  THEN PA.amount

ELSE 0 END),' | credit - ',SUM(DISTINCT CASE
WHEN PA.paid_by = 'credit'  THEN PA.amount

ELSE 0 END)) paid_by,U1.first_name delivery_person,GP.name group_name
        FROM " . $this->db->dbprefix('bils') . " P
        JOIN " . $this->db->dbprefix('users') . "  U ON P.created_by = U.id
        JOIN " . $this->db->dbprefix('sales') . " S ON S.id = P.sales_id
        JOIN " . $this->db->dbprefix('orders') . " O ON O.split_id = S.sales_split_id
        JOIN " . $this->db->dbprefix('companies') . " CP ON CP.id = O.customer_id
        JOIN " . $this->db->dbprefix('customer_groups') . " GP ON GP.id = CP.customer_group_id
        JOIN " . $this->db->dbprefix('users') . " U1 ON U1.id = P.delivery_person_id
        JOIN " . $this->db->dbprefix('warehouses') . " W ON W.id = P.warehouse_id
        JOIN " . $this->db->dbprefix('payments') . " PA ON PA.bill_id = P.id
        JOIN " . $this->db->dbprefix('sale_currency') . " SC ON SC.bil_id = P.id  
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
             P.payment_status ='Completed' AND O.order_type = 3 ".$where."  GROUP BY P.id";
             
        $limit_q = " limit $offset,$limit";
        $t = $this->db->query($HomeDelivery);
        if($limit!=0) $HomeDelivery .=$limit_q;
        $q = $this->db->query($HomeDelivery);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return array('data'=>$data,'total'=>$t->num_rows());
        }
        return FALSE;
    }
    public function getTakeAwayReport($start,$end,$warehouse_id,$limit,$offset)
    {
        $where ='';
        if($warehouse_id != 0)
        {
            $where = "AND P.warehouse_id =".$warehouse_id."";
        }

        $takeaway = "SELECT DATE_FORMAT(P.date, '%d-%m-%Y') AS Orderdate,U.username,P.bill_number AS Bill_No,W.name branch,P.paid,P.total_discount,SUM(P.total-P.total_discount+CASE WHEN (P.tax_type= 1) THEN P.total_tax ELSE 0 END) AS grand_total,CP.address,CP.name,SUM(P.grand_total - P.round_total) AS round,CONCAT('cash - ',SUM(DISTINCT CASE  WHEN ((PA.paid_by = 'cash') AND (SC.currency_id=2) AND (SC.amount!='')) THEN PA.amount 
WHEN ((PA.paid_by = 'cash' AND SC.currency_id=1 AND SC.amount!='')) THEN (SC.amount*SC.currency_rate) ELSE 0 END),' | CC - ',SUM(DISTINCT CASE
WHEN PA.paid_by = 'CC'  THEN PA.amount

ELSE 0 END),' | credit - ',SUM(DISTINCT CASE
WHEN PA.paid_by = 'credit'  THEN PA.amount

ELSE 0 END)) paid_by
        FROM " . $this->db->dbprefix('bils') . " P
        JOIN " . $this->db->dbprefix('users') . "  U ON P.created_by = U.id
        JOIN " . $this->db->dbprefix('sales') . " S ON S.id = P.sales_id
        JOIN " . $this->db->dbprefix('orders') . " O ON O.split_id = S.sales_split_id
        JOIN " . $this->db->dbprefix('companies') . " CP ON CP.id = O.customer_id
        JOIN " . $this->db->dbprefix('warehouses') . " W ON W.id = P.warehouse_id
        JOIN " . $this->db->dbprefix('payments') . " PA ON PA.bill_id = P.id
        JOIN " . $this->db->dbprefix('sale_currency') . " SC ON SC.bil_id = P.id  
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
             P.payment_status ='Completed' AND O.order_type = 2 ".$where."  GROUP BY P.id";
        /*echo $takeaway;die;*/
        $limit_q = " limit $offset,$limit";
        $t = $this->db->query($takeaway);//print_R($this->db->error());exit;
        if($limit!=0) $takeaway .=$limit_q;
        $q = $this->db->query($takeaway);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return array('data'=>$data,'total'=>$t->num_rows());
        }
        return FALSE;
    }    
public function getDiscountsummaryReport($start,$end, $warehouse_id,$limit,$offset,$report_view_access,$report_show)
    {
        $where ='';
        if($warehouse_id != 0)
        {
            $where = "AND P.warehouse_id =".$warehouse_id."";
        }        
        if($report_view_access != 1){
             $where .= " AND P.table_whitelisted = ".$report_show." ";
         }

        $dis_report = "SELECT DATE_FORMAT(P.date, '%d-%m-%Y') AS dis_date,SUM(P.total_discount) AS total_discount,W.name branch 
        FROM " . $this->db->dbprefix('bils') . "  P
        LEFT JOIN " . $this->db->dbprefix('warehouses') . " W ON W.id = P.warehouse_id    
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
             P.payment_status ='Completed' ".$where."
             GROUP BY DATE(P.date)";
        $limit_q = " limit $offset,$limit";
        $t = $this->db->query($dis_report);
        if($limit!=0) $dis_report .=$limit_q;
        $q = $this->db->query($dis_report);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return array('data'=>$data,'total'=>$t->num_rows());
        }
        return FALSE;
    }
public function getDiscountDetailsReport($start, $end, $warehouse_id,$limit,$offset,$report_view_access,$report_show)
    {
        $where ='';
        if($warehouse_id != 0)
        {
            $where = "AND P.warehouse_id =".$warehouse_id."";
        }

        if($report_view_access != 1)
         {
             $where .= " AND P.table_whitelisted = ".$report_show." ";
         }

        $dis_details = "SELECT DATE_FORMAT(P.date, '%d-%m-%Y') AS dis_date,P.total_discount,P.bill_number,UO.first_name AS username,U.first_name AS cashier,SUM(DISTINCT P.total-P.total_discount+CASE WHEN (P.tax_type= 1) THEN P.total_tax ELSE 0 END) as grand_total,W.name AS branch 
        FROM " . $this->db->dbprefix('bils') . " P
        JOIN " . $this->db->dbprefix('users') . " U ON  U.id = P.created_by
        JOIN " . $this->db->dbprefix('sales') . " S ON S.id = P.sales_id
        JOIN " . $this->db->dbprefix('orders') . " O ON O.split_id = S.sales_split_id
        JOIN " . $this->db->dbprefix('users') . " UO ON UO.id = O.created_by
        LEFT JOIN " . $this->db->dbprefix('warehouses') . " W ON W.id = P.warehouse_id          
            
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
             P.payment_status ='Completed' ".$where." GROUP BY P.id ";
        /*echo $dis_details;die;*/
        $limit_q = " limit $offset,$limit";
        $t = $this->db->query($dis_details);
        if($limit!=0) $dis_details .=$limit_q;
        $q = $this->db->query($dis_details);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return array('data'=>$data,'total'=>$t->num_rows());
        }
        return FALSE;
    }    
public function getTaxReport($start,$end,$warehouse_id,$limit,$offset,$report_view_access,$report_show)
    {
        $where ='';
        if($warehouse_id != 0)
        {
            $where = "AND P.warehouse_id =".$warehouse_id."";
        }
        if($report_view_access != 1)
         {
             $where .= " AND P.table_whitelisted = ".$report_show." ";
         }
        /*CONCAT(first_name, " ", last_name) AS Name*/
        $tax_report = "SELECT W.name branch,DATE_FORMAT(P.date, '%d-%m-%Y') AS tax_date,P.bill_number,P.tax_type,T.name AS Taxname,(CASE WHEN (P.tax_type= 1) THEN P.total_tax ELSE 0 END) as total_tax1,P.total_tax,P.grand_total,P.total_discount,P.total,SUM(total-total_discount+CASE WHEN (tax_type= 1) THEN total_tax ELSE 0 END) as final_amt
        FROM " . $this->db->dbprefix('bils') . "  P
        JOIN " . $this->db->dbprefix('tax_rates') . " T ON T.id = P.tax_id
        LEFT JOIN " . $this->db->dbprefix('warehouses') . " W ON W.id = P.warehouse_id            
            
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
             P.payment_status ='Completed' ".$where." GROUP BY P.id ";
        /*echo $dis_report;    die; */
        $limit_q = " limit $offset,$limit";
        $t = $this->db->query($tax_report);
        if($limit!=0) $tax_report .=$limit_q;
        $q = $this->db->query($tax_report);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return array('data'=>$data,'total'=>$t->num_rows());
        }
        return FALSE;
    }  

public function getPopularReports($start,$end,$warehouse_id,$limit,$offset,$report_view_access,$report_show){
    $where ='';
        if($warehouse_id != 0)
        {
            $where = "AND B.warehouse_id =".$warehouse_id."";
        }
        if($report_view_access != 1){
             $where .= " AND B.table_whitelisted = ".$report_show." ";
        }

        $this->db->start_cache();
        $myQuery = "SELECT RC.id AS cate_id,RC.name AS category,'split_order'
        FROM " . $this->db->dbprefix('recipe_categories') . " RC
       LEFT  JOIN " . $this->db->dbprefix('recipe') . " R ON R.category_id = RC.id
        JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.recipe_id = R.id
        JOIN " . $this->db->dbprefix('bils') . " B ON B.id = BI.bil_id
        WHERE DATE(B.date) BETWEEN '".$start."' AND '".$end."' AND (RC.parent_id is NULL OR  RC.parent_id= 0) AND B.payment_status='Completed' ".$where." GROUP BY RC.id ORDER BY SUM(BI.quantity) DESC";
        /*echo $myQuery;die;*/
        $t = $this->db->query($myQuery);

   /*$this->db->select("recipe_categories.id AS cate_id,'recipe_categories.name' as category, 'split_order'")
        ->join('recipe', 'recipe.category_id = recipe_categories.id')
        ->join('bil_items', 'bil_items.recipe_id = recipe.id')
        ->join('bils', 'bils.id = bil_items.bil_id')
        ->where('recipe_categories.parent_id', NULL)
        ->where('bils.payment_status', 'Completed')
        ->or_where('recipe_categories.parent_id',0);
        if($warehouse_id != 0){
            $this->db->where('bils.warehouse_id', $warehouse_id);    
        }*/
       /* $this->db->group_by('recipe_categories.id');
        $this->db->order_by('SUM(' . $this->db->dbprefix('bil_items') . '.quantity)', 'DESC');*/
       // $this->db->stop_cache();
        $total = $t->num_rows();   

        //if($limit!=0) $this->db->limit($limit,$offset);
        /*$t = $this->db->get('recipe_categories'); */     
         $this->db->flush_cache();
//var_dump($t);die;
//print_r($t->result());die;
        if ($t->num_rows() > 0) {
            
            foreach ($t->result() as $row) {
                    /*$this->db->select("recipe_categories.id AS sub_id,recipe_categories.name AS sub_category,bils.total_tax, 'order'")
                    ->join('recipe', 'recipe.subcategory_id = recipe_categories.id')
                    ->join('bil_items', 'bil_items.recipe_id = recipe.id')
                    ->join('bils', 'bils.id = bil_items.bil_id')
                    ->where('bils.payment_status', 'Completed')
                    ->where('recipe.category_id', $row->cate_id);
                    if($warehouse_id != 0){
                        $this->db->where('bils.warehouse_id', $warehouse_id);
                    }
                    $this->db->group_by('recipe.subcategory_id');
                    $this->db->order_by('SUM(' . $this->db->dbprefix('bil_items') . '.quantity)', 'DESC');
                    $s = $this->db->get('recipe_categories');
                    */
            $SubQuery = "SELECT RC.id AS sub_id,RC.name AS sub_category,'order'
            FROM " . $this->db->dbprefix('recipe_categories') . " RC
            JOIN " . $this->db->dbprefix('recipe') . " R ON R.subcategory_id = RC.id
            LEFT JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.recipe_id = R.id
            JOIN " . $this->db->dbprefix('bils') . " B ON B.id = BI.bil_id
            WHERE DATE(B.date) BETWEEN '".$start."' AND '".$end."' AND R.category_id=".$row->cate_id."  AND B.payment_status='Completed' ".$where." GROUP BY RC.id ORDER BY SUM(BI.quantity) DESC";  
            
            $s = $this->db->query($SubQuery);

                if ($s->num_rows() > 0) {
                        
                        foreach ($s->result() as $sow) {
                            
                            $RepQuery = "SELECT R.name,SUM(BI.item_discount) AS item_discount,SUM(BI.off_discount) AS off_discount,SUM(BI.input_discount) AS input_discount,SUM(BI.quantity) AS quantity,SUM(BI.tax) AS tax,SUM(BI.subtotal-CASE WHEN (BI.tax_type= 0) THEN BI.tax ELSE 0 END) as subtotal
                                FROM " . $this->db->dbprefix('bils') . " B
                                JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.bil_id = B.id
                                LEFT JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
                                
                                WHERE DATE(B.date) BETWEEN '".$start."' AND '".$end."' AND
                                R.subcategory_id =".$sow->sub_id." AND B.payment_status='Completed'".$where."
                                GROUP BY BI.recipe_name ORDER BY SUM(BI.quantity) DESC";
                                 // echo $RepQuery;die;
                                $o = $this->db->query($RepQuery);

                                /*$this->db->select('recipe.name,SUM(' . $this->db->dbprefix('bil_items') . '.item_discount) as item_discount,SUM(' . $this->db->dbprefix('bil_items') . '.off_discount) as off_discount,SUM(' . $this->db->dbprefix('bil_items') . '.input_discount) as input_discount,SUM(' . $this->db->dbprefix('bil_items') . '.tax) as tax,SUM(' . $this->db->dbprefix('bil_items') . '.quantity) as quantity,SUM(' . $this->db->dbprefix('bil_items') . '.subtotal) as subtotal')
                                    ->join('recipe', 'recipe.id = bil_items.recipe_id')
                                    ->join('bils', 'bils.id = bil_items.bil_id')
                                    ->where('DATE(date) >=', $start)
                                    ->where('DATE(date) <=', $end)
                                    ->where('recipe.subcategory_id', $sow->sub_id);
                                    $this->db->group_by('recipe.id');
                                    $this->db->order_by('SUM(' . $this->db->dbprefix('bil_items') . '.quantity)', 'DESC');
                                    $o = $this->db->get('bil_items');   */                            
                                $split[$row->cate_id][] = $sow;
                                if ($o->num_rows() > 0) {                                    
                                    foreach($o->result() as $oow){
                                        $order[$sow->sub_id][] = $oow;
                                    }
                                }
                                $sow->order = $order[$sow->sub_id];                   
                        }                    
                    $row->split_order = $split[$row->cate_id];
                }else{
                    $row->split_order = array();
                }                
                $data[] = $row;

            }        
              
           return array('data'=>$data,'total'=>$total);
        }        
        return FALSE;
    }  
public function getNonPopularReports($start,$end,$warehouse_id,$limit,$offset,$report_view_access,$report_show){
    $where ='';
        if($warehouse_id != 0)
        {
            $where = "AND B.warehouse_id =".$warehouse_id."";
        }
        if($report_view_access != 1){
             $where .= " AND B.table_whitelisted = ".$report_show." ";
         }

        $this->db->start_cache();
        $myQuery = "SELECT RC.id AS cate_id,RC.name AS category,'split_order'
        FROM " . $this->db->dbprefix('recipe_categories') . " RC
       LEFT  JOIN " . $this->db->dbprefix('recipe') . " R ON R.category_id = RC.id
        JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.recipe_id = R.id
        JOIN " . $this->db->dbprefix('bils') . " B ON B.id = BI.bil_id
        WHERE DATE(B.date) BETWEEN '".$start."' AND '".$end."' AND (RC.parent_id is NULL OR  RC.parent_id= 0) AND B.payment_status='Completed' ".$where." GROUP BY RC.id ORDER BY SUM(BI.quantity) ASC";
        
        $t = $this->db->query($myQuery);
   
        $total = $t->num_rows();   
          
         $this->db->flush_cache();

        if ($t->num_rows() > 0) {
            
            foreach ($t->result() as $row) {
                  
            $SubQuery = "SELECT RC.id AS sub_id,RC.name AS sub_category,'order'
            FROM " . $this->db->dbprefix('recipe_categories') . " RC
            JOIN " . $this->db->dbprefix('recipe') . " R ON R.subcategory_id = RC.id
            LEFT JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.recipe_id = R.id
            JOIN " . $this->db->dbprefix('bils') . " B ON B.id = BI.bil_id
            WHERE DATE(B.date) BETWEEN '".$start."' AND '".$end."' AND R.category_id=".$row->cate_id."  AND B.payment_status='Completed' ".$where." GROUP BY RC.id ORDER BY SUM(BI.quantity) ASC";  
            
            $s = $this->db->query($SubQuery);

                if ($s->num_rows() > 0) {
                        
                        foreach ($s->result() as $sow) {
                            
                            $RepQuery = "SELECT R.name,SUM(BI.item_discount) AS item_discount,SUM(BI.off_discount) AS off_discount,SUM(BI.input_discount) AS input_discount,SUM(BI.quantity) AS quantity,SUM(BI.tax) AS tax,SUM(BI.subtotal-CASE WHEN (BI.tax_type= 0) THEN BI.tax ELSE 0 END) as subtotal
                                FROM " . $this->db->dbprefix('bils') . " B
                                JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.bil_id = B.id
                                LEFT JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id                                
                                WHERE DATE(B.date) BETWEEN '".$start."' AND '".$end."' AND
                                R.subcategory_id =".$sow->sub_id." AND B.payment_status='Completed'".$where."
                                GROUP BY BI.recipe_name ORDER BY SUM(BI.quantity) ASC";                                
                                $o = $this->db->query($RepQuery);

                                $split[$row->cate_id][] = $sow;
                                if ($o->num_rows() > 0) {                                    
                                    foreach($o->result() as $oow){
                                        $order[$sow->sub_id][] = $oow;
                                    }
                                }
                                $sow->order = $order[$sow->sub_id];                   
                        }                    
                    $row->split_order = $split[$row->cate_id];
                }else{
                    $row->split_order = array();
                }                
                $data[] = $row;
            }        
           return array('data'=>$data,'total'=>$total);
        }        
        return FALSE;
    }    
public function getNonPopularReports_($start,$end,$warehouse_id,$limit,$offset)
{
    $this->db->start_cache();
$this->db->select("recipe_categories.id AS cate_id,recipe_categories.name as category,SUM(" . $this->db->dbprefix('bils') . ".grand_total) AS grand_total,SUM(" . $this->db->dbprefix('bils') . ".round_total) AS round_total, 'split_order'")
        ->join('recipe', 'recipe.category_id = recipe_categories.id')
        ->join('bil_items', 'bil_items.recipe_id = recipe.id')
        ->join('bils', 'bils.id = bil_items.bil_id')
        ->where('bils.payment_status', 'Completed')
        ->where('recipe_categories.parent_id', NULL)
        ->or_where('recipe_categories.parent_id',0);
         if($warehouse_id != 0){
            $this->db->where('bils.warehouse_id', $warehouse_id);    
        }
        if(!$this->Owner && !$this->Admin){
                $this->db->where('bils.table_whitelisted', 0); 
            }
        $this->db->group_by('recipe_categories.id');
        $this->db->order_by('SUM(' . $this->db->dbprefix('bil_items') . '.quantity)', 'ASC');
        $this->db->stop_cache();
        $total = $this->db->get('recipe_categories');      
        if($limit!=0) $this->db->limit($limit,$offset);
        $t = $this->db->get('recipe_categories');      
        $this->db->flush_cache();
        if ($t->num_rows() > 0) {
            
            foreach ($t->result() as $row) {
                 $this->db->select("recipe_categories.id AS sub_id,recipe_categories.name AS sub_category,bils.total_tax, 'order'")
                    ->join('recipe', 'recipe.subcategory_id = recipe_categories.id')
                    ->join('bil_items', 'bil_items.recipe_id = recipe.id')
                    ->join('bils', 'bils.id = bil_items.bil_id')
                    ->where('bils.payment_status', 'Completed')
                    ->where('recipe.category_id', $row->cate_id);
                     if(!$this->Owner && !$this->Admin){
                $this->db->where('bils.table_whitelisted', 0); 
            }
                    $this->db->group_by('recipe.subcategory_id');
                    $this->db->order_by('SUM(' . $this->db->dbprefix('bil_items') . '.quantity)', 'ASC');
                    $s = $this->db->get('recipe_categories');
                if ($s->num_rows() > 0) {
                        
                        foreach ($s->result() as $sow) {
                                $where='';if(!$this->Owner && !$this->Admin){
            $where .= " AND B.table_whitelisted =0";
        }
        
                                $myQuery = "SELECT R.name,SUM(BI.item_discount) AS item_discount,SUM(BI.off_discount) AS off_discount,SUM(BI.input_discount) AS input_discount,SUM(BI.quantity) AS quantity,SUM(BI.tax) AS tax,SUM(BI.subtotal-CASE WHEN (BI.tax_type= 0) THEN BI.tax ELSE 0 END) as subtotal
                                FROM " . $this->db->dbprefix('bil_items') . " BI
                                JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
                                JOIN " . $this->db->dbprefix('bils') . " B ON B.id = BI.bil_id
                                WHERE DATE(B.date) BETWEEN '".$start."' AND '".$end."' AND
                                R.subcategory_id =".$sow->sub_id." AND B.payment_status='Completed'".$where."
                                GROUP BY R.id ORDER BY SUM(BI.quantity) ASC";
                                
                                $o = $this->db->query($myQuery);
                                               
                                $split[$row->cate_id][] = $sow;
                                if ($o->num_rows() > 0) {                                    
                                    foreach($o->result() as $oow){
                                        $order[$sow->sub_id][] = $oow;
                                    }
                                }
                                $sow->order = $order[$sow->sub_id];                   
                        }                    
                    $row->split_order = $split[$row->cate_id];
                }else{
                    $row->split_order = array();
                }                
                $data[] = $row;

            }            
            return array('data'=>$data,'total'=>$total->num_rows());
        }        
        return FALSE;
    }  
public function getRoundamount($start,$end,$warehouse_id)
    {
        $where ='';
        if($warehouse_id != 0)
        {
            $where = "AND P.warehouse_id =".$warehouse_id."";
        }

        $round = "SELECT SUM(P.grand_total - round_total) AS round
        FROM " . $this->db->dbprefix('bils') . " AS P
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
             P.payment_status ='Completed' ".$where."";
            /*echo $cover; die;*/
        $q = $this->db->query($round);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }    
public function getCoverAnalysisReport($start,$end,$warehouse_id,$limit,$offset,$report_view_access,$report_show)
    {
        $where ='';
        if($warehouse_id != 0)
        {
            $where = "AND P.warehouse_id =".$warehouse_id."";
        }
        if($report_view_access != 1){
             $where .= " AND P.table_whitelisted = ".$report_show." ";
         }                 

        $cover = "SELECT W.name AS warehouse,SUM(total) as total,SUM(total-total_discount+CASE WHEN (tax_type= 1) THEN total_tax ELSE 0 END) grand_total,SUM(P.round_total) AS round_total,COUNT(P.id) AS tot_bills,SUM(total_discount) AS discount
        FROM " . $this->db->dbprefix('bils') . " AS P
        LEFT JOIN  " . $this->db->dbprefix('warehouses') . " AS W ON W.id=P.warehouse_id
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
             P.payment_status ='Completed' ".$where."";  
             
        $limit_q = " limit $offset,$limit";
        $t = $this->db->query($cover);
        // if($limit!=0) $cover .=$limit_q;
        $q = $this->db->query($cover);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return array('data'=>$data,'total'=>$t->num_rows());
        }
        return FALSE;
    }  
public function getBill_no($start,$end,$warehouse_id,$report_view_access,$report_show)
    {   
        $where ='';
        if($warehouse_id != 0)
        {
            $where = "AND P.warehouse_id =".$warehouse_id."";
        }
        if($report_view_access != 1){
             $where .= " AND P.table_whitelisted = ".$report_show." ";
         }

       $BillNo = "SELECT P.id,P.bill_number
         FROM " . $this->db->dbprefix('bils') . " AS P
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
             P.payment_status ='Completed' ".$where." ";

        $q = $this->db->query($BillNo);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }     

public function getBillDetailsReport($start,$end,$bill_no,$warehouse_id,$limit,$offset,$report_view_access,$report_show)
    {  
        $where1 ='';
        if($warehouse_id != 0)
        {
            $where1 = "AND P.warehouse_id =".$warehouse_id."";
        }
        if($report_view_access != 1){
             $where .= " AND P.table_whitelisted = ".$report_show." ";
         }

        /*if(!$this->Owner && !$this->Admin){
            $where1 .= " AND P.table_whitelisted =0";
        }
        if(($this->Owner || $this->Admin) && $table_whitelisted!='all'){
            $where1 .= " AND P.table_whitelisted =".$table_whitelisted;
        }*/
        if($bill_no)
        {
            $where = "AND P.id =".$bill_no."";
        }
        else{
            $where = "";
        }
        
         $bill = "SELECT P.id,SUM(DISTINCT P.total-P.total_discount+CASE WHEN (P.tax_type= 1) THEN P.total_tax ELSE 0 END) as grand_total,P.round_total 
            FROM ". $this->db->dbprefix('bils') ." AS P
             JOIN ". $this->db->dbprefix('users') ." AS U ON P.created_by = U.id
             JOIN ". $this->db->dbprefix('payments') ." AS PM ON PM.bill_id = P.id
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
             P.payment_status ='Completed' ".$where." ".$where1."
            GROUP BY P.id ORDER BY P.id ASC";
            
            $limit_q = " limit $offset,$limit";
            $t = $this->db->query($bill);
            if($limit!=0) $bill .=$limit_q;
            $u = $this->db->query($bill);
            
        if ($u->num_rows() > 0) {
            foreach (($u->result()) as $uow) {

            $Billdetails = "SELECT K.id AS kitchenno,DATE_FORMAT(O.date, '%d-%m-%Y') AS kot_date,T.name AS table_name, U.username,OU.username AS steward,R.name AS item,BI.quantity,P.bill_number, BI.subtotal AS Bill_amt,DATE_FORMAT(P.date, '%H:%i') as bill_time,BI.item_discount,BI.off_discount,BI.input_discount,BI.tax,TY.name AS order_type,P.grand_total,P.round_total,P.id as Bill_id,BI.tax_type,P.table_whitelisted,W.name branch
                    FROM ".$this->db->dbprefix('bils')." AS P
                    JOIN ". $this->db->dbprefix('sales') ." AS S ON S.id = P.sales_id
                    JOIN ". $this->db->dbprefix('orders') ." AS O ON O.split_id = S.sales_split_id
                    LEFT JOIN ". $this->db->dbprefix('sales_type') ." AS TY ON TY.id = O.order_type
                    JOIN ". $this->db->dbprefix('bil_items') ." AS BI ON BI.bil_id = P.id
                    JOIN ". $this->db->dbprefix('recipe') ." AS R ON BI.recipe_id = R.id
                    LEFT JOIN ". $this->db->dbprefix('restaurant_tables') ." AS T ON T.id = O.table_id
                    JOIN ". $this->db->dbprefix('kitchen_orders') ." AS K ON K.sale_id = O.id
                    JOIN ". $this->db->dbprefix('users') ." AS U ON U.id = P.created_by
                    JOIN ". $this->db->dbprefix('users') ." AS OU ON OU.id = O.created_by
                    LEFT JOIN " . $this->db->dbprefix('warehouses') . " W ON W.id = P.warehouse_id
                    WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
                    P.payment_status ='Completed' AND P.id='".$uow->id."' GROUP BY BI.id ";                    

                    $q = $this->db->query($Billdetails);
                    if ($q->num_rows() > 0) {
                        foreach (($q->result()) as $row) {
                            $user[$uow->id][] = $row;
                        }
                        $uow->user = $user[$uow->id];
                        $data[] = $uow;
                    }
                }
            return array('data'=>$data,'total'=>$t->num_rows());
        }
        return FALSE;
    }  
public function getStockVariance($start,$product_id,$warehouse_id,$limit,$offset)
    {   
        $where ='';
        $prd_where ='';
        $prd_where1 ='';
        if($warehouse_id != 0)
        {
            $where = "AND P.warehouse_id =".$warehouse_id."";
        }
        if($product_id != 0)
        {
            $prd_where = "AND PR.id =".$product_id."";
        }
        if($product_id != 0)
        {
            $prd_where1 = "WHERE PR.id =".$product_id."";
        }

       /* $myQuery = "SELECT DATE_FORMAT(P.date, '%d-%m-%Y') AS bill_date,PR.id,PR.name,SU.name AS saleunit,PU.name AS productunit,(CASE
        WHEN SU.name = 'Gram' and PU.name = 'Kg'  THEN SUM(RP.max_quantity)/1000
        WHEN SU.name = 'Kg' and PU.name = 'Kg' THEN SUM(RP.max_quantity)
        WHEN SU.name = 'Millilitre' and PU.name = 'Litre'  THEN SUM(RP.max_quantity)/1000
        WHEN SU.name = 'Litre' and PU.name = 'Litre' THEN SUM(RP.max_quantity)
        WHEN SU.name = 'Pieces' and PU.name = 'Package' THEN SUM(RP.max_quantity)/12
        WHEN SU.name = 'Package' and PU.name = 'Package' THEN SUM(RP.max_quantity)        
        ELSE 0
        END) AS soldQty,PI.given_quantity
        FROM ". $this->db->dbprefix('bils') ." P
        LEFT JOIN ". $this->db->dbprefix('bil_items') ." BI ON BI.bil_id = P.id 
        LEFT JOIN ". $this->db->dbprefix('recipe_products') ." RP ON RP.recipe_id =  BI.recipe_id 
        LEFT JOIN ". $this->db->dbprefix('products') ." PR ON PR.id = RP.product_id
        LEFT JOIN ". $this->db->dbprefix('production_items') ." AS PI ON PI.product_id = PR.id 
        LEFT JOIN ". $this->db->dbprefix('production') ." AS PN  ON PN.id = PI.production_id
        LEFT JOIN ". $this->db->dbprefix('units') ." SU ON SU.id = RP.units_id
        LEFT JOIN ". $this->db->dbprefix('units') ." PU ON PU.id = PR.unit
        WHERE P.payment_status ='Completed' AND DATE(P.date) = '".$start."' ".$where." ".$prd_where."";*/
$originalDate = $start;

$newDate = date("d-m-Y", strtotime($originalDate));
        $myQuery = "SELECT '".$newDate."'

 AS bill_date,PR.id,PR.name,SU.name AS saleunit,PU.name AS productunit,(CASE
        WHEN SU.name = 'Gram' and PU.name = 'Kg'  AND P.payment_status ='Completed'  ".$prd_where." AND DATE(P.date) = '".$start."'   THEN SUM(RP.max_quantity)/1000
        WHEN SU.name = 'Kg' and PU.name = 'Kg' AND P.payment_status ='Completed' ".$prd_where." AND DATE(P.date) = '".$start."'  THEN SUM(RP.max_quantity)
        WHEN SU.name = 'Millilitre' and PU.name = 'Litre'  AND P.payment_status ='Completed' ".$prd_where." AND DATE(P.date) = '".$start."'  THEN SUM(RP.max_quantity)/1000
        WHEN SU.name = 'Litre' and PU.name = 'Litre' AND P.payment_status ='Completed'  ".$prd_where." AND DATE(P.date) = '".$start."' THEN SUM(RP.max_quantity)
        WHEN SU.name = 'Pieces' and PU.name = 'Package' AND P.payment_status ='Completed' ".$prd_where."  AND DATE(P.date) = '".$start."' THEN SUM(RP.max_quantity)/12
        WHEN SU.name = 'Package' and PU.name = 'Package' AND P.payment_status ='Completed' ".$prd_where." AND DATE(P.date) = '".$start."' THEN SUM(RP.max_quantity)        
        ELSE 0
        END) AS soldQty,(COALESCE( PI.given_quantity, 0 ) ) AS given_quantity
        FROM srampos_products PR
        LEFT JOIN srampos_recipe_products RP ON RP.product_id = PR.id  
        LEFT JOIN srampos_bil_items BI ON  BI.recipe_id  = RP.recipe_id
        LEFT JOIN srampos_bils P ON P.id  = BI.bil_id
        LEFT JOIN srampos_production_items AS PI ON PI.product_id = PR.id 
        LEFT JOIN srampos_production AS PN  ON PN.id = PI.production_id
        LEFT JOIN srampos_units SU ON SU.id = RP.units_id
        LEFT JOIN srampos_units PU ON PU.id = PR.unit ".$prd_where1."
        GROUP by PR.name";
        
        $t = $this->db->query($myQuery);
        $limit_q = " limit $offset,$limit";
        if($limit!=0) $myQuery .=$limit_q;/*echo $myQuery;die;*/
        $q = $this->db->query($myQuery);

        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return array('data'=>$data,'total'=>$t->num_rows());
        }
        return FALSE;
    }
    public function getHourlysummaryReport($start,$end,$warehouse_id,$time_range,$limit,$offset,$report_view_access,$report_show){  

        $where ='';
        if($warehouse_id != 0)
        {
            $where = "AND P.warehouse_id =".$warehouse_id."";
        }
        if($report_view_access != 1)
         {
             $where .= " AND P.table_whitelisted = ".$report_show." ";
         }
        //->where('date BETWEEN ' . $start . ' and ' . $end);
        //count(BI.quantity) qty,SUM(BI.unit_price) val,DATE_FORMAT(p.date,'%H') time,GROUP_CONCAT(DATE_FORMAT(p.date,'%H'),'-',BI.quantity) qtyval
            $myQuery = "SELECT R.id recipeid,R.name,count(BI.quantity) qty,(BI.unit_price) AS val
            FROM " . $this->db->dbprefix('bils') . " P
            JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.bil_id = P.id
            JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
            JOIN " . $this->db->dbprefix('recipe_categories') . " RC ON RC.id = R.category_id
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND
            P.payment_status ='Completed'  ".$where."
            GROUP BY R.id ORDER BY R.id,RC.id ASC ";
            /*echo $myQuery;die;*/
            $limit_q = " limit $offset,$limit";
            $t = $this->db->query($myQuery);
            if($limit!=0) $myQuery .=$limit_q;
            $q = $this->db->query($myQuery);
            
            if ($q->num_rows() > 0) {
                $result = $q->result_array();
                $timeArray = array();$first = true;
                $to='';
                $time_start = 0;$time_end = 23;
                for($i=$time_start;$i<$time_end;$i++){
                    if($first){
                        $first = false;
                        $frm = $i;
                        $to = $i+$time_range;
                    }else{
                       $frm = ($to);//$k+$time_range;
                       $to = $frm+$time_range;//$k+$time_range+$time_range;
                      
                    } 
                    if($to > $time_end){ 
                        $to = $time_end;
                    }
                    $frmTo = $frm.'-'.$to;
                    $timeArray[$frm] = $frmTo;
                    if($frm==$time_end || $to==$time_end || ($frm < $time_end && $to > $time_end)){  break;}
                }
               $conditions['start'] = $start;
               $conditions['end'] = $end;
               $conditions['where'] = $where;
               $hourlsale = $this->houlrTableHtml($result,$timeArray,$time_range,$conditions);
               return array('data'=>$hourlsale,'total'=>$t->num_rows());
            }
        
        return FALSE;
    }
    public function houlrTableHtml_bkk($data,$timerange,$time_range,$conditions){
     
        $tableHead = '<thead>';
        $tableHead .= '<tr><th>Item</th>';
        $tableHead .= '<th>Qty/Val</th>';
        $first=true;
        $to = '';
        foreach($timerange as $k => $range){
            $tableHead .= '<th>'.$range.'</th>';
        }
        $tableHead .= '</tr></thead>';
       
        $tableBody = '<tbody>';
        $timerangeBody = $timerange;
        foreach($data as $key => $row){ 
            $timerangeBody = $timerange;
            $tableBody .= '<tr>';
            $tableBody .= '<td>'.$row['name'].'</td>';
            $tableBody .= '<td><span>Qty</span><br /><span>val</span></td>';
            $row['recipeid'] = 206;
            $qtyval = $this->getRecipeQty_val($row['recipeid'],$conditions['start'],$conditions['end'],$conditions['where']);
//print_r($qtyval); continue;
            
            $cnt = 1;
            $qtyval_1 = array();
            $itemTotal_price = '';
            //print_r($qtyval);//exit;
            foreach($qtyval as $k => $k_val){                
                foreach($timerangeBody as $t => $t_val){
                    $tarray = explode('-',$t_val);
                    
                    if($tarray[0] <= $k_val['time'] && $tarray[1] > $k_val['time']){
                      
                      if(isset($qtyval_1[$t_val])){ 
                        $cnt = $cnt+$k_val['qty'];
                        $qtyval_1[$t_val]['qty'] = $cnt;
                        $itemTotal_price = $itemTotal_price + $k_val['more_qty_total'];
                        $qtyval_1[$t_val]['item_total_price'] = $itemTotal_price;
                          
                      }else{ 
                        $cnt = $k_val['qty'];
                        $qtyval_1[$t_val]['qty'] = $cnt;//exit;
                        $itemTotal_price = $k_val['more_qty_total'];
                        $qtyval_1[$t_val]['item_total_price'] = $itemTotal_price;
                      }
                      $qtyval_1[$t_val]['price'] = 'price';
                      $qtyval_1[$t_val]['time'] = $k_val['time'];
                      /*echo $k_val['tax_type'];die;*/
                      if($k_val['tax_type'] != 0)
                      { 
                        $final_val = $k_val['subtotal']-$k_val['item_discount']-$k_val['off_discount']-$k_val['input_discount']+$k_val['tax'];
                      }
                      else{
                        $final_val = $k_val['subtotal']-$k_val['item_discount']-$k_val['off_discount']-$k_val['input_discount'];
                      }
                      /*$k_val['val'] = $final_val;*/
                      $qtyval_1[$t_val]['val'] = $k_val['val'];
                    }
                    print_R($qtyval_1);
                }
            }exit;
          foreach($timerangeBody as $t => $t_val){
             foreach($qtyval_1 as $k => $k_val){ 
                    if($k == $t_val){
                            //$timerangeBody[$t] =   '<td><span class="recipe-qty">'.$k_val['qty'].'</span></br><span  class="recipe-val">'.($k_val['val']*$k_val['qty']).'</span></td>';
                            $timerangeBody[$t] =   '<td><span class="recipe-qty">'.$k_val['qty'].'</span></br><span  class="recipe-val">'.$k_val['item_total_price'].'</span></td>';
                    }
                }
            }
            foreach($timerangeBody as $td_key => $td_val){
                $tdvalarray = explode('-',$td_val);
                if(!is_numeric($tdvalarray[0])){
                    $tableBody .= $td_val;
                }else{
                    $tableBody .= '<td class="empty-values" style="text-align: center;"><span>-</span></td>';
                }
            }
            $tableBody .= '</tr>';
        }
        $tableBody .= '</tbody>';
        $table = $tableHead.$tableBody;
        return $table;
    }
    function getRecipeQty_vall($id,$start,$end,$where){
          $myQuery = "SELECT R.id,R.name,(BI.quantity) AS qty,BI.unit_price AS val,BI.subtotal,BI.item_discount,BI.off_discount,BI.input_discount,BI.tax_type,BI.tax,DATE_FORMAT(P.date,'%H') time,(BI.subtotal-BI.item_discount-BI.off_discount-BI.input_discount+CASE WHEN (BI.tax_type = 1) THEN BI.tax ELSE 0 END) AS val12,SUM(BI.subtotal-BI.item_discount-BI.off_discount-BI.input_discount+CASE WHEN (BI.tax_type = 1) THEN BI.tax ELSE 0 END) as val
            FROM " . $this->db->dbprefix('bils') . " P
            JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.bil_id = P.id
            JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
            JOIN " . $this->db->dbprefix('recipe_categories') . " RC ON RC.id = R.category_id
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND BI.recipe_id = $id AND
            P.payment_status ='Completed'  ".$where." GROUP BY R.id
             ORDER BY RC.id ASC";
              
             /*echo $myQuery;die;*/
            $q = $this->db->query($myQuery);
            if ($q->num_rows() > 0) {
                return $q->result_array();
            }
    }
    public function houlrTableHtml($data,$timerange,$time_range,$conditions){
     
        $tableHead = '<thead>';
        $tableHead .= '<tr><th>Item</th>';
        $tableHead .= '<th>Qty/Val</th>';
        $first=true;
        $to = '';
        foreach($timerange as $k => $range){
            $tableHead .= '<th>'.$range.'</th>';
        }
        $tableHead .= '</tr></thead>';
       
        $tableBody = '<tbody>';
        $timerangeBody = $timerange;
        foreach($data as $key => $row){ 
            $timerangeBody = $timerange;
            $tableBody .= '<tr>';
            $tableBody .= '<td>'.$row['name'].'</td>';
            $tableBody .= '<td><span>Qty</span><br /><span>val</span></td>';
            foreach($timerangeBody as $t => $t_val){
                    $timebetween = explode('-',$t_val);
                    $items[$t_val] = $this->getRecipeQty_val($row['recipeid'],$conditions['start'],$conditions['end'],$conditions['where'],$timebetween);
                    if($items[$t_val]){
                        $tableBody .= '<td><span class="recipe-qty">'.$this->sma->formatDecimal($items[$t_val]['qty']).'</span></br><span  class="recipe-val">'.$this->sma->formatDecimal($items[$t_val]['more_qty_total']).'</span></td>';
                    }else{
                        $tableBody .= '<td class="empty-values" style="text-align: center;"><span>-</span></td>';
                    }
                    
            }

            $tableBody .= '</tr>';//echo $tableBody;exit;
        }
        $tableBody .= '</tbody>';
        $table = $tableHead.$tableBody;
        return $table;
    }
     function getRecipeQty_val($id,$start,$end,$where,$timebetween){//print_R($timebetween);
        //$id = 167;
          $myQuery = "SELECT P.bill_number,R.id,R.name,SUM(BI.quantity) qty,BI.unit_price val,BI.subtotal,BI.item_discount,BI.off_discount,BI.input_discount,DATE_FORMAT(P.date,'%H') time,BI.tax,BI.tax_type,(BI.subtotal-BI.item_discount-BI.off_discount-BI.input_discount+CASE WHEN (BI.tax_type = 1) THEN BI.tax ELSE 0 END) AS one_qty_total,SUM(BI.subtotal-BI.item_discount-BI.off_discount-BI.input_discount+CASE WHEN (BI.tax_type = 1) THEN BI.tax ELSE 0 END) as more_qty_total

            FROM " . $this->db->dbprefix('bils') . " P
            JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.bil_id = P.id
            JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
            JOIN " . $this->db->dbprefix('recipe_categories') . " RC ON RC.id = R.category_id
            WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND BI.recipe_id = ".$id." AND DATE_FORMAT(P.date,'%H') >= ".$timebetween[0]." AND DATE_FORMAT(P.date,'%H') < ".$timebetween[1]." AND
            P.payment_status ='Completed'  ".$where." GROUP BY R.id
             ORDER BY RC.id ASC";
              
             /*echo $myQuery;die;*/
            $q = $this->db->query($myQuery);
           // print_r($q->result_array());exit;
           //echo $this->db->error();
            if ($q->num_rows() > 0) {
                return $q->row_array();
            }
            return false;
    }
public function getOrderTimeReport($start,$end,$warehouse_id,$limit,$offset,$report_view_access,$report_show)
    {   $default_p_time = $this->Settings->default_preparation_time;
        $where ='';

         if($warehouse_id != 0)
        {
            $where = "AND B.warehouse_id =".$warehouse_id."";
        }
        if($report_view_access != 1){
            $where .= " AND O.table_whitelisted =".$report_show."";
        }
        $Ordertime = "SELECT O.reference_no,O.id,R.name AS recipe_name,T.name AS table_name,TIMESTAMPDIFF(SECOND,OI.time_started,OI.time_end) prepared_time,OI.time_started,OI.time_end,U.username,CASE WHEN R.preparation_time!=0 THEN R.preparation_time ELSE ".$default_p_time."  END preparation_time,W.name branch
        FROM " . $this->db->dbprefix('orders') . " O
        JOIN " . $this->db->dbprefix('order_items') . " OI ON OI.sale_id = O.id
        JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = OI.recipe_id 
        JOIN " . $this->db->dbprefix('bil_items') . " BI ON R.id = BI.recipe_id  
        JOIN " . $this->db->dbprefix('bils') . " B ON B.id = BI.bil_id and B.sales_id=OI.sale_id
        JOIN " . $this->db->dbprefix('users') . " U ON U.id = O.created_by 
        JOIN " . $this->db->dbprefix('restaurant_tables') . " T ON T.id = O.table_id
        LEFT JOIN " . $this->db->dbprefix('warehouses') . " W ON W.id = O.warehouse_id
            WHERE DATE(O.date) BETWEEN '".$start."' AND '".$end."' AND B.payment_status ='Completed'".$where." group by O.reference_no,OI.recipe_id";
            
        $t = $this->db->query($Ordertime);
        if($limit!=0) $Ordertime .= " limit $offset,$limit";
        $q = $this->db->query($Ordertime);
        //echo $Ordertime;exit;
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
               // echo $row->time_started.'==='.$row->time_end;
               // echo $row->preparation_time.'==='.$row->prepared_time;exit;
                $row->default_preparation_time = ($row->preparation_time!=0)?round(($row->preparation_time/60),1):null;
                $row->preparedTime = round(($row->prepared_time/60),1);
                $row->timediff = round(($row->preparedTime-$row->default_preparation_time),1);
                $row->timediff = (strpos($row->timediff, '-')===false)?'After '.trim($row->timediff,'-'):'Before '.trim($row->timediff,'-');
                
                $data[] = $row;
            }
            return array('data'=>$data,'total'=>$t->num_rows());
        }
        return FALSE;
    }
    //public function postpaid_bills_report($start,$end,$warehouse_id,$limit,$offset,$dayrange)
    //{   
    //    $where ='';
    //     if($warehouse_id != 0)
    //    {
    //        $where = "AND P.warehouse_id =".$warehouse_id."";
    //    }
    //   $this->db->start_cache();
    //    
    //    $this->db
    //            ->select('P.*,C.name customer_name,B.date bill_date,
    //                     CASE
    //                        WHEN DATEDIFF(P.paid_on,P.due_date) Is Null  THEN "-"
    //                        WHEN DATEDIFF(P.paid_on,P.due_date) > 0 THEN DATEDIFF(P.paid_on,P.due_date)
    //                        WHEN DATEDIFF(P.paid_on,P.due_date) < 0 THEN 0
    //                     END as exceeded_days')
    //            ->from('companies_postpaid_bills P')
    //            ->join('companies C','C.id=P.company_id')
    //            ->join('bils B','B.id=P.bill_id')
    //            ->where('DATE(B.date)>=',$start)
    //            ->where('DATE(B.date)<=',$end);
    //            if($dayrange!=''){
    //                $this->db->where('DATEDIFF(P.paid_on,P.due_date)',$dayrange);
    //            }
    //    $this->db->stop_cache();
    //    
    //    $total = $this->db->get();
    //    if($limit!=0) $this->db->limit($limit,$offset);
    //    $q = $this->db->get();
    //    $this->db->flush_cache();
    //    //print_R($q->result());exit;
    //    //print_R($this->db->error());//exit;
    //    if ($q->num_rows() > 0) {
    //        foreach (($q->result()) as $row) {
    //            $data[] = $row;
    //        }
    //        return array('data'=>$data,'total'=>$total->num_rows());
    //    }
    //    return FALSE;
    //}
    public function postpaid_bills_report($warehouse_id,$limit,$offset,$customer_id)
    {   
        $where ='';
         if($warehouse_id != 0)
        {
            $where = "AND P.warehouse_id =".$warehouse_id."";
        }
       $this->db->start_cache();
        
        $this->db
                ->select('C.name customer_name,C.id customer_id,SUM(amount_payable) amount')                         
                ->from('companies_postpaid_bills P')
                ->join('companies C','C.id=P.company_id')
                ->join('bils B','B.id=P.bill_id');
                if($customer_id!=''){
                    $this->db->where('P.company_id',$customer_id);
                }
                $this->db->group_by('P.company_id');
               
        $this->db->stop_cache();
        
        $total = $this->db->get();
        if($limit!=0) $this->db->limit($limit,$offset);
        $q = $this->db->get();
        $this->db->flush_cache();
        
        $this->db
                ->select('C.name customer_name,C.id customer_id,SUM(amount_payable) amount')                         
                ->from('companies_postpaid_bills P')
                ->join('companies C','C.id=P.company_id')
                ->join('bils B','B.id=P.bill_id');
                $this->db->group_by('P.company_id');
        $c = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return array('data'=>$data,'customer_details'=>$c->result_array(),'total'=>$total->num_rows());
        }
        return FALSE;
    }
    function customer_postpaid_bills($id,$limit,$offset){
        $this->db->start_cache();
        $this->db
                ->select('P.*,C.name customer_name,B.date bill_date,B.bill_number,SUM(amount_payable) amount_payable,SUM(credit_amount) credit_amount,SUM(amount_paid) amount_paid,
                         CASE
                            WHEN DATEDIFF(P.paid_on,P.due_date) Is Null  THEN "-"
                            WHEN DATEDIFF(P.paid_on,P.due_date) > 0 THEN DATEDIFF(P.paid_on,P.due_date)
                            WHEN DATEDIFF(P.paid_on,P.due_date) < 0 THEN 0
                         END as exceeded_days,
                          CASE
                            WHEN (P.status=2) THEN "Paid"
                            WHEN (P.status=5) THEN "Partialy Paid"
                            WHEN (P.status=9) THEN "Not Paid"
                         END as status'
                         
                         )
                ->from('companies_postpaid_bills P')
                ->join('companies C','C.id=P.company_id')
                ->join('bils B','B.id=P.bill_id')
                ->where('P.company_id',$id)
                ->group_by('P.bill_id');
                //->where('DATE(B.date)>=',$start)
                //->where('DATE(B.date)<=',$end);
                //if($dayrange!=''){
                //    $this->db->where('DATEDIFF(P.paid_on,P.due_date)',$dayrange);
                //}
            $this->db->stop_cache();
            $total = $this->db->get();
            if($limit!=0) $this->db->limit($limit,$offset);
            $q = $this->db->get();
            $this->db->flush_cache();
            if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return array('data'=>$data,'total'=>$total->num_rows());
        }
        return FALSE;
    }
    function GetPostpaid_BillDetails($id,$billId=false){
        $this->db
                ->select('C.name customer_name,C.id customer_id,SUM(amount_payable) amount,SUM(amount_paid) amount_paid,,SUM(credit_amount) credit_amount')                         
                ->from('companies_postpaid_bills P')
                ->join('companies C','C.id=P.company_id')
                ->join('bils B','B.id=P.bill_id')
                ->where('P.company_id',$id);
                if($billId){
                    $this->db->where('P.bill_id',$billId);
                }
                $this->db->group_by('P.company_id');
                
                
        $q = $this->db->get();
                if ($q->num_rows() > 0) {
                    
                    $data = $q->row();
                    return array('bill'=>$data);
                }
                return FALSE;
    }
    function postpaid_Addpayment($data,$billId){
        if ($this->db->insert('postpaid_payments', $data)) {
            $id = $this->db->insert_id();
            $this->db
                ->select('P.*')
                ->from('companies_postpaid_bills P')
                ->join('companies C','C.id=P.company_id')
                ->join('bils B','B.id=P.bill_id')
                ->where('P.company_id',$data['company_id'])
                ->where('P.status!=',2);
                if($billId){
                    $this->db->where('P.bill_id',$billId);
                }
            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                $amountpaid = $data['amount'];
                foreach (($q->result()) as $row) {
                    $update['payment_id'] = $id;
                        if($amountpaid>=$row->amount_payable){
                            $update['status'] = 2;
                            $update['amount_paid'] = $row->amount_paid+$row->amount_payable;
                            $update['amount_payable'] = 0;
                            $update['paid_on'] = date('Y-m-d H:i:s');
                            $amountpaid = $amountpaid-$row->amount_payable;
                            $this->db->where('id',$row->id);
                            $this->db->update('companies_postpaid_bills',$update);
                        }else{
                            $update['status'] = 5; //partial
                            $update['amount_paid'] = $row->amount_paid+$amountpaid;
                            $update['amount_payable'] = $row->amount_payable-$amountpaid;
                            $update['paid_on'] = date('Y-m-d H:i:s');
                            $this->db->where('id',$row->id);
                            $this->db->update('companies_postpaid_bills',$update);
                            break;
                        }
                        
                        //ec
                        //print_R($this->db->error());exit;
                }
            }
            return true;
        }
        return FALSE;
    }
    function getCustomerDetails($id){
        $this->db
            ->select('name,id')
                ->from('companies')
                ->where('id',$id);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            
            $data = $q->row();
            return $data;
        }
        return FALSE;
    }

public function getopenregisterReport($start,$end,$limit,$offset)
    {
       /* $where ='';
        if($warehouse_id != 0)
        {
            $where = "AND P.warehouse_id =".$warehouse_id."";
        }*/

        $openreg = "SELECT RP.id,RP.cash_in_hand,DATE_FORMAT(RP.date, '%d-%m-%Y') AS open_payment_date,U.first_name AS recived_from,UU.first_name as creared_by
        FROM " . $this->db->dbprefix('open_register_payments') . " AS RP
         JOIN  " . $this->db->dbprefix('pos_open_register') . " AS PR ON PR.id = RP.register_id
         JOIN  " . $this->db->dbprefix('users') . " AS U ON U.id = PR.user_id
         JOIN  " . $this->db->dbprefix('users') . " AS UU ON UU.id = PR.created_by
            WHERE DATE(RP.date) BETWEEN '".$start."' AND '".$end."' "; 

            

        /*SELECT RP.id,RP.cash_in_hand,DATE_FORMAT(RP.date, '%d-%m-%Y') AS open_payment_date,U.first_name AS recived_from,UU.first_name as crearedby FROM `srampos_open_register_payments` AS RP
        JOIN srampos_pos_open_register PR ON PR.id = RP.register_id
        JOIN srampos_users U ON U.id = PR.user_id
        JOIN srampos_users UU ON UU.id = PR.created_by*/

        $limit_q = " limit $offset,$limit";
        $t = $this->db->query($openreg);        
        $q = $this->db->query($openreg);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return array('data'=>$data,'total'=>$t->num_rows());
        }
        return FALSE;
    } 

public function getcloseregisterReport($start,$end,$limit,$offset)
    {
       /* $where ='';
        if($warehouse_id != 0)
        {
            $where = "AND P.warehouse_id =".$warehouse_id."";
        }*/

        $openreg = "SELECT CR.id,CR.close_amt,DATE_FORMAT(CR.date, '%d-%m-%Y') AS close_payment_date,U.first_name AS recived_from,UU.first_name as creared_by
        FROM " . $this->db->dbprefix('close_register_payments') . " AS CR
         JOIN  " . $this->db->dbprefix('pos_open_register') . " AS PR ON PR.id = CR.register_id
         JOIN  " . $this->db->dbprefix('users') . " AS U ON U.id = PR.user_id
         JOIN  " . $this->db->dbprefix('users') . " AS UU ON UU.id = PR.created_by
            WHERE DATE(CR.date) BETWEEN '".$start."' AND '".$end."' ";
        $limit_q = " limit $offset,$limit";
        $t = $this->db->query($openreg);        
        $q = $this->db->query($openreg);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return array('data'=>$data,'total'=>$t->num_rows());
        }
        return FALSE;
    }        
public function getFeedBackReports($start,$end,$warehouse_id,$limit,$offset)
    {  
      
     
        $FeedBack = "SELECT F.split_id,B.bill_number,DATE_FORMAT(B.date, '%d-%m-%Y') bill_date,C.name,T.photo,T.audio,RT.name AS table_name,T.fb_postid,CASE WHEN T.comment !='' THEN T.comment ELSE  '-' END AS comment
        FROM " . $this->db->dbprefix('feedback') . " F        
        JOIN " . $this->db->dbprefix('testimonial') . "  T ON T.split_id = F.split_id
        JOIN " . $this->db->dbprefix('companies') . " C ON C.id = F.customer_id 
        JOIN " . $this->db->dbprefix('sales') . " S ON S.sales_split_id = F.split_id
        JOIN " . $this->db->dbprefix('bils') . " B ON  B.sales_id = S.id
        LEFT JOIN " . $this->db->dbprefix('restaurant_tables') . " RT ON RT.id = S.sales_table_id
        JOIN " . $this->db->dbprefix('sales_type') . " ST ON ST.id = S.sales_type_id
         WHERE DATE(F.create_on) BETWEEN '".$start."' AND '".$end."' 
        GROUP BY F.split_id ORDER BY B.id";
        $limit_q = " limit $offset,$limit"; 
        $t = $this->db->query($FeedBack);        
        // var_dump($t->num_rows());die;
        if($limit!=0) $FeedBack .=$limit_q;
        $q = $this->db->query($FeedBack);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return array('data'=>$data,'total'=>$t->num_rows());
        }
        return FALSE;
    }    
public function getFeedBackItems($split_id)
    {   
        $itemFeedBack = "SELECT R.name item_name,F.message,F.status
        FROM " . $this->db->dbprefix('feedback') . " F        
        JOIN " . $this->db->dbprefix('companies') . " C ON C.id = F.customer_id         
        JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = F.item_id
        JOIN " . $this->db->dbprefix('sales') . " S ON S.sales_split_id = F.split_id
        JOIN " . $this->db->dbprefix('bils') . " B ON  B.sales_id = S.id WHERE F.split_id = '".$split_id."'
        GROUP BY F.item_id";    

        $q = $this->db->query($itemFeedBack);

        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        
        return FALSE;
    }        
public function getFeedBackAboutCompany($split_id)
    {   
        $CompanyFeedBack = "SELECT EF.question_id,EF.answer
        FROM " . $this->db->dbprefix('feedback') . " F        
        JOIN " . $this->db->dbprefix('companies') . " C ON C.id = F.customer_id 
        JOIN " . $this->db->dbprefix('extrafeedback') . " EF ON EF.split_id = F.split_id        
        JOIN " . $this->db->dbprefix('sales') . " S ON S.sales_split_id = F.split_id
        JOIN " . $this->db->dbprefix('bils') . " B ON  B.sales_id = S.id WHERE F.split_id = '".$split_id."'
        GROUP BY EF.question_id";                
        $q = $this->db->query($CompanyFeedBack);

        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }
public function getCustomerInfo($split_id)
    {   
        $Customerdet = "SELECT C.*,B.reference_no,DATE_FORMAT(B.date, '%d-%m-%Y') bill_date,B.bill_number,U.username AS sales_associate,PU.username AS cashier
        FROM " . $this->db->dbprefix('feedback') . " F        
        JOIN " . $this->db->dbprefix('companies') . " C ON C.id = F.customer_id 
        JOIN " . $this->db->dbprefix('sales') . " S ON S.sales_split_id = F.split_id
        JOIN " . $this->db->dbprefix('bils') . " B ON  B.sales_id = S.id 
        JOIN " . $this->db->dbprefix('payments') . " PM ON  PM.bill_id = B.id         
        JOIN " . $this->db->dbprefix('users') . " U ON  B.created_by = U.id        
        JOIN " . $this->db->dbprefix('users') . " PU ON  PM.created_by = PU.id        
        WHERE F.split_id = '".$split_id."' GROUP BY F.split_id";                
        $q = $this->db->query($Customerdet);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data = $row;
            }
            return $data;
        }
    }
    function getTestimonialData($splitid){
        $this->db->select('t.*,c.name as customer_name');
        $this->db->from('testimonial t');
        $this->db->join('companies c','c.id = t.customer_id');
        $this->db->where(array('t.split_id'=>$splitid));
        //echo $this->db->get_compiled_select();
        $q = $this->db->get();
        if($q->num_rows()>0){
            return $q->row();
        }
        return false;
    }
    function updateFBPostid($testimonialid,$fbpostid){
        $data['fb_postid'] = $fbpostid;
        $this->db->where('id',$testimonialid);
        $this->db->update('testimonial',$data);
        return true;
    }

public function getBBQDetailsReport($start,$end,$warehouse_id,$summary_items,$limit,$offset)
    {  
        $where ='';
        if($warehouse_id != 0)
        {
            $where = "AND P.warehouse_id =".$warehouse_id."";
        }     

        $BBQsummaydetails = "SELECT DATE_FORMAT(P.date, '%d-%m-%Y %H:%i') AS bill_date,C.name as customer_name,P.bill_number,SUM(CASE WHEN (BQI.type = 'adult') THEN BQI.cover ELSE 0 END) as no_of_adult,SUM(CASE WHEN (BQI.type = 'child') THEN BQI.cover ELSE 0 END) as no_of_child,SUM(CASE WHEN (BQI.type = 'kids') THEN BQI.cover ELSE 0 END) as no_of_kids,P.total,P.total_tax,P.total_discount,(P.total-P.total_discount+CASE WHEN (P.tax_type= 1) THEN P.total_tax ELSE 0 END) as grand_total,GROUP_CONCAT(DISTINCT PM.paid_by separator ', ') AS paid_by
                FROM ".$this->db->dbprefix('bils')." AS P
                JOIN ". $this->db->dbprefix('companies') ." AS C ON C.id = P.customer_id
                JOIN ". $this->db->dbprefix('bbq_bil_items') ." AS BQI ON BQI.bil_id = P.id
                LEFT JOIN srampos_payments PM ON PM.bill_id = P.id
                WHERE DATE(P.date) BETWEEN '".$start."' AND '".$end."' AND P.order_type = 4 AND
                P.payment_status ='Completed'  GROUP BY P.id "; 
 
                $t = $this->db->query($BBQsummaydetails);  
                $q = $this->db->query($BBQsummaydetails);
                if ($q->num_rows() > 0) {
                    foreach (($q->result()) as $row) {
                        $data[] = $row;
                    }
                    return array('data'=>$data,'total'=>$t->num_rows());
                }
                return FALSE;
    }
public function getBBQitemsDetailsReport($start,$end,$warehouse_id,$summary_items,$limit,$offset)
    {  
        $where ='';
        if($warehouse_id != 0)
        {
            $where = "AND P.warehouse_id =".$warehouse_id."";
        }     

        /*$BBQsummaydetails = "SELECT item,pcs_percondo,order_qtycondo,ordered_qtypcs,cost,item_qty_rate,IFNULL(returnqty_condopcs,0) as returnqty_condopcs,IFNULL((ordered_qtypcs-returnqty_condopcs) , 0)AS totalconsum_qtycondopcs,
                        IFNULL((item_qty_rate/ordered_qtypcs) ,0)AS AvgCost_PricePerunit,IFNULL(((item_qty_rate/ordered_qtypcs) * ordered_qtypcs) ,0)AS costper_orderqty,
                        IFNULL(((item_qty_rate/ordered_qtypcs) * returnqty_condopcs) ,0)AS Return_Price
                        ,IFNULL(((ordered_qtypcs-returnqty_condopcs) *(item_qty_rate/ordered_qtypcs) ) ,0)AS profit from (SELECT  SRI.recipe_id,R.name AS item,R.piece AS pcs_percondo,SUM(BI.quantity) AS order_qtycondo,R.piece * SUM(BI.quantity) AS ordered_qtypcs,
                        SUM(DISTINCT SRI.return_piece) AS returnqty_condopcs,SUM(DISTINCT R.cost * BI.quantity)  AS item_qty_rate,R.cost FROM  " . $this->db->dbprefix('bils') . " B 
                        JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.bil_id = B.id 
                        LEFT JOIN " . $this->db->dbprefix('sale_return_item') . " SRI ON SRI.recipe_id =  BI.recipe_id                         
                        JOIN " . $this->db->dbprefix('sales') . " S ON S.id = B.sales_id
                        LEFT JOIN " . $this->db->dbprefix('sale_return') . " SR ON SR.split_id = S.sales_split_id 
                        JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
                        
                        WHERE DATE(B.date) BETWEEN '".$start."' AND '".$end."' 
                        AND B.order_type = 4 AND B.payment_status ='Completed' 
                        GROUP BY R.id ) t";
                     echo $BBQsummaydetails;die;*/
/*
                    $BBQsummaydetails = "SELECT item,pcs_percondo,order_qtycondo,(pcs_percondo * order_qtycondo) AS ordered_qtypcs,cost,item_qty_rate,returnqty_condopcs,IFNULL(((pcs_percondo * order_qtycondo)-returnqty_condopcs) , 0)AS totalconsum_qtycondopcs,item_qty_rate/(pcs_percondo * order_qtycondo) AS AvgCost_PricePerunit,((item_qty_rate/pcs_percondo * order_qtycondo) *pcs_percondo * order_qtycondo) AS costper_orderqty,((item_qty_rate/pcs_percondo * order_qtycondo) *returnqty_condopcs) AS Return_Price  FROM (SELECT TT.recipe_name AS item,TT.piece AS pcs_percondo,SUM(TT.quantity) order_qtycondo,(TT.cost * SUM(TT.quantity)) AS item_rate, TT.cost,SUM(SRI.return_piece) AS returnqty_condopcs,SUM(TT.cost * TT.quantity)  AS item_qty_rate,TT.recipe_id from (SELECT BI.recipe_name,BI.quantity,BI.recipe_id,R.cost,R.piece,B.date,B.payment_status,B.order_type  FROM  " . $this->db->dbprefix('bil_items') . " BI
                    JOIN  ". $this->db->dbprefix('bils') . " B ON B.id = BI.bil_id                     
                    JOIN ". $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id group by recipe_id) TT 
                    JOIN (SELECT * FROM ". $this->db->dbprefix('sale_return_item') . ") SRI
                    ON SRI.recipe_id = TT.recipe_id
                    WHERE DATE(TT.date) BETWEEN '".$start."' AND '".$end."' 
                    AND TT.order_type = 4 AND TT.payment_status ='Completed'
                    GROUP BY TT.recipe_id ) F ";*/

                    $BBQsummaydetails = "SELECT TT.recipe_name AS item,TT.piece AS pcs_percondo,SUM(TT.quantity) order_qtycondo,SUM(SRI.return_piece) AS returnqty_condopcs,SUM(TT.cost * TT.quantity)  AS item_qty_rate,TT.recipe_id,(TT.piece * SUM(TT.quantity)) AS orderpcs,(SUM(TT.cost * TT.quantity) /(TT.piece * SUM(TT.quantity))) AS avgpcsrate,TT.cost,SUM(SRI.return_piece) AS 

                    returnqty_condopcs from (SELECT BI.recipe_name,BI.quantity,BI.recipe_id,R.cost,R.piece,B.date,B.payment_status,B.order_type  FROM  srampos_bil_items BI
                    JOIN  srampos_bils B ON B.id = BI.bil_id                     
                    JOIN srampos_recipe R ON R.id = BI.recipe_id WHERE DATE(B.date) BETWEEN '".$start."' AND '".$end."'  
                    AND B.order_type = 4 AND B.payment_status ='Completed' group by recipe_id) TT 
                    JOIN (SELECT * FROM srampos_sale_return_item) SRI
                    ON SRI.recipe_id = TT.recipe_id
                    WHERE DATE(TT.date) BETWEEN '".$start."' AND '".$end."'  
                    AND TT.order_type = 4 AND TT.payment_status ='Completed'
                    GROUP BY TT.recipe_id";

 // echo $BBQsummaydetails;die;
                $t = $this->db->query($BBQsummaydetails);  
                $q = $this->db->query($BBQsummaydetails);
                if ($q->num_rows() > 0) {
                    foreach (($q->result()) as $row) {
                        $data[] = $row;
                    }
                    return array('data'=>$data,'total'=>$t->num_rows());
                }
                return FALSE;
    }    
public function getBBQBillDetailsReport($start,$end,$warehouse_id,$summary_items,$limit,$offset)
    {  

        
         $BBQbill = "SELECT B.id,B.bill_number
            FROM ". $this->db->dbprefix('bils') ." AS B            
            WHERE DATE(B.date) BETWEEN '".$start."' AND '".$end."' AND
             B.payment_status ='Completed' AND B.order_type = 4 
            GROUP BY B.id ORDER BY B.id ASC";
            
            $limit_q = " limit $offset,$limit";
            $t = $this->db->query($BBQbill);
            if($limit!=0) $BBQbill .=$limit_q;
            $u = $this->db->query($BBQbill);
            
        if ($u->num_rows() > 0) {
            foreach (($u->result()) as $uow) {

               /*$BBQsummaydetails = "SELECT item,pcs_percondo,order_qtycondo,(pcs_percondo * order_qtycondo) AS ordered_qtypcs,cost,item_qty_rate,returnqty_condopcs,IFNULL(((pcs_percondo * order_qtycondo)-returnqty_condopcs) , 0)AS totalconsum_qtycondopcs,item_qty_rate/(pcs_percondo * order_qtycondo) AS AvgCost_PricePerunit,((item_qty_rate/pcs_percondo * order_qtycondo) *pcs_percondo * order_qtycondo) AS costper_orderqty,((item_qty_rate/pcs_percondo * order_qtycondo) *returnqty_condopcs) AS Return_Price  FROM (SELECT TT.recipe_name AS item,TT.piece AS pcs_percondo,SUM(TT.quantity) order_qtycondo,(TT.cost * SUM(TT.quantity)) AS item_rate, TT.cost,SUM(SRI.return_piece) AS returnqty_condopcs,SUM(TT.cost * TT.quantity)  AS item_qty_rate,TT.recipe_id,TT.id  from (SELECT BI.recipe_name,BI.quantity,BI.recipe_id,R.cost,R.piece,B.date,B.payment_status,B.order_type,B.id  FROM  " . $this->db->dbprefix('bil_items') . " BI
                        JOIN  ". $this->db->dbprefix('bils') . " B ON B.id = BI.bil_id                     
                        JOIN ". $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id WHERE DATE(B.date) BETWEEN '".$start."' AND '".$end."' 
                        AND B.order_type = 4 AND B.payment_status ='Completed' AND B.id='".$uow->id."'
                        group by recipe_id) TT 
                        JOIN (SELECT * FROM ". $this->db->dbprefix('sale_return_item') . ") SRI
                        ON SRI.recipe_id = TT.recipe_id                        
                        GROUP BY TT.recipe_id ) F ";*/


/*$BBQsummaydetails = "SELECT recipe_name as item,pcs_percondo,order_qtycondo,(pcs_percondo * order_qtycondo) 

AS ordered_qtypcs,cost,item_qty_rate,returnqty_condopcs,IFNULL(((pcs_percondo * 

order_qtycondo)-returnqty_condopcs) , 0)AS 

totalconsum_qtycondopcs,item_qty_rate/(pcs_percondo * order_qtycondo) AS 

AvgCost_PricePerunit,((item_qty_rate/pcs_percondo * order_qtycondo) 

*pcs_percondo * order_qtycondo) AS costper_orderqty,((item_qty_rate/pcs_percondo 

* order_qtycondo) *returnqty_condopcs) AS Return_Price  FROM(SELECT TT.recipe_name,TT.piece AS pcs_percondo,SUM(TT.quantity) order_qtycondo,(TT.cost 

* SUM(TT.quantity)) AS item_rate, TT.cost,SUM(SRI.return_piece) AS 

returnqty_condopcs,SUM(TT.cost * TT.quantity)  AS item_qty_rate,TT.recipe_id  from (SELECT BI.recipe_name,BI.quantity,BI.recipe_id,R.cost,R.piece,B.date,B.payment_status,B
.order_type,B.id,S.sales_split_id  FROM srampos_bils B
JOIN srampos_bil_items BI ON BI.bil_id = B.id
JOIN srampos_sales S ON S.id = B.sales_id
JOIN srampos_recipe R ON R.id = BI.recipe_id
WHERE B.id =".$uow->id.") TT
JOIN (SELECT SR.split_id,RI.recipe_id,RI.return_piece FROM srampos_sale_return SR
     JOIN srampos_sale_return_item RI ON RI.sale_return_id = SR.id
     JOIN srampos_sales S ON S.sales_split_id = SR.split_id
      JOIN srampos_bils B ON B.sales_id = S.id
     WHERE B.id =".$uow->id." GROUP BY B.id,RI.recipe_id ) SRI ON SRI.split_id = TT.sales_split_id AND SRI.recipe_id = TT.recipe_id
      GROUP BY TT.recipe_id,TT.id )FF ";*/

            $BBQsummaydetails = " SELECT TT.recipe_name,TT.piece AS pcs_percondo,SUM(TT.quantity) order_qtycondo,(TT.piece * SUM(TT.quantity)) AS orderpcs,((SUM(TT.cost * TT.quantity))/TT.piece * SUM(TT.quantity)) AS avgpcsrate,TT.cost,SUM(SRI.return_piece) AS 

            returnqty_condopcs,SUM(TT.cost * TT.quantity)  AS item_qty_rate,TT.recipe_id  
            FROM

            (SELECT BI.recipe_name,BI.quantity,BI.recipe_id,R.cost,R.piece,B.date,B.payment_status,B
            .order_type,B.id,S.sales_split_id  

            FROM srampos_bils B
            JOIN srampos_bil_items BI ON BI.bil_id = B.id
            JOIN srampos_sales S ON S.id = B.sales_id
            JOIN srampos_recipe R ON R.id = BI.recipe_id
            WHERE  DATE(B.date) BETWEEN '".$start."' AND '".$end."'  
                    AND B.order_type = 4 AND  B.id =".$uow->id." AND B.payment_status ='Completed') TT
            JOIN (SELECT SR.split_id,RI.recipe_id,RI.return_piece FROM srampos_sale_return SR
            JOIN srampos_sale_return_item RI ON RI.sale_return_id = SR.id
            JOIN srampos_sales S ON S.sales_split_id = SR.split_id
            JOIN srampos_bils B ON B.sales_id = S.id
            WHERE DATE(B.date) BETWEEN '".$start."' AND '".$end."'  
                    AND B.order_type = 4 AND  B.id =".$uow->id." AND B.payment_status ='Completed' GROUP BY B.id,RI.recipe_id ) SRI ON SRI.split_id = TT.sales_split_id AND SRI.recipe_id = TT.recipe_id
            GROUP BY TT.recipe_id,TT.id ";


                      /*  $BBQsummaydetails = "SELECT item,pcs_percondo,order_qtycondo,ordered_qtypcs,cost,item_qty_rate,IFNULL(returnqty_condopcs,0) as returnqty_condopcs,IFNULL((ordered_qtypcs-returnqty_condopcs) , 0)AS totalconsum_qtycondopcs,
                        IFNULL((item_qty_rate/ordered_qtypcs) ,0)AS AvgCost_PricePerunit,IFNULL(((item_qty_rate/ordered_qtypcs) * ordered_qtypcs) ,0)AS costper_orderqty,
                        IFNULL(((item_qty_rate/ordered_qtypcs) * returnqty_condopcs) ,0)AS Return_Price
                        ,IFNULL(((ordered_qtypcs-returnqty_condopcs) *(item_qty_rate/ordered_qtypcs) ) ,0)AS profit from (SELECT  SRI.recipe_id,R.name AS item,R.piece AS pcs_percondo,SUM(DISTINCT BI.quantity) AS order_qtycondo,R.piece * BI.quantity AS ordered_qtypcs,
                        SUM(DISTINCT SRI.return_piece) AS returnqty_condopcs,SUM(DISTINCT R.cost * BI.quantity)  AS item_qty_rate,R.cost FROM  " . $this->db->dbprefix('bils') . " B 
                        JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.bil_id = B.id 
                        JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
                        JOIN " . $this->db->dbprefix('sales') . " S ON S.id = B.sales_id
                        LEFT JOIN " . $this->db->dbprefix('sale_return') . " SR ON SR.split_id = S.sales_split_id 
                        LEFT JOIN " . $this->db->dbprefix('sale_return_item') . " SRI ON SRI.sale_return_id = SR.id 
                        WHERE DATE(B.date) BETWEEN '".$start."' AND '".$end."' 
                        AND B.order_type = 4 AND B.payment_status ='Completed' AND B.id='".$uow->id."'
                        GROUP BY R.id ) t"; */                   
    // echo $BBQsummaydetails;die;
                    $q = $this->db->query($BBQsummaydetails);
                    if ($q->num_rows() > 0) {
                        foreach (($q->result()) as $row) {
                            $user[$uow->id][] = $row;
                        }
                        $uow->user = $user[$uow->id];
                        $data[] = $uow;
                    }
                }
            return array('data'=>$data,'total'=>$t->num_rows());
        }
        return FALSE;
    }
    public function get_bbqnotificationrports($start,$end,$warehouse_id,$limit,$offset)
    {  
        $where ='';
        if($warehouse_id != 0)
        {
            $where = "AND P.warehouse_id =".$warehouse_id."";
        }     

        $BBQNotifidetails = "SELECT *,DATE_FORMAT(b.created_on, '%d-%m-%Y %H:%i') AS notification_date,u.first_name as username 
                FROM ".$this->db->dbprefix('bbq_validation_notify')." AS b
                JOIN ". $this->db->dbprefix('users') ." AS u ON u.id = b.to_user_id
                WHERE DATE(b.created_on) BETWEEN '".$start."' AND '".$end."'"; 
                $limit_q = " limit $offset,$limit";
                $t = $this->db->query($BBQNotifidetails); 
                if($limit!=0) $BBQNotifidetails .=$limit_q;
                
                $q = $this->db->query($BBQNotifidetails);
                if ($q->num_rows() > 0) {
                    foreach (($q->result()) as $row) {
                        $data[] = $row;
                    }
                    return array('data'=>$data,'total'=>$t->num_rows());
                }
                return FALSE;
    }
public function check_reportview_access($pass_code){

    $myQuery = "SELECT (CASE WHEN (S.taxation_all  =".$pass_code.")  THEN 1 WHEN (S.taxation_include  =".$pass_code.") THEN 2 WHEN ((S.taxation_exclude =".$pass_code.") )  THEN 3                           
         ELSE 0 END) AS report_view
        FROM " . $this->db->dbprefix('pos_settings') . " AS S ";         
        $q = $this->db->query($myQuery);

        if ($q->num_rows() > 0) {

            $res = $q->row();
            return $res->report_view;
        }  
    }
    
    public function getStoreRequest_Report($start,$end,$warehouse_id,$limit,$offset)
    {  
      
     
        $query = "SELECT SR.date as date,SR.reference_no,product_code,product_name,quantity,unit_price,FW.name as from_store,TW.name as to_warehouse
        FROM " . $this->db->dbprefix('pro_store_request_items') . " SRI        
        JOIN " . $this->db->dbprefix('pro_store_request') . "  SR ON SR.id = SRI.store_request_id
        JOIN " . $this->db->dbprefix('warehouses') . " FW ON FW.id = SR.from_store_id 
        JOIN " . $this->db->dbprefix('warehouses') . " TW ON TW.id = SR.to_store_id 
        WHERE DATE(SR.date) BETWEEN '".$start."' AND '".$end."' 
        ORDER BY SRI.id";
        $limit_q = " limit $offset,$limit"; 
        $t = $this->db->query($query);        
        // var_dump($t->num_rows());die;
        if($limit!=0) $query .=$limit_q;
        $q = $this->db->query($query);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return array('data'=>$data,'total'=>$t->num_rows());
        }
        return FALSE;
    }
    public function getQuotesRequest_Report($start,$end,$warehouse_id,$limit,$offset)
    {  
      
     
        $query= "SELECT QR.reference_no,SR.reference_no as store_request,W.name as store_name,QR.supplier,QRI.product_code,QRI.product_name,QRI.quantity,QRI.unit_price
        FROM " . $this->db->dbprefix('pro_request_items') . " QRI        
        JOIN " . $this->db->dbprefix('pro_request') . "  QR ON QR.id = QRI.request_id
        JOIN " . $this->db->dbprefix('warehouses') . " W ON W.id = QRI.store_id
        LEFT JOIN " . $this->db->dbprefix('pro_store_request') . "  SR ON FIND_IN_SET(SR.id,QR.store_request_ids) > 0
        
         JOIN " . $this->db->dbprefix('pro_store_request_items') . "  SRI ON SRI.store_request_id = SR.id AND QRI.product_id = SRI.product_id
        WHERE DATE(QR.date) BETWEEN '".$start."' AND '".$end."' 
        ORDER BY QRI.id";
        $limit_q = " limit $offset,$limit"; 
        $t = $this->db->query($query);        
        // var_dump($t->num_rows());die;
        if($limit!=0) $query .=$limit_q;
        $q = $this->db->query($query);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
                
            }
            return array('data'=>$data,'total'=>$t->num_rows());
        }
        return FALSE;
    }
     public function getQuotation_Report($start,$end,$warehouse_id,$limit,$offset)
    {  
      
     
        $query= "SELECT Q.reference_no,QR.reference_no as quote_request,SR.reference_no as store_request,Q.supplier,QI.product_code,QI.product_name,QI.quantity,QI.cost_price as cost
        FROM " . $this->db->dbprefix('pro_quote_items') . " QI        
        JOIN " . $this->db->dbprefix('pro_quotes') . "  Q ON Q.id = QI.quote_id
        LEFT JOIN " . $this->db->dbprefix('pro_request') . "  QR ON QR.id = Q.request_id
        LEFT JOIN " . $this->db->dbprefix('pro_store_request') . "  SR ON SR.id = QR.request_id
        WHERE DATE(Q.date) BETWEEN '".$start."' AND '".$end."' 
        ORDER BY QI.id";
        $limit_q = " limit $offset,$limit"; 
        $t = $this->db->query($query);        
        // var_dump($t->num_rows());die;
        if($limit!=0) $query .=$limit_q;
        $q = $this->db->query($query);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return array('data'=>$data,'total'=>$t->num_rows());
        }
        return FALSE;
    }
    
    public function getPurchaseOrder_Report($start,$end,$warehouse_id,$limit,$offset)
    {  
      
     
        $query= "SELECT PO.reference_no,PO.supplier,POI.product_code,POI.product_name,POI.quantity,POI.cost,POI.selling_price,POI.landing_cost,POI.margin,POI.item_disc_amt,POI.item_bill_disc_amt,POI.gross,POI.tax_rate,POI.item_tax
        FROM " . $this->db->dbprefix('pro_purchase_order_items') . " POI        
        JOIN " . $this->db->dbprefix('pro_purchase_orders') . "  PO ON PO.id = POI.purchase_order_id
        WHERE DATE(PO.date) BETWEEN '".$start."' AND '".$end."' 
        ORDER BY POI.id";
        $limit_q = " limit $offset,$limit"; 
        $t = $this->db->query($query);        
        // var_dump($t->num_rows());die;
        if($limit!=0) $query .=$limit_q;
        $q = $this->db->query($query);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return array('data'=>$data,'total'=>$t->num_rows());
        }
        return FALSE;
    }
    
    public function getPurchaseInvoice_Report($start,$end,$warehouse_id,$limit,$offset)
    {  
      
     
        $query= "SELECT PI.reference_no,PI.supplier,PII.product_code,PII.product_name,PII.quantity,PII.cost,PII.selling_price,PII.landing_cost,PII.margin,PII.item_disc_amt,PII.item_bill_disc_amt,PII.gross,PII.tax_rate,PII.tax
        FROM " . $this->db->dbprefix('pro_purchase_invoice_items') . " PII        
        JOIN " . $this->db->dbprefix('pro_purchase_invoices') . "  PI ON PI.id = PII.invoice_id
        WHERE DATE(PI.date) BETWEEN '".$start."' AND '".$end."' 
        ORDER BY PII.id";
        $limit_q = " limit $offset,$limit"; 
        $t = $this->db->query($query);        
        // var_dump($t->num_rows());die;
        if($limit!=0) $query .=$limit_q;
        $q = $this->db->query($query);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return array('data'=>$data,'total'=>$t->num_rows());
        }
        return FALSE;
    } 
}

