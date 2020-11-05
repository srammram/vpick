<style>
.mainmenu{padding: 0px;margin: 10% 0px 0px;list-style-type: none;position: relative;}
.navbar-collapse,#sidebar-left{padding: 0px;}
.mainmenu li{padding: 6px 0px;color: #fff;}
.mainmenu li span{padding-left: 15px;}
	.mainmenu li strong{position: absolute;margin-left: 10px;color: #006635; font-weight: 600;}
.mainmenu li a {
  display: block;
  background-color: transparent;
  text-decoration: none;
  padding: 6px 0px 6px 30%;
  color: #fff;
}
	.mainmenu li a::before{position: absolute;content:'';width: 10px;height: 10px;background-color: #006635;left: 21.7%;margin-top: 6px; }
	.mainmenu li a:hover:before{background-color: #000;}
.mainmenu a:hover {
    background-color: #006635;
	color: #000;transition: all 0.25s ease;
}
.mainmenu .active {
    background-color: #006635;
    color: #000;
}
.mainmenu li .active:before{background-color: #000;}
.mainmenu li:hover .submenu {
  display: block;
  max-height: 200px;
}
.submenu a {
  background-color: #999;
}

.submenu a:hover {
  background-color: #666;
}
.submenu {
  overflow: hidden;
  max-height: 0;
  -webkit-transition: all 0.5s ease-out;
}

	#sidebar_menu h3{margin:5px 0px;}
</style>
<div class="clearfix"></div>
	

<nav class="navigation" id="navigation_container">

  <ul class="mainmenu">
    <li><span><div class="kappoverview"></div> </span><strong><?= lang('overview') ?></strong>
    	<ul>
    		<li><a class="<?= $this->uri->segment(2) == 'welcome' ? 'active' : '' ?>" href="<?=admin_url('welcome')?>"><?= lang('statistics') ?></a></li>
            <?php if($this->session->userdata('group_id') != 4 && $this->session->userdata('group_id') !=5 && $this->session->userdata('group_id') !=3 && $this->session->userdata('group_id') !=6 ){  ?>
    		<li><a class="<?= $this->uri->segment(2) == 'heatmap' && $this->uri->segment(3) == 'available_cityride' ? 'active' : '' ?>" href="<?=admin_url('heatmap/available_cityride')?>"><?= lang('tracking') ?></a></li>
            <li><a class="<?= $this->uri->segment(2) == 'heatmap' && $this->uri->segment(3) == 'search_heatmap' ? 'active' : '' ?>" href="<?=admin_url('heatmap/search_heatmap')?>"><?= lang('search_heatmap') ?></a></li>
            <?php } ?>
            <li><a class="<?= $this->uri->segment(2) == 'notification' ? 'active' : '' ?>" href="<?=admin_url('notification/index')?>"><?= lang('notification') ?></a></li>
    	</ul>
    </li>
    <li><span><div class="kappmaster"></div></span><strong><?= lang('masters') ?></strong>
    	<ul>
        	<?php if($this->session->userdata('group_id') != 4 && $this->session->userdata('group_id') !=5 && $this->session->userdata('group_id') !=3 ){  ?>
            <?php if($this->session->userdata('group_id') != 6){ ?>
        	<li><a class="<?= $this->uri->segment(2) == 'people' && ($this->uri->segment(3) == 'employee' || $this->uri->segment(3) == 'employee_edit' || $this->uri->segment(3) == 'employee_role_setting' || $this->uri->segment(3) == 'add_employee') ? 'active' : '' ?>" href="<?=admin_url('people/employee')?>"><?= lang('staff') ?></a></li>
            <?php } ?>
            
    		<!--<li><a class="<?= $this->uri->segment(2) == 'people' && $this->uri->segment(3) == 'vendor' ? 'active' : '' ?>" href="<?=admin_url('people/vendor')?>">Vendor</a></li>-->
            
    		<li><a class="<?= $this->uri->segment(2) == 'people' && ($this->uri->segment(3) == 'driver' || $this->uri->segment(3) == 'add_driver' || $this->uri->segment(3) == 'driver_edit' || $this->uri->segment(3) == 'driver_role_setting') ? 'active' : '' ?>" href="<?=admin_url('people/driver')?>"><?= lang('driver') ?></a></li>
            
    		<li><a class="<?= $this->uri->segment(2) == 'taxi'  ? 'active' : '' ?>" href="<?=admin_url('taxi')?>"><?= lang('truck') ?></a></li>
            
    		<li><a class="<?= $this->uri->segment(2) == 'people' && ($this->uri->segment(3) == 'customer' || $this->uri->segment(3) == 'customer_view' || $this->uri->segment(3) == 'add_customer') ? 'active' : '' ?>" href="<?=admin_url('people/customer')?>"><?= lang('customer') ?></a></li>
            
            <li><a class="<?= $this->uri->segment(2) == 'rides'  ? 'active' : '' ?>" href="<?=admin_url('rides')?>"><?= lang('rides') ?></a></li>
            <?php } ?>
            <?php if($this->session->userdata('group_id') != 1 && $this->session->userdata('group_id') !=2 && $this->session->userdata('group_id') !=6 ){  ?>
            <li><a class="<?= $this->uri->segment(2) == 'usersrides'  ? 'active' : '' ?>" href="<?=admin_url('usersrides')?>"><?= lang('rides') ?></a></li>
            <?php } ?>
    	</ul>
    </li>
    <?php if($this->session->userdata('group_id') != 3 && $this->session->userdata('group_id') !=4 && $this->session->userdata('group_id') !=5  && $this->session->userdata('group_id') !=6 ){  ?>
    <li><span><div class="kappfare"></div></span><strong><?= lang('fare'); ?></strong>
    	<ul>
    		<li><a class="<?= $this->uri->segment(2) == 'locations' && ($this->uri->segment(3) == 'daily' || $this->uri->segment(3) == 'edit_daily' || $this->uri->segment(3) == 'add_daily') ? 'active' : '' ?>" href="<?=admin_url('locations/daily')?>"><?= lang('city_ride') ?></a></li>
    		<li><a class="<?= $this->uri->segment(2) == 'locations' && ($this->uri->segment(3) == 'outstation' || $this->uri->segment(3) == 'add_outstation' || $this->uri->segment(3) == 'edit_outstation') ? 'active' : '' ?>" href="<?=admin_url('locations/outstation')?>"><?= lang('outstation') ?></a></li>
    		<li class="hidden"><a class="<?= $this->uri->segment(2) == 'locations' && ($this->uri->segment(3) == 'rental' || $this->uri->segment(3) == 'add_rental' || $this->uri->segment(3) == 'edit_rental') ? 'active' : '' ?>" href="<?=admin_url('locations/rental')?>"><?= lang('rentals') ?></a></li>
    	</ul>
    </li>
    <?php } ?>
    <?php if($this->session->userdata('group_id') != 3 && $this->session->userdata('group_id') !=4 && $this->session->userdata('group_id') !=5  && $this->session->userdata('group_id') !=6 ){  ?>
    <li><span><div class="kappoption"></div></span><strong><?= lang('options') ?></strong>
    	<ul>
        	<?php if($this->session->userdata('group_id') != 1){ ?>
        	<li><a class="<?= $this->uri->segment(2) == 'masters' && $this->uri->segment(3) == 'index' ? 'active' : '' ?>" href="<?=admin_url('masters/index/'.$countryCode)?>"><?= lang('settings') ?></a></li>
            <?php }else{ ?>
            <li><a class="<?= $this->uri->segment(2) == 'masters' && $this->uri->segment(3) == 'index' ? 'active' : '' ?>" href="<?=admin_url('masters/index')?>"><?= lang('settings') ?></a></li>
            <?php } ?>
            
            <!--<li><a class="<?= $this->uri->segment(2) == 'masters' && $this->uri->segment(3) == 'countrywisesetting' ? 'active' : '' ?>" href="<?=admin_url('masters/countrywisesetting')?>">Country Settings</a></li>-->
    		<!--<li><a class="<?= $this->uri->segment(2) == 'masters' && $this->uri->segment(3) == 'user_department' ? 'active' : '' ?>"  href="<?=admin_url('masters/user_department')?>">User Department</a></li>-->
            <li><a class="<?= $this->uri->segment(2) == 'masters' && ($this->uri->segment(3) == 'currencies'  || $this->uri->segment(3) == 'add_currency' || $this->uri->segment(3) == 'edit_currency') ? 'active' : '' ?>"  href="<?=admin_url('masters/currencies')?>"><?= lang('currency') ?></a></li>
            
             <li><a class="<?= $this->uri->segment(2) == 'masters' && ($this->uri->segment(3) == 'company'  || $this->uri->segment(3) == 'add_company' || $this->uri->segment(3) == 'edit_company') ? 'active' : '' ?>"  href="<?=admin_url('masters/company')?>"><?= lang('company') ?></a></li>
            
            <li><a class="<?= $this->uri->segment(2) == 'masters' && ($this->uri->segment(3) == 'walletoffer'  || $this->uri->segment(3) == 'add_walletoffer' || $this->uri->segment(3) == 'edit_walletoffer') ? 'active' : '' ?>"  href="<?=admin_url('masters/walletoffer')?>"><?= lang('wallet_offer') ?></a></li>
            
            <li><a class="<?= $this->uri->segment(2) == 'masters' && ($this->uri->segment(3) == 'bank' || $this->uri->segment(3) == 'add_bank' || $this->uri->segment(3) == 'edit_bank') ? 'active' : '' ?>"  href="<?=admin_url('masters/bank')?>"><?= lang('bank') ?></a></li>
            <li><a class="<?= $this->uri->segment(2) == 'masters' && $this->uri->segment(3) == 'payment_gateway' ? 'active' : '' ?>"  href="<?=admin_url('masters/payment_gateway')?>"><?= lang('payment_gateway') ?></a></li>
            
    		<li><a class="<?= $this->uri->segment(2) == 'masters' && ($this->uri->segment(3) == 'tax' || $this->uri->segment(3) == 'add_tax' || $this->uri->segment(3) == 'edit_tax') ? 'active' : '' ?>"  href="<?=admin_url('masters/tax')?>"><?= lang('tax') ?></a></li>
            <!--<li><a href="<?=admin_url('masters/taxi_category')?>">Taxi Category</a></li>-->
            <li><a class="<?= $this->uri->segment(2) == 'masters' && $this->uri->segment(3) == 'taxi_category' ? 'active' : '' ?>"  href="<?=admin_url('masters/taxi_category')?>"><?= lang('truck_category') ?></a></li>
    		<li><a class="<?= $this->uri->segment(2) == 'masters' && $this->uri->segment(3) == 'taxi_type' ? 'active' : '' ?>"  href="<?=admin_url('masters/taxi_type')?>"><?= lang('truck_type') ?></a></li>
            <li><a class="<?= $this->uri->segment(2) == 'masters' && $this->uri->segment(3) == 'taxi_make' ? 'active' : '' ?>"  href="<?=admin_url('masters/taxi_make')?>"><?= lang('truck_make') ?></a></li>
            <li><a class="<?= $this->uri->segment(2) == 'masters' && $this->uri->segment(3) == 'taxi_model' ? 'active' : '' ?>"  href="<?=admin_url('masters/taxi_model')?>"><?= lang('truck_model') ?></a></li>
            <li><a class="<?= $this->uri->segment(2) == 'masters' && $this->uri->segment(3) == 'taxi_fuel' ? 'active' : '' ?>"  href="<?=admin_url('masters/taxi_fuel')?>"><?= lang('truck_fuel') ?></a></li>
            <li><a class="<?= $this->uri->segment(2) == 'masters' && $this->uri->segment(3) == 'shifting' ? 'active' : '' ?>"  href="<?=admin_url('masters/shifting')?>"><?= lang('shifting') ?></a></li>
            
            <?php if($this->session->userdata('group_id') == 1){ ?>
            <li><a class="<?= $this->uri->segment(2) == 'masters' && $this->uri->segment(3) == 'continents' ? 'active' : '' ?>"  href="<?=admin_url('masters/continents')?>"><?= lang('continents') ?></a></li>
            <li><a class="<?= $this->uri->segment(2) == 'masters' && $this->uri->segment(3) == 'country' ? 'active' : '' ?>"  href="<?=admin_url('masters/country')?>"><?= lang('countries') ?></a></li>
            <?php } ?>
            <li><a class="<?= $this->uri->segment(2) == 'masters' && $this->uri->segment(3) == 'zone' ? 'active' : '' ?>"  href="<?=admin_url('masters/zone')?>"><?= lang('zone') ?></a></li>
            <li><a class="<?= $this->uri->segment(2) == 'masters' && $this->uri->segment(3) == 'state' ? 'active' : '' ?>"  href="<?=admin_url('masters/state')?>"><?= lang('state') ?></a></li>
            <li><a class="<?= $this->uri->segment(2) == 'masters' && $this->uri->segment(3) == 'city' ? 'active' : '' ?>"  href="<?=admin_url('masters/city')?>"><?= lang('city') ?></a></li>
            <li><a class="<?= $this->uri->segment(2) == 'masters' && $this->uri->segment(3) == 'area' ? 'active' : '' ?>"  href="<?=admin_url('masters/area')?>"><?= lang('areas') ?></a></li>
            <li><a class="<?= $this->uri->segment(2) == 'masters' && $this->uri->segment(3) == 'pincode' ? 'active' : '' ?>"  href="<?=admin_url('masters/pincode')?>"><?= lang('pincode') ?></a></li>
            <li><a class="<?= $this->uri->segment(2) == 'masters' && ($this->uri->segment(3) == 'help' || $this->uri->segment(3) == 'help_main' || $this->uri->segment(3) == 'help_sub' || $this->uri->segment(3) == 'help_form') ? 'active' : '' ?>"  href="<?=admin_url('masters/help')?>"><?= lang('help') ?></a></li>
            
             <li><a class="<?= $this->uri->segment(2) == 'masters' && ($this->uri->segment(3) == 'import_csv_common_cab_setting') ? 'active' : '' ?>"  href="<?=admin_url('masters/import_csv_common_cab_setting')?>"><?= lang('import_csv_common_cab') ?></a></li>
             
             <li><a class="<?= $this->uri->segment(2) == 'masters' && ($this->uri->segment(3) == 'import_csv_common_location') ? 'active' : '' ?>"  href="<?=admin_url('masters/import_csv_common_location')?>"><?= lang('import_csv_common_location') ?></a></li>
            
    	</ul>
    </li>
    <?php } ?>
    
     
    
    <?php if($this->session->userdata('group_id') != 3 && $this->session->userdata('group_id') !=4 && $this->session->userdata('group_id') !=5  && $this->session->userdata('group_id') !=6 ){  ?>
    <li><span><div class="kappaccount"></div></span><strong>ACCOUNTS</strong>
    	<ul>
        	<li><a class="<?= $this->uri->segment(2) == 'account' && $this->uri->segment(3) == 'dashboard' ? 'active' : '' ?>" href="<?=admin_url('account/dashboard')?>"><?= lang('dashboard') ?></a></li>
            
            <li><a class="<?= $this->uri->segment(2) == 'account' && $this->uri->segment(3) == 'settlement_branch' ? 'active' : '' ?>" href="<?=admin_url('account/settlement_branch')?>"><?= lang('settlement_branch') ?></a></li>
            <li><a class="<?= $this->uri->segment(2) == 'account' && $this->uri->segment(3) == 'account_settlementlist' ? 'active' : '' ?>" href="<?=admin_url('account/account_settlementlist')?>"><?= lang('settlementlist') ?></a></li>
            
            <li><a class="<?= $this->uri->segment(2) == 'account' && $this->uri->segment(3) == 'account_owner' ? 'active' : '' ?>" href="<?=admin_url('account/account_owner')?>"><?= lang('owner') ?></a></li>
            <!--<li><a class="<?= $this->uri->segment(2) == 'account' && $this->uri->segment(3) == 'account_customer' ? 'active' : '' ?>" href="<?=admin_url('account/account_customer')?>"><?= lang('customer') ?></a></li>
            <li><a class="<?= $this->uri->segment(2) == 'account' && $this->uri->segment(3) == 'account_driver' ? 'active' : '' ?>" href="<?=admin_url('account/account_driver')?>"><?= lang('driver') ?></a></li>-->
            
            <li><a class="<?= $this->uri->segment(2) == 'account' && $this->uri->segment(3) == 'bank_excel' ? 'active' : '' ?>" href="<?=admin_url('account/bank_excel')?>"><?= lang('bank_excel') ?></a></li>
            <li><a class="<?= $this->uri->segment(2) == 'account' && $this->uri->segment(3) == 'reconcilation' ? 'active' : '' ?>" href="<?=admin_url('account/reconcilation')?>"><?= lang('reconcilation') ?></a></li>
            
    		 <li><a class="<?= $this->uri->segment(2) == 'account' && $this->uri->segment(3) == 'trip' ? 'active' : '' ?>" href="<?=admin_url('account/trip')?>"><?= lang('per_trip_accounting') ?></a></li>
             
    		<li><a class="<?= $this->uri->segment(2) == 'account' && ($this->uri->segment(3) == 'driver' || $this->uri->segment(3) == 'driver_view') ? 'active' : '' ?>" href="<?=admin_url('account/driver')?>"><?= lang('driver_payment') ?></a></li>
            <li><a class="<?= $this->uri->segment(2) == 'account' && $this->uri->segment(3) == 'complete_payment' ? 'active' : '' ?>" href="<?=admin_url('account/complete_payment')?>"><?= lang('payment_history') ?></a></li>
            <!--<li><a class="<?= $this->uri->segment(2) == 'account' && $this->uri->segment(3) == 'withdraw' ? 'active' : '' ?>" href="<?=admin_url('account/withdraw')?>"><?= lang('customer_settlement') ?></a></li>-->
    	</ul>
    </li>
    <?php } ?>
    
    
    
    <?php if($this->session->userdata('group_id') != 3 && $this->session->userdata('group_id') !=4 && $this->session->userdata('group_id') !=5  && $this->session->userdata('group_id') !=6 ){  ?>
    <li><span><div class="kappaccount"></div></span><strong><?= lang('wallet') ?></strong>
    	<ul>
    		<li><a class="<?= $this->uri->segment(2) == 'wallet' && $this->uri->segment(3) == 'index' ? 'active' : '' ?>" href="<?=admin_url('wallet/index')?>"><?= lang('dashboard') ?></a></li>
    		<li><a class="<?= $this->uri->segment(2) == 'wallet' && $this->uri->segment(3) == 'customer' ? 'active' : '' ?>" href="<?=admin_url('wallet/customer')?>"><?= lang('customer') ?></a></li>
            <li><a class="<?= $this->uri->segment(2) == 'wallet' && $this->uri->segment(3) == 'driver' ? 'active' : '' ?>" href="<?=admin_url('wallet/driver')?>"><?= lang('driver') ?></a></li>
           <!-- <li><a class="<?= $this->uri->segment(2) == 'wallet' && $this->uri->segment(3) == 'vendor' ? 'active' : '' ?>" href="<?=admin_url('wallet/vendor')?>"><?= lang('vendor') ?></a></li>-->
            <li><a class="<?= $this->uri->segment(2) == 'wallet' && $this->uri->segment(3) == 'owner' ? 'active' : '' ?>" href="<?=admin_url('wallet/owner')?>"><?= lang('owner') ?></a></li>
    	</ul>
    </li>
    <?php } ?>
    
    <?php if($this->session->userdata('group_id') != 3 && $this->session->userdata('group_id') !=4 && $this->session->userdata('group_id') !=5  && $this->session->userdata('group_id') !=6 ){  ?>
    <li><span><div class="kappaccount"></div></span><strong><?= lang('incentives') ?></strong>
    	<ul>
    		
    		<li><a class="<?= $this->uri->segment(2) == 'incentive' && ($this->uri->segment(3) == 'index' || $this->uri->segment(3) == 'add_incentive' || $this->uri->segment(3) == 'edit_incentive' || $this->uri->segment(3) == 'view_incentive') ? 'active' : '' ?>" href="<?=admin_url('incentive/index')?>"><?= lang('list') ?></a></li>
            <li><a class="<?= $this->uri->segment(2) == 'incentive' && ($this->uri->segment(3) == 'group' || $this->uri->segment(3) == 'edit_group' || $this->uri->segment(3) == 'add_group') ? 'active' : '' ?>" href="<?=admin_url('incentive/group')?>"><?= lang('group') ?></a></li>
            
    	</ul>
    </li>
    <?php } ?>
    
    <?php if($this->session->userdata('group_id') != 3 && $this->session->userdata('group_id') !=4 && $this->session->userdata('group_id') !=5  && $this->session->userdata('group_id') !=6 ){  ?>
    <li><span><div class="kappaccount"></div></span><strong><?= lang('offers') ?></strong>
    	<ul>
    		
    		<li><a class="<?= $this->uri->segment(2) == 'incentive' && ($this->uri->segment(3) == 'index' || $this->uri->segment(3) == 'add_offers' || $this->uri->segment(3) == 'edit_offers' || $this->uri->segment(3) == 'view_offers') ? 'active' : '' ?>" href="<?=admin_url('offers/index')?>"><?= lang('list') ?></a></li>
            
    	</ul>
    </li>
    <?php } ?>
    
    <?php if($this->session->userdata('group_id') != 1 && $this->session->userdata('group_id') !=2 && $this->session->userdata('group_id') !=6 ){  ?>
    <!--<li><span><div class="kappaccount"></div></span><strong>PAYMENTS</strong>
    	<ul>
    		<li><a class="<?= $this->uri->segment(2) == 'userspayments' && $this->uri->segment(3) == 'index' ? 'active' : '' ?>" href="<?=admin_url('userspayments/index')?>">Dashboard</a></li>
    		<li><a class="<?= $this->uri->segment(2) == 'userspayments' && $this->uri->segment(3) == 'listview' ? 'active' : '' ?>" href="<?=admin_url('userspayments/listview')?>">Payments</a></li>
            
    	</ul>
    </li>-->
    <?php } ?>
    
    <?php if($this->session->userdata('group_id') != 1 && $this->session->userdata('group_id') !=2 && $this->session->userdata('group_id') !=6 ){  ?>
    <li><span><div class="kappaccount"></div></span><strong><?= lang('crm') ?></strong>
    	<ul>
    		<li><a class="<?= $this->uri->segment(2) == 'usersenquiry' && $this->uri->segment(3) == 'index' ? 'active' : '' ?>" href="<?=admin_url('usersenquiry/index')?>"><?= lang('dashboard') ?></a></li>
    		<li><a class="<?= $this->uri->segment(2) == 'usersenquiry' && ($this->uri->segment(3) == 'listview' || $this->uri->segment(3) == 'close_transfer' || $this->uri->segment(3) == 'open') ? 'active' : '' ?>" href="<?=admin_url('usersenquiry/listview')?>"><?= lang('enquiry') ?></a></li>
            
    	</ul>
    </li>
    <?php } ?>
    <?php if($this->session->userdata('group_id') != 3 && $this->session->userdata('group_id') !=4 && $this->session->userdata('group_id') !=5){  ?>
    <li><span><div class="kappaccount"></div></span><strong><?= lang('crm') ?></strong>
    	<ul>
    		<li><a class="<?= $this->uri->segment(2) == 'enquiry' && $this->uri->segment(3) == 'index' ? 'active' : '' ?>" href="<?=admin_url('enquiry/index')?>"><?= lang('dashboard') ?></a></li>
    		<li><a class="<?= $this->uri->segment(2) == 'enquiry' && ($this->uri->segment(3) == 'listview' || $this->uri->segment(3) == 'close_transfer' || $this->uri->segment(3) == 'open') ? 'active' : '' ?>" href="<?=admin_url('enquiry/listview')?>"><?= lang('enquiry') ?></a></li>
            
    	</ul>
    </li>
    <?php
	}
	?>
    
    <?php if($this->session->userdata('group_id') != 3 && $this->session->userdata('group_id') !=4 && $this->session->userdata('group_id') !=5){  ?>
    <li class="hidden"><span><div class="kappaccount"></div></span><strong><?= lang('booking_ride') ?></strong>
    	<ul>
    		<li><a class="<?= $this->uri->segment(2) == 'booking_crm' && $this->uri->segment(3) == 'index' ? 'active' : '' ?>" href="<?=admin_url('booking_crm/index')?>"><?= lang('dashboard') ?></a></li>
    		<li><a class="<?= $this->uri->segment(2) == 'booking_crm' && ($this->uri->segment(3) == 'listview' ) ? 'active' : '' ?>" href="<?=admin_url('booking_crm/listview')?>"><?= lang('booking_ride') ?></a></li>
            
    	</ul>
    </li>
    <?php
	}
	?>
   
  </ul>
</nav>
<!--
<script>
$(document).ready(function(){
  $('ul li a').click(function(){
    $('li a').removeClass("active");
    $(this).addClass("active");
});
});
</script>
-->
<!--
 <script>
	$('#navigation_container').mCustomScrollbar({ 
        theme:"dark-3" ,
		mouseWheelPixels: 50 //change this to a value, that fits your needs
	});
	</script>
-->
