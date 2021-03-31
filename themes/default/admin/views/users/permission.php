<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
    .table td:first-child {
        font-weight: bold;
    }

    label {
        margin-right: 10px;
    }
</style>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-folder-open"></i><?= $page_title ?></h2>
    </div>
    <div class="box-content">
        <div class="row group-permission">
            <div class="col-lg-12">
                <?php
                if($department_id != '' && $designation_id !=''){
                ?>
                
				<div class="table-responsive">
                    <?php $attrib = array('class' => 'form-horizontal','class' => 'add_from','data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                    echo admin_form_open_multipart("users/permission", $attrib);
                    ?>
                    <input type="hidden" name="id" value="<?= $id ?>">
                    <input type="hidden" name="designation" value="<?= $designation_id ?>">
                    <input type="hidden" name="department" value="<?= $department_id ?>">
                    <input type="hidden" name="country" value="<?= $country ?>">
                    <input type="hidden" name="group_id" value="<?= $group_id ?>">

					<table class="table table-bordered table-hover table-striped reports-table">

						<thead>
						<tr>
							<th colspan="10"
								class="text-center"><?= $department_name.' ('.$designation_name.')' ?> Permission</th>
						</tr>
						<tr>
							<th rowspan="2" class="text-center"><?= lang("module_name"); ?></th>
							<th colspan="8" class="text-center"><?= lang("permissions"); ?></th>
						</tr>
						<tr>
							<th class="text-center"><?= lang("view"); ?></th>
							<th class="text-center"><?= lang("add"); ?></th>
							<th class="text-center"><?= lang("edit"); ?></th>
							<th class="text-center"><?= lang("delete"); ?></th>
							<th class="text-center"><?= lang("status"); ?></th>
							<th class="text-center"><?= lang("approved"); ?></th>
							<th class="text-center"><?= lang("import_csv"); ?></th>
							<th class="text-center"><?= lang("details"); ?></th>
						</tr>
						</thead>

						<tbody>
								<tr>
                                    <td colspan="10"><?= lang("overview"); ?></td>
								</tr>
                                <tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("statistics"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="statistics-index" <?php echo $p->{'statistics-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        
                                    </td>
                                    <td class="text-center">
                                        
                                    </td>
                                    <td class="text-center">
                                        
                                    </td>
                                    <td class="text-center">
                                        
                                    </td>
									<td class="text-center">
                                        
                                    </td>
									<td class="text-center">
                                        
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
								<tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("live_tracking"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="live_tracking-index" <?php echo $p->{'live_tracking-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        
                                    </td>
                                    <td class="text-center">
                                        
                                    </td>
                                    <td class="text-center">
                                        
                                    </td>
                                    <td class="text-center">
                                        
                                    </td>
									<td class="text-center">
                                        
                                    </td>
									<td class="text-center">
                                        
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
								<tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("search_heat_map"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="search_heat_map-index" <?php echo $p->{'search_heat_map-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        
                                    </td>
                                    <td class="text-center">
                                        
                                    </td>
                                    <td class="text-center">
                                        
                                    </td>
                                    <td class="text-center">
                                        
                                    </td>
									<td class="text-center">
                                        
                                    </td>
									<td class="text-center">
                                        
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
								<tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("notification"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="notification-index" <?php echo $p->{'notification-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        
                                    </td>
                                    <td class="text-center">
                                        
                                    </td>
                                    <td class="text-center">
                                        
                                    </td>
                                    <td class="text-center">
                                        
                                    </td>
									<td class="text-center">
                                        
                                    </td>
									<td class="text-center">
                                        
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
								<tr>
                                    <td colspan="10"><?= lang("masters"); ?></td>
								</tr>
								<tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("staff"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="staff-index" <?php echo $p->{'staff-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="staff-add" <?php echo $p->{'staff-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="staff-edit" <?php echo $p->{'staff-edit'} ? "checked" : ''; ?>>	
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="staff-delete" <?php echo $p->{'staff-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										                                      
                                    </td>
									<td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="staff-approved" <?php echo $p->{'staff-approved'} ? "checked" : ''; ?>>
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
								<tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("driver"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="driver-index" <?php echo $p->{'driver-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="driver-add" <?php echo $p->{'driver-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="driver-edit" <?php echo $p->{'driver-edit'} ? "checked" : ''; ?>>	
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="driver-delete" <?php echo $p->{'driver-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										                                      
                                    </td>
									<td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="driver-approved" <?php echo $p->{'driver-approved'} ? "checked" : ''; ?>>
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
								<tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("cab"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="cab-index" <?php echo $p->{'cab-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="cab-add" <?php echo $p->{'cab-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="cab-edit" <?php echo $p->{'cab-edit'} ? "checked" : ''; ?>>	
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="cab-delete" <?php echo $p->{'cab-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										                                      
                                    </td>
									<td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="cab-approved" <?php echo $p->{'cab-approved'} ? "checked" : ''; ?>>
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
								<tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("customer"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="customer-index" <?php echo $p->{'customer-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="customer-add" <?php echo $p->{'customer-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="customer-delete" <?php echo $p->{'customer-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										                                      
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="customer-view" <?php echo $p->{'customer-view'} ? "checked" : ''; ?>>
                                    </td>
								</tr>
								<tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("rides"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="rides-index" <?php echo $p->{'rides-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										
                                    </td>
                                    <td class="text-center">
										
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="rides-delete" <?php echo $p->{'rides-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										                                      
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="rides-view" <?php echo $p->{'rides-view'} ? "checked" : ''; ?>>
                                    </td>
								</tr>
								<tr>
                                    <td colspan="10"><?= lang("fares"); ?></td>
								</tr>
								<tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("city_rides"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="city_rides-index" <?php echo $p->{'city_rides-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="city_rides-add" <?php echo $p->{'city_rides-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="city_rides-edit" <?php echo $p->{'city_rides-edit'} ? "checked" : ''; ?>>	
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="city_rides-delete" <?php echo $p->{'city_rides-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										                                      
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
								<tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("outstation"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="outstation-index" <?php echo $p->{'outstation-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="outstation-add" <?php echo $p->{'outstation-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="outstation-edit" <?php echo $p->{'outstation-edit'} ? "checked" : ''; ?>>	
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="outstation-delete" <?php echo $p->{'outstation-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										                                      
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
								<tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("rentals"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="rentals-index" <?php echo $p->{'rentals-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="rentals-add" <?php echo $p->{'rentals-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="rentals-edit" <?php echo $p->{'rentals-edit'} ? "checked" : ''; ?>>	
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="rentals-delete" <?php echo $p->{'rentals-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										                                      
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
								<tr>
                                    <td colspan="10"><?= lang("options"); ?></td>
								</tr>
								<tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("settings"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="settings-index" <?php echo $p->{'settings-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										
                                    </td>
                                    <td class="text-center">
										
                                    </td>
                                    <td class="text-center">
										
                                    </td>
                                    <td class="text-center">
										                                      
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
								<tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("currency"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="currency-index" <?php echo $p->{'currency-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="currency-add" <?php echo $p->{'currency-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="currency-edit" <?php echo $p->{'currency-edit'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="currency-delete" <?php echo $p->{'currency-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="currency-status" <?php echo $p->{'currency-status'} ? "checked" : ''; ?>>                              
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
								<tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("cancel_master"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="cancel_master-index" <?php echo $p->{'cancel_master-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="cancel_master-add" <?php echo $p->{'cancel_master-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="cancel_master-edit" <?php echo $p->{'cancel_master-edit'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="cancel_master-delete" <?php echo $p->{'cancel_master-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="cancel_master-status" <?php echo $p->{'cancel_master-status'} ? "checked" : ''; ?>>                                  
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
								<tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("discount"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="discount-index" <?php echo $p->{'discount-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="discount-add" <?php echo $p->{'discount-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="discount-edit" <?php echo $p->{'discount-edit'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="discount-delete" <?php echo $p->{'discount-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="discount-status" <?php echo $p->{'discount-status'} ? "checked" : ''; ?>>                             
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
								<tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("company"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="company-index" <?php echo $p->{'company-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="company-add" <?php echo $p->{'company-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="company-delete" <?php echo $p->{'company-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="company-status" <?php echo $p->{'company-status'} ? "checked" : ''; ?>>                             
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
								<tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("wallet_offer"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="wallet_offer-index" <?php echo $p->{'wallet_offer-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="wallet_offer-add" <?php echo $p->{'wallet_offer-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="wallet_offer-edit" <?php echo $p->{'wallet_offer-edit'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="wallet_offer-delete" <?php echo $p->{'wallet_offer-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="wallet_offer-status" <?php echo $p->{'wallet_offer-status'} ? "checked" : ''; ?>>                             
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
								<tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("bank"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="bank-index" <?php echo $p->{'bank-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="bank-add" <?php echo $p->{'bank-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="bank-edit" <?php echo $p->{'bank-edit'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="bank-delete" <?php echo $p->{'bank-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="bank-status" <?php echo $p->{'bank-status'} ? "checked" : ''; ?>>                             
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
								<tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("payment_gateway"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="payment_gateway-index" <?php echo $p->{'payment_gateway-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="payment_gateway-add" <?php echo $p->{'payment_gateway-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="payment_gateway-edit" <?php echo $p->{'payment_gateway-edit'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="payment_gateway-delete" <?php echo $p->{'payment_gateway-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="payment_gateway-status" <?php echo $p->{'payment_gateway-status'} ? "checked" : ''; ?>>                             
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
								<tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("tax"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="tax-index" <?php echo $p->{'tax-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="tax-add" <?php echo $p->{'tax-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="tax-edit" <?php echo $p->{'tax-edit'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="tax-delete" <?php echo $p->{'tax-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="tax-status" <?php echo $p->{'tax-status'} ? "checked" : ''; ?>>                             
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
								<tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("cab_type"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="cab_type-index" <?php echo $p->{'cab_type-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="cab_type-add" <?php echo $p->{'cab_type-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="cab_type-edit" <?php echo $p->{'cab_type-edit'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="cab_type-delete" <?php echo $p->{'cab_type-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="cab_type-status" <?php echo $p->{'cab_type-status'} ? "checked" : ''; ?>>                             
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
								<tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("cab_make"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="cab_make-index" <?php echo $p->{'cab_make-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="cab_make-add" <?php echo $p->{'cab_make-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="cab_make-edit" <?php echo $p->{'cab_make-edit'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="cab_make-delete" <?php echo $p->{'cab_make-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="cab_make-status" <?php echo $p->{'cab_make-status'} ? "checked" : ''; ?>>                             
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
								<tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("cab_model"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="cab_model-index" <?php echo $p->{'cab_model-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="cab_model-add" <?php echo $p->{'cab_model-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="cab_model-edit" <?php echo $p->{'cab_model-edit'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="cab_model-delete" <?php echo $p->{'cab_model-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="cab_model-status" <?php echo $p->{'cab_model-status'} ? "checked" : ''; ?>>                             
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
								<tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("cab_fuel"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="cab_fuel-index" <?php echo $p->{'cab_fuel-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="cab_fuel-add" <?php echo $p->{'cab_fuel-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="cab_fuel-edit" <?php echo $p->{'cab_fuel-edit'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="cab_fuel-delete" <?php echo $p->{'cab_fuel-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="cab_fuel-status" <?php echo $p->{'cab_fuel-status'} ? "checked" : ''; ?>>                             
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
								<tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("continents"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="continents-index" <?php echo $p->{'continents-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="continents-add" <?php echo $p->{'continents-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="continents-edit" <?php echo $p->{'continents-edit'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="continents-delete" <?php echo $p->{'continents-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										                       
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
								<tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("countries"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="countries-index" <?php echo $p->{'countries-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="countries-add" <?php echo $p->{'countries-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="countries-edit" <?php echo $p->{'countries-edit'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="countries-delete" <?php echo $p->{'countries-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										                        
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
								<tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("zone"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="zone-index" <?php echo $p->{'zone-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="zone-add" <?php echo $p->{'zone-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="zone-edit" <?php echo $p->{'zone-edit'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="zone-delete" <?php echo $p->{'zone-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										                       
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
								<tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("state"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="state-index" <?php echo $p->{'state-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="state-add" <?php echo $p->{'state-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="state-edit" <?php echo $p->{'state-edit'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="state-delete" <?php echo $p->{'state-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										                       
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
								<tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("city"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="city-index" <?php echo $p->{'city-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="city-add" <?php echo $p->{'city-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="city-edit" <?php echo $p->{'city-edit'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="city-delete" <?php echo $p->{'city-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										                       
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
								<tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("areas"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="areas-index" <?php echo $p->{'areas-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="areas-add" <?php echo $p->{'areas-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="areas-edit" <?php echo $p->{'areas-edit'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="areas-delete" <?php echo $p->{'areas-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										                       
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
								<tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("pincode"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="pincode-index" <?php echo $p->{'pincode-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="pincode-add" <?php echo $p->{'pincode-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="pincode-edit" <?php echo $p->{'pincode-edit'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="pincode-delete" <?php echo $p->{'pincode-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										                       
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
								<tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("help"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="help-index" <?php echo $p->{'help-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="help-add" <?php echo $p->{'help-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="help-edit" <?php echo $p->{'help-edit'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										
                                    </td>
                                    <td class="text-center">
										<input type="checkbox" value="1" class="checkbox" name="help-status" <?php echo $p->{'help-status'} ? "checked" : ''; ?>>                    
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
								<tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("import_csv_common_cab"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="import_csv_common_cab-index" <?php echo $p->{'products-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										
                                    </td>
                                    <td class="text-center">
										
                                    </td>
                                    <td class="text-center">
										
                                    </td>
                                    <td class="text-center">
										               
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
								<tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("import_csv_common_location"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="import_csv_common_location-index" <?php echo $p->{'products-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										
                                    </td>
                                    <td class="text-center">
										
                                    </td>
                                    <td class="text-center">
										
                                    </td>
                                    <td class="text-center">
										               
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
								<tr>
                                    <td colspan="10"><?= lang("wallets"); ?></td>
								</tr>
								<tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("dashboard"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="wallets_dashboard-index" <?php echo $p->{'wallets_dashboard-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										
                                    </td>
                                    <td class="text-center">
										
                                    </td>
                                    <td class="text-center">
										
                                    </td>
                                    <td class="text-center">
										                                      
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
								<tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("customer"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="wallets_customer-index" <?php echo $p->{'wallets_customer-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										
                                    </td>
                                    <td class="text-center">
										
                                    </td>
                                    <td class="text-center">
										
                                    </td>
                                    <td class="text-center">
										                                      
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
								<tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("driver"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="wallets_driver-index" <?php echo $p->{'wallets_driver-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										
                                    </td>
                                    <td class="text-center">
										
                                    </td>
                                    <td class="text-center">
										
                                    </td>
                                    <td class="text-center">
										                                      
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
								<tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("owner"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="wallets_owner-index" <?php echo $p->{'wallets_owner-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										
                                    </td>
                                    <td class="text-center">
										
                                    </td>
                                    <td class="text-center">
										
                                    </td>
                                    <td class="text-center">
										                                      
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
                                <tr>
                                    <td colspan="10"><?= lang("incentives"); ?></td>
								</tr>
								<tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("list"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="incentive_list-index" <?php echo $p->{'incentive_list-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="incentive_list-add" <?php echo $p->{'incentive_list-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="incentive_list-edit" <?php echo $p->{'incentive_list-edit'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										
                                    </td>
                                    <td class="text-center">
										                                      
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="incentive_list-view" <?php echo $p->{'incentive_list-view'} ? "checked" : ''; ?>>
                                    </td>
								</tr>
                                <tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("group"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="incentive_group-index" <?php echo $p->{'incentive_group-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="incentive_group-add" <?php echo $p->{'incentive_group-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="incentive_group-edit" <?php echo $p->{'incentive_group-edit'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="incentive_group-delete" <?php echo $p->{'incentive_group-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										                                      
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
                                <tr>
                                    <td colspan="10"><?= lang("offers"); ?></td>
								</tr>
								<tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("list"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="offers_list-index" <?php echo $p->{'offers_list-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        
                                    </td>
                                    <td class="text-center">
                                       
                                    </td>
                                    <td class="text-center">
										
                                    </td>
                                    <td class="text-center">
										                                      
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
                                <tr>
                                    <td colspan="10"><?= lang("crm"); ?></td>
								</tr>
								<tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("dashboard"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="crm_dashboard-index" <?php echo $p->{'crm_dashboard-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="crm_dashboard-add" <?php echo $p->{'crm_dashboard-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="crm_dashboard-edit" <?php echo $p->{'crm_dashboard-edit'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
										
                                    </td>
                                    <td class="text-center">
										                                      
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
                                <tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("enquiry"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="crm_enquiry-index" <?php echo $p->{'crm_enquiry-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                       
                                    </td>
                                    <td class="text-center">
                                       
                                    </td>
                                    <td class="text-center">
										
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="crm_enquiry-status" <?php echo $p->{'crm_enquiry-status'} ? "checked" : ''; ?>>                                  
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="crm_enquiry-view" <?php echo $p->{'crm_enquiry-view'} ? "checked" : ''; ?>>
                                    </td>
								</tr>
                                <tr>
                                    <td colspan="10"><?= lang("booking_rides"); ?></td>
								</tr>
								<tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("dashboard"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="booking_rides_dashboard-index" <?php echo $p->{'booking_rides_dashboard-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="booking_rides_dashboard-add" <?php echo $p->{'booking_rides_dashboard-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                       
                                    </td>
                                    <td class="text-center">
										
                                    </td>
                                    <td class="text-center">
										                                      
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
                                <tr>
                                    <td colspan="10"><?= lang("accounts"); ?></td>
								</tr>
                                <tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("dashboard"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="accounts_dashboard-index" <?php echo $p->{'accounts_dashboard-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        
                                    </td>
                                    <td class="text-center">
                                       
                                    </td>
                                    <td class="text-center">
										
                                    </td>
                                    <td class="text-center">
										                                      
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
                                <tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("settlement_branch"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="settlement_branch-index" <?php echo $p->{'settlement_branch-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        
                                    </td>
                                    <td class="text-center">
                                       
                                    </td>
                                    <td class="text-center">
										
                                    </td>
                                    <td class="text-center">
										                                      
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
                                <tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("account_settlementlist"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="account_settlementlist-index" <?php echo $p->{'account_settlementlist-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        
                                    </td>
                                    <td class="text-center">
                                       
                                    </td>
                                    <td class="text-center">
										
                                    </td>
                                    <td class="text-center">
										                                      
                                    </td>
									<td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="account_settlementlist-approved" <?php echo $p->{'account_settlementlist-approved'} ? "checked" : ''; ?>>
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
                                <tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("account_owner"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="account_owner-index" <?php echo $p->{'account_owner-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        
                                    </td>
                                    <td class="text-center">
                                       
                                    </td>
                                    <td class="text-center">
										
                                    </td>
                                    <td class="text-center">
										                                      
                                    </td>
									<td class="text-center">
                                        
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
                                <tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("bank_excel"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="bank_excel-index" <?php echo $p->{'bank_excel-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        
                                    </td>
                                    <td class="text-center">
                                       
                                    </td>
                                    <td class="text-center">
										
                                    </td>
                                    <td class="text-center">
										                                      
                                    </td>
									<td class="text-center">
                                        
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
                                <tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("reconcilation"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="reconcilation-index" <?php echo $p->{'reconcilation-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        
                                    </td>
                                    <td class="text-center">
                                       
                                    </td>
                                    <td class="text-center">
										
                                    </td>
                                    <td class="text-center">
										                                      
                                    </td>
									<td class="text-center">
                                        
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
                                <tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("trip"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="trip-index" <?php echo $p->{'trip-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        
                                    </td>
                                    <td class="text-center">
                                       
                                    </td>
                                    <td class="text-center">
										
                                    </td>
                                    <td class="text-center">
										                                      
                                    </td>
									<td class="text-center">
                                        
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
                                <tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("driver_payment"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="driver_payment-index" <?php echo $p->{'driver_payment-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        
                                    </td>
                                    <td class="text-center">
                                       
                                    </td>
                                    <td class="text-center">
										
                                    </td>
                                    <td class="text-center">
										                                      
                                    </td>
									<td class="text-center">
                                        
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
                                <tr>
                                    <td colspan="10"><?= lang("reports"); ?></td>
								</tr>
                                <tr>
                                    <td><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?= lang("dashboard"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="reports_dashboard-index" <?php echo $p->{'reports_dashboard-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        
                                    </td>
                                    <td class="text-center">
                                       
                                    </td>
                                    <td class="text-center">
										
                                    </td>
                                    <td class="text-center">
										                                      
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
										
                                    </td>
									<td class="text-center">
                                        
                                    </td>
								</tr>
						</tbody>
					</table>
                    <div class="col-sm-12 last_sa_se"><?php echo form_submit('add_permission', lang('submit'), 'class="btn btn-primary  change_btn_save center-block"'); ?></div>
                    <?php echo form_close(); ?>
				</div>

                <?php
                }else{
                ?>
                <div class="instance_country">
                	<div class="form-group col-sm-3 <?php if($this->session->userdata('group_id') != 1){ echo 'hidden'; } ?>">
						<?php echo lang('instance_of_country', 'instance_of_country'); ?>
                        <select <?php if($this->session->userdata('group_id') == 1){ echo 'required'; } ?> class="form-control select is_country" name="is_country" id="is_country">
                            <option value="">Select Country</option>
                            <?php
                            foreach($AllCountrys as $AllCountry){
                            ?>
                            <option value="<?= $AllCountry->iso ?>" <?php if($AllCountry->iso == $_GET['is_country']){ echo 'selected'; }else{ echo ''; } ?>><?= $AllCountry->name ?></option>
                            <?php
                            }
                            ?>
                        </select>
					</div>

                    <div class="form-group col-sm-3 col-xs-12">
                        <?php echo lang('department', 'department'); ?>
                        <?php
                        $dep[''] = 'Select Department';
                        foreach ($user_department as $department) {
                            $dep[$department->id] = $department->name;
                        }
                        
                        echo form_dropdown('department_id', $dep, '', 'class="tip form-control" id="department_id" data-placeholder="' . lang("select") . ' ' . lang("department") . '" required="required"');
                        ?>
                    </div>
                    
                    <div class="form-group col-sm-3 col-xs-12">
                        <?php echo lang('designation', 'designation'); ?>
                        <?php
                        $des[''] = 'Select Designation';
                        foreach ($user_designation as $designation) {
                            $des[$designation->id] = $designation->position;
                        }
                        
                        echo form_dropdown('designation_id', $des, '', 'class="tip form-control select-designation" id="designation_id" data-placeholder="' . lang("select") . ' ' . lang("designation") . '" required="required"');
                        ?>
                    </div>

                    <div class="col-sm-3">&nbsp;
                    <label>&nbsp;</label>
                    <button type="button" id="permission" class="btn btn-primary btn-block change_btn_save">Submit</button>
                   
                </div>
                <?php
                }
                ?>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
    $('#permission').click(function () {
        var is_country = $('#is_country').val();
        var department_id  = $('#department_id').val();
        var designation_id = $('#designation_id').val();
        if(is_country != '' && department_id != '' && designation_id != ''){
        var site = '<?php echo site_url() ?>';
            window.location.href = site+"admin/users/permission?is_country="+is_country+"&department_id="+department_id+"&designation_id="+designation_id;
        }else{
            alert('please select all feild')
        }

    })
});
</script>