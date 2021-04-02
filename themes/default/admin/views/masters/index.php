
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-taxi"></i><?= lang('settings'); ?></h2>
       
    </div>
    <div class="box-content">
        <div class="row">
        
        	<?php
			
			if(!empty($countryCode)){
			?>
            
            <div class="col-lg-12">
            	<?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
                echo admin_form_open_multipart("masters/index/".$countryCode, $attrib);
                ?>
                
                
                <fieldset class="scheduler-border">
                    <legend class="scheduler-border"><?= lang('customer') ?></legend>
                    <!--<div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("driver_ride_accept"); ?></label>
                
                            <div class="controls">
                                <?php
                                $opt = array(0 => lang('no'), 1 => lang('yes'));
                                echo form_dropdown('driver_ride_accept', $opt, $dataSettings->driver_ride_accept, 'class="form-control tip" id="driver_ride_accept" required="required" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                    </div>-->

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("ride_cancel_allocated_another_driver"); ?></label>
                
                            <div class="controls">
                                <?php
                                $opt = array(0 => lang('no'), 1 => lang('yes'));
                                echo form_dropdown('ride_cancel_allocated_another_driver', $opt, $dataSettings->ride_cancel_allocated_another_driver, 'class="form-control tip" id="ride_cancel_allocated_another_driver" required="required" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("cancel_maximum_fare"); ?></label>
                
                            <div class="controls">
                                <?= form_input('cancel_maximum_fare', $dataSettings->cancel_maximum_fare, 'class="form-control tip" id="cancel_maximum_fare"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="ride_cancel_driver_on_the_way_km_fare_enable"><?= lang("ride_cancel_driver_on_the_way_km_fare_enable"); ?></label>
							<div class="controls">
                                <?php
                                $opt = array(0 => lang('no'), 1 => lang('yes'));
                                echo form_dropdown('ride_cancel_driver_on_the_way_km_fare_enable', $opt, $dataSettings->ride_cancel_driver_on_the_way_km_fare_enable, 'class="form-control tip" id="ride_cancel_driver_on_the_way_km_fare_enable" required="required" style="width:100%;"');
                                ?>
                            </div>
                           
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="ride_cancel_driver_on_the_way_km_fare_enable"><?= lang("ride_cancel_driver_on_the_way_percentage_enable"); ?></label>
							<div class="controls">
                                <?php
                                $opt = array(0 => lang('no'), 1 => lang('yes'));
                                echo form_dropdown('ride_cancel_driver_on_the_way_percentage_enable', $opt, $dataSettings->ride_cancel_driver_on_the_way_percentage_enable, 'class="form-control tip" id="ride_cancel_driver_on_the_way_percentage_enable" required="required" style="width:100%;"');
                                ?>
                            </div>
                           
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("ride_cancel_driver_on_the_way_percentage_value"); ?></label>
                
                            <div class="controls">
                                <?= form_input('ride_cancel_driver_on_the_way_percentage_value', $dataSettings->ride_cancel_driver_on_the_way_percentage_value, 'class="form-control tip" id="ride_cancel_driver_on_the_way_percentage_value"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="driver_working_hours_limit"><?= lang("driver_working_hours_limit"); ?></label>
                
                            <div class="controls">
                                <?= form_input('driver_working_hours_limit', $dataSettings->driver_working_hours_limit, 'class="form-control tip" id="driver_working_hours_limit"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("estimate_fare_enable"); ?></label>
                
                            <div class="controls">
                                <?php
                                $opt = array(0 => lang('no'), 1 => lang('yes'));
                                echo form_dropdown('estimate_fare_enable', $opt, $dataSettings->estimate_fare_enable, 'class="form-control tip" id="estimate_fare_enable" required="required" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("search_kilometer"); ?></label>
                
                            <div class="controls">
                                <?= form_input('search_kilometer', $dataSettings->search_kilometer, 'class="form-control tip" id="search_kilometer"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                    	<div class="form-group">
                                <?php echo lang('camera_enable', 'camera_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('all'), '1' => lang('camera'), '2' => lang('gallery'));
                                echo form_dropdown('camera_enable', $ge, (isset($_POST['camera_enable']) ? $_POST['camera_enable'] : 0), 'class="tip form-control" id="camera_enable" data-placeholder="' . lang("select") . ' ' . lang("camera_enable") . '" required="required"');
                                ?>
                            </div>
                    </div>
                    <!--<div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("outstation_min_kilometer"); ?></label>
                
                            <div class="controls">
                                <?= form_input('outstation_min_kilometer', $dataSettings->outstation_min_kilometer, 'class="form-control tip" id="outstation_min_kilometer"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("rental_max_kilometer"); ?></label>
                
                            <div class="controls">
                                <?= form_input('rental_max_kilometer', $dataSettings->rental_max_kilometer, 'class="form-control tip" id="rental_max_kilometer"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("cityride_max_kilometer"); ?></label>
                
                            <div class="controls">
                                <?= form_input('cityride_max_kilometer', $dataSettings->cityride_max_kilometer, 'class="form-control tip" id="cityride_max_kilometer"'); ?>
                            </div>
                        </div>
                    </div> -->

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("riding_kilometer"); ?></label>
                
                            <div class="controls">
                                <?= form_input('cityride_max_kilometer', $dataSettings->cityride_max_kilometer, 'class="form-control tip" id="cityride_max_kilometer"'); ?>
                            </div>
                        </div>
                    </div>
                    
                    
                    <!--<div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("outstation_min_balance"); ?></label>
                
                            <div class="controls">
                                <?= form_input('outstation_min_balance', $dataSettings->outstation_min_balance, 'class="form-control tip" id="outstation_min_balance"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("rental_min_balance"); ?></label>
                
                            <div class="controls">
                                <?= form_input('rental_min_balance', $dataSettings->rental_min_balance, 'class="form-control tip" id="rental_min_balance"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("cityride_min_balance"); ?></label>
                
                            <div class="controls">
                                <?= form_input('cityride_min_balance', $dataSettings->cityride_min_balance, 'class="form-control tip" id="cityride_min_balance"'); ?>
                            </div>
                        </div>
                    </div>-->
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("wallet_min_add_money"); ?></label>
                
                            <div class="controls">
                                <?= form_input('wallet_min_add_money', $dataSettings->wallet_min_add_money, 'class="form-control tip" id="wallet_min_add_money"'); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("waiting_time"); ?></label>
                
                            <div class="controls">
                                <?= form_input('waiting_time', $dataSettings->waiting_time, 'class="form-control tip" id="waiting_time"'); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("waiting_charges"); ?></label>
                
                            <div class="controls">
                                <?= form_input('waiting_charges', $dataSettings->waiting_charges, 'class="form-control tip" id="waiting_charges"'); ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="trafic_distance"><?= lang("trafic_distance"); ?></label>
                
                            <div class="controls">
                                <?= form_input('trafic_distance', $dataSettings->trafic_distance, 'class="form-control tip" id="trafic_distance"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("drop_points_limit"); ?></label>
                
                            <div class="controls">
                            	<?php
                                $dp = array(1 => lang('1'), 2 => lang('2'), 3 => lang('3'));
                                echo form_dropdown('drop_points', $dp, $dataSettings->drop_points, 'class="form-control tip" id="drop_points" required="required" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                    </div>

                </fieldset>
                
                <fieldset class="scheduler-border">
                    <legend class="scheduler-border"><?= lang('customer_refercode') ?></legend>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("amount"); ?></label>
                
                            <div class="controls">
                                <?= form_input('customer_amount', $dataSettings->customer_amount, 'class="form-control tip" id="customer_amount"'); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("register_enable"); ?></label>
                
                            <div class="controls">
                                <?php
                                $opt = array(0 => lang('no'), 1 => lang('yes'));
                                echo form_dropdown('customer_user_reg', $opt, $dataSettings->customer_user_reg, 'class="form-control tip" id="customer_user_reg" required="required" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("rides_enable"); ?></label>
                
                            <div class="controls">
                                <?php
                                $opt = array(0 => lang('no'), 1 => lang('yes'));
                                echo form_dropdown('customer_rides', $opt, $dataSettings->customer_rides, 'class="form-control tip" id="customer_rides" required="required" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("rides_no"); ?></label>
                
                            <div class="controls">
                                <?= form_input('customer_rides_no', $dataSettings->customer_rides_no, 'class="form-control tip" id="customer_rides_no"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("code_validation"); ?></label>
                
                            <div class="controls">
                                <?= form_input('customer_validation', $dataSettings->customer_validation, 'class="form-control tip" id="customer_validation"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("using_type"); ?></label>
                
                            <div class="controls">
                                <?php
                                $opt = array(0 => lang('year'), 1 => lang('month'), '2' => lang('week'));
                                echo form_dropdown('customer_using_type', $opt, $dataSettings->customer_using_type, 'class="form-control tip" id="customer_using_type" required="required" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("using_members"); ?></label>
                
                            <div class="controls">
                                <?= form_input('customer_using_members', $dataSettings->customer_using_members, 'class="form-control tip" id="customer_using_members"'); ?>
                            </div>
                        </div>
                    </div>
                    
                    
                </fieldset>
                
                <fieldset class="scheduler-border">
                    <legend class="scheduler-border"><?= lang('driver_refercode') ?></legend>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("amount"); ?></label>
                
                            <div class="controls">
                                <?= form_input('driver_amount', $dataSettings->driver_amount, 'class="form-control tip" id="driver_amount"'); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("register_enable"); ?></label>
                
                            <div class="controls">
                                <?php
                                $opt = array(0 => lang('no'), 1 => lang('yes'));
                                echo form_dropdown('driver_user_reg', $opt, $dataSettings->driver_user_reg, 'class="form-control tip" id="driver_user_reg" required="required" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("rides_enable"); ?></label>
                
                            <div class="controls">
                                <?php
                                $opt = array(0 => lang('no'), 1 => lang('yes'));
                                echo form_dropdown('driver_rides', $opt, $dataSettings->driver_rides, 'class="form-control tip" id="driver_rides" required="required" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("rides_no"); ?></label>
                
                            <div class="controls">
                                <?= form_input('driver_rides_no', $dataSettings->driver_rides_no, 'class="form-control tip" id="driver_rides_no"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("code_validation"); ?></label>
                
                            <div class="controls">
                                <?= form_input('driver_validation', $dataSettings->driver_validation, 'class="form-control tip" id="driver_validation"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("using_type"); ?></label>
                
                            <div class="controls">
                                <?php
                                $opt = array(0 => lang('year'), 1 => lang('month'), '2' => lang('week'));
                                echo form_dropdown('driver_using_type', $opt, $dataSettings->driver_using_type, 'class="form-control tip" id="driver_using_type" required="required" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("using_members"); ?></label>
                
                            <div class="controls">
                                <?= form_input('driver_using_members', $dataSettings->driver_using_members, 'class="form-control tip" id="driver_using_members"'); ?>
                            </div>
                        </div>
                    </div>
                    
                    
                </fieldset>
                
                <fieldset class="scheduler-border">
                    <legend class="scheduler-border"><?= lang('customer_support') ?></legend>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="support_email"><?= lang("support_email"); ?></label>
                
                            <div class="controls">
                                <?= form_input('support_email', $dataSettings->support_email, 'class="form-control tip" id="support_email"'); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="support_mobile"><?= lang("support_mobile"); ?></label>
                
                            <div class="controls">
                                <?= form_input('support_mobile', $dataSettings->support_mobile, 'class="form-control tip" id="support_mobile"'); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="support_whatsapp"><?= lang("support_whatsapp"); ?></label>
                
                            <div class="controls">
                                <?= form_input('support_whatsapp', $dataSettings->support_whatsapp, 'class="form-control tip" id="support_whatsapp"'); ?>
                            </div>
                        </div>
                    </div>
                  
                </fieldset>
                
                <fieldset class="scheduler-border">
                    <legend class="scheduler-border"><?= lang('due_dates') ?></legend>
                    
                    
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="support_mobile"><?= lang("month"); ?></label>
                
                            <div class="controls">
                                <?= form_input('due_month', $dataSettings->due_month, 'class="form-control tip" id="due_month"'); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="support_whatsapp"><?= lang("year"); ?></label>
                
                            <div class="controls">
                                <?= form_input('due_year', $dataSettings->due_year, 'class="form-control tip" id="due_year"'); ?>
                            </div>
                        </div>
                    </div>
                  
                </fieldset>
                
                <fieldset class="scheduler-border">
                    <legend class="scheduler-border"><?= lang('booking_time') ?></legend>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="support_email"><?= lang("driver_time(seconds)"); ?></label>
                
                            <div class="controls">
                                <?= form_input('driver_time', $dataSettings->driver_time, 'class="form-control tip" maxlength="3" id="driver_time"'); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="support_mobile"><?= lang("driver_count"); ?></label>
                
                            <div class="controls">
                                <?= form_input('driver_count', $dataSettings->driver_count, 'class="form-control tip" maxlength="2" id="driver_count"'); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="support_whatsapp"><?= lang("customer_time(seconds)"); ?></label>
                
                            <div class="controls">
                                <?= form_input('customer_time', $dataSettings->customer_time, 'class="form-control tip" readonly id="customer_time"'); ?>
                            </div>
                        </div>
                    </div>
                  
                </fieldset>
                
                <fieldset class="scheduler-border">
                    <legend class="scheduler-border"><?= lang('account_module') ?></legend>
                    <!--<div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("driver_default_set_payment"); ?></label>
                
                            <div class="controls">
                                <?php
                                $dpw1 = array(0 => lang('wallet'), 1 => lang('offline'));
                                echo form_dropdown('driver_default_set_payment', $dpw1, $dataSettings->driver_default_set_payment, 'class="form-control tip" id="driver_default_set_payment" required="required" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                    </div>-->
                    <!--<div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("driver_admin_payment_option"); ?></label>
                
                            <div class="controls">
                                <?php
                                $opt1 = array(0 => 'Select Option', 1 => lang('day'), 2 => lang('week'), 3 => lang('month'));
                                echo form_dropdown('driver_admin_payment_option', $opt1, $dataSettings->driver_admin_payment_option, 'class="form-control tip" id="driver_admin_payment_option" required="required" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                    </div>-->
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("heyycab_commision_fee_percentage"); ?></label>
                
                            <div class="controls">
                                <?php 
								$opt2 = array(0 => 'Select Percentage');
								for($i=1; $i<=100; $i++){
									$opt2[] = $i;
								}
								echo form_dropdown('driver_admin_payment_percentage', $opt2, $dataSettings->driver_admin_payment_percentage, 'class="form-control tip" id="driver_admin_payment_percentage" required="required" style="width:100%;"');
								?>
                            </div>
                        </div>
                    </div>
                    
                    <!--<div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("driver_admin_payment_duration"); ?></label>
                
                            <div class="controls">
                                <?php 
								$v1 = array(0 => 'Select Duration');
								for($i=1; $i<=5; $i++){
									$v1[] = $i;
								}
								echo form_dropdown('driver_admin_payment_duration', $v1, $dataSettings->driver_admin_payment_duration, 'class="form-control tip" id="driver_admin_payment_duration" required="required" style="width:100%;"');
								?>
                            </div>
                        </div>
                    </div>-->
                    
                    <!--<div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("vendor_admin_payment_option"); ?></label>
                
                            <div class="controls">
                                <?php
                                $opt3 = array(0 => 'Select Option', 1 => lang('day'), 2 => lang('week'), 3 => lang('month'));
                                echo form_dropdown('vendor_admin_payment_option', $opt3, $dataSettings->vendor_admin_payment_option, 'class="form-control tip" id="vendor_admin_payment_option" required="required" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("vendor_admin_payment_percentage"); ?></label>
                
                            <div class="controls">
                                <?php 
								$opt4 = array(0 => 'Select Percentage');
								for($i=1; $i<=100; $i++){
									$opt4[] = $i;
								}
								echo form_dropdown('vendor_admin_payment_percentage', $opt4, $dataSettings->vendor_admin_payment_percentage, 'class="form-control tip" id="vendor_admin_payment_percentage" required="required" style="width:100%;"');
								?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("vendor_admin_payment_duration"); ?></label>
                
                            <div class="controls">
                                <?php 
								$v1 = array(0 => 'Select Duration');
								for($i=1; $i<=5; $i++){
									$v1[] = $i;
								}
								echo form_dropdown('vendor_admin_payment_duration', $v1, $dataSettings->vendor_admin_payment_duration, 'class="form-control tip" id="vendor_admin_payment_duration" required="required" style="width:100%;"');
								?>
                            </div>
                        </div>
                    </div>-->
                    
                    <!--<div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("driver_vendor_payment_option"); ?></label>
                
                            <div class="controls">
                                <?php
                                $opt5 = array(0 => 'Select Option', 1 => lang('day'), 2 => lang('week'), 3 => lang('month'));
                                echo form_dropdown('driver_vendor_payment_option', $opt5, $dataSettings->driver_vendor_payment_option, 'class="form-control tip" id="driver_vendor_payment_option" required="required" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("driver_vendor_payment_percentage"); ?></label>
                
                            <div class="controls">
                                <?php 
								$opt6 = array(0 => 'Select Percentage');
								for($i=1; $i<=100; $i++){
									$opt6[] = $i;
								}
								echo form_dropdown('driver_vendor_payment_percentage', $opt6, $dataSettings->driver_vendor_payment_percentage, 'class="form-control tip" id="driver_vendor_payment_percentage" required="required" style="width:100%;"');
								?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("driver_vendor_payment_duration"); ?></label>
                
                            <div class="controls">
                                <?php 
								$v1 = array(0 => 'Select Duration');
								for($i=1; $i<=5; $i++){
									$v1[] = $i;
								}
								echo form_dropdown('driver_vendor_payment_duration', $v1, $dataSettings->driver_vendor_payment_duration, 'class="form-control tip" id="driver_vendor_payment_duration" required="required" style="width:100%;"');
								?>
                            </div>
                        </div>
                    </div>-->
                    
                </fieldset>
                
                <fieldset class="scheduler-border">
                    <legend class="scheduler-border"><?= lang('sos') ?></legend>
                    
                     <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("support_no"); ?></label>
                
                            <div class="controls">
                                <?= form_input('help_number_one', $dataSettings->help_number_one, 'class="form-control tip" id="help_number_one"'); ?>
                            </div>
                        </div>
                    </div>
                     <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("alternate_no"); ?></label>
                
                            <div class="controls">
                                <?= form_input('help_number_two', $dataSettings->help_number_two, 'class="form-control tip" id="help_number_two"'); ?>
                            </div>
                        </div>
                    </div>
                     <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("alternate_no"); ?></label>
                
                            <div class="controls">
                                <?= form_input('help_number_three', $dataSettings->help_number_three, 'class="form-control tip" id="help_number_three"'); ?>
                            </div>
                        </div>
                    </div>
                     <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("alternate_no"); ?></label>
                
                            <div class="controls">
                                <?= form_input('help_number_four', $dataSettings->help_number_four, 'class="form-control tip" id="help_number_four"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("alternate_no"); ?></label>
                
                            <div class="controls">
                                <?= form_input('help_number_five', $dataSettings->help_number_five, 'class="form-control tip" id="help_number_five"'); ?>
                            </div>
                        </div>
                    </div>
                    
                   
                    
                </fieldset>
                
                <fieldset class="scheduler-border">
                    <legend class="scheduler-border"><?= lang('truck_shift') ?></legend>
                    
                    
                    
                    
                    
                    <div class="col-md-6">
                        <div class="form-group">
                        	<div class="col-md-12">
                            <label class="control-label" for="support_mobile"><?= lang("day_shift"); ?></label>
                			</div>
                             <div class="col-md-6">
                            <div class="controls">
                                <?= form_input('day_shift_from_time', $dataSettings->day_shift_from_time, 'class="form-control tip" id="day_shift_from_time"'); ?>
                            </div>
                            </div>
                             <div class="col-md-6">
                            <div class="controls">
                                <?= form_input('day_shift_to_time', $dataSettings->day_shift_to_time, 'class="form-control tip" id="day_shift_to_time" readonly'); ?>
                            </div>
                            </div>
                            
                        </div>
                    </div>
                    
                     <div class="col-md-6">
                        <div class="form-group">
                        	<div class="col-md-12">
                            <label class="control-label" for="support_mobile"><?= lang("night_shift"); ?></label>
                			</div>
                             <div class="col-md-6">
                            <div class="controls">
                                <?= form_input('night_shift_from_time', $dataSettings->night_shift_from_time, 'class="form-control tip" id="night_shift_from_time" readonly'); ?>
                            </div>
                            </div>
                             <div class="col-md-6">
                            <div class="controls">
                                <?= form_input('night_shift_to_time', $dataSettings->night_shift_to_time, 'class="form-control tip" id="night_shift_to_time" readonly'); ?>
                            </div>
                            </div>
                            
                        </div>
                    </div>
                    
                   
                </fieldset>
                
                <fieldset class="scheduler-border">
                    <legend class="scheduler-border"><?= lang('user') ?></legend>
                   
                    
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label" for="timezone"><?= lang("timezone"); ?></label>
                            <?php
							$tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
							?>
                            <select name="timezone" class="form-control" id="timezone" required>
                            	<?php
								foreach($tzlist as  $val){
									if($dataSettings->timezone == $val){
										$selected = 'selected';
									}else{
										$selected = '';
									}
								?>
                                <option value="<?php echo $val; ?>" <?= $selected ?> ><?php echo $val; ?></option>
                                <?php
								}
								?>
                            </select>
                           
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang("dateofbirth"); ?></label>
                
                            <div class="controls">
                                <?= form_input('dateofbirth', $dataSettings->dateofbirth, 'class="form-control tip" id="dateofbirth"'); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('login_otp_enable', 'login_otp_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('login_otp_enable', $ge, $dataSettings->login_otp_enable, 'class="tip form-control" id="login_otp_enable" ');
                                ?>
                            </div>
                    </div>
                    
                     <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('register_otp_enable', 'register_otp_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('register_otp_enable', $ge, $dataSettings->register_otp_enable, 'class="tip form-control" id="register_otp_enable" ');
                                ?>
                            </div>
                    </div>
                    
                    
                    
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('device_change_otp_enable', 'device_change_otp_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('device_change_otp_enable', $ge, $dataSettings->device_change_otp_enable, 'class="tip form-control" id="device_change_otp_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('ride_otp', 'ride_otp_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('ride_otp_enable', $ge, $dataSettings->ride_otp_enable, 'class="tip form-control" id="ride_otp_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('address', 'address_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('address_enable', $ge, $dataSettings->address_enable, 'class="tip form-control" id="address_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('account_holder_name', 'account_holder_name_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('account_holder_name_enable', $ge, $dataSettings->account_holder_name_enable, 'class="tip form-control" id="account_holder_name_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('bank_name', 'bank_name_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('bank_name_enable', $ge, $dataSettings->bank_name_enable, 'class="tip form-control" id="bank_name_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('branch_name', 'branch_name_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('branch_name_enable', $ge, $dataSettings->branch_name_enable, 'class="tip form-control" id="branch_name_enable" ');
                                ?>
                            </div>
                    </div>
                    
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('ifsc_code', 'ifsc_code_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('ifsc_code_enable', $ge, $dataSettings->ifsc_code_enable, 'class="tip form-control" id="ifsc_code_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('aadhaar', 'aadhaar_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('aadhaar_enable', $ge, $dataSettings->aadhaar_enable, 'class="tip form-control" id="aadhaar_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('pancard', 'pancard_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('pancard_enable', $ge, $dataSettings->pancard_enable, 'class="tip form-control" id="pancard_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('license', 'license_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('license_enable', $ge, $dataSettings->license_enable, 'class="tip form-control" id="license_enable" ');
                                ?>
                            </div>
                    </div><div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('police', 'police_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('police_enable', $ge, $dataSettings->police_enable, 'class="tip form-control" id="police_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('loan', 'loan_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('loan_enable', $ge, $dataSettings->loan_enable, 'class="tip form-control" id="loan_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('vendor', 'vendor_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('vendor_enable', $ge, $dataSettings->vendor_enable, 'class="tip form-control" id="vendor_enable" ');
                                ?>
                            </div>
                    </div>

                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('badge', 'badge_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('badge_enable', $ge, $dataSettings->badge_enable, 'class="tip form-control" id="badge_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('training_certificate', 'training_certificate_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('training_certificate_enable', $ge, $dataSettings->training_certificate_enable, 'class="tip form-control" id="training_certificate_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('experience_certificate', 'experience_certificate_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('experience_certificate_enable', $ge, $dataSettings->experience_certificate_enable, 'class="tip form-control" id="experience_certificate_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('medical_certificate', 'medical_certificate_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('medical_certificate_enable', $ge, $dataSettings->medical_certificate_enable, 'class="tip form-control" id="medical_certificate_enable" ');
                                ?>
                            </div>
                    </div>
                    <input type="hidden" name="police_verification_enable" value="1">
                    <!--<div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('police_verification', 'police_verification_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('police_verification_enable', $ge, $dataSettings->police_verification_enable, 'class="tip form-control" id="police_verification_enable" ');
                                ?>
                            </div>
                    </div>-->
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('health_insurance', 'health_insurance_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('health_insurance_enable', $ge, $dataSettings->health_insurance_enable, 'class="tip form-control" id="health_insurance_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('term_insurance', 'term_insurance_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('term_insurance_enable', $ge, $dataSettings->term_insurance_enable, 'class="tip form-control" id="term_insurance_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('additional_contact', 'additional_contact_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('additional_contact_enable', $ge, $dataSettings->additional_contact_enable, 'class="tip form-control" id="additional_contact_enable" ');
                                ?>
                            </div>
                    </div>
                    
                    
                </fieldset>
                
                <fieldset class="scheduler-border">
                    <legend class="scheduler-border"><?= lang('truck') ?></legend>
                    
                    
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('cab_registration', 'cab_registration_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('cab_registration_enable', $ge, $dataSettings->cab_registration_enable, 'class="tip form-control" id="cab_registration_enable" ');
                                ?>
                            </div>
                    </div>
                    
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('taxation', 'taxation_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('taxation_enable', $ge, $dataSettings->taxation_enable, 'class="tip form-control" id="taxation_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('insurance', 'insurance_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('insurance_enable', $ge, $dataSettings->insurance_enable, 'class="tip form-control" id="insurance_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('permit', 'permit_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('permit_enable', $ge, $dataSettings->permit_enable, 'class="tip form-control" id="permit_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('authorisation', 'authorisation_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('authorisation_enable', $ge, $dataSettings->authorisation_enable, 'class="tip form-control" id="authorisation_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('fitness', 'fitness_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('fitness_enable', $ge, $dataSettings->fitness_enable, 'class="tip form-control" id="fitness_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('speed', 'speed_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('speed_enable', $ge, $dataSettings->speed_enable, 'class="tip form-control" id="speed_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('puc', 'puc_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('puc_enable', $ge, $dataSettings->puc_enable, 'class="tip form-control" id="puc_enable" ');
                                ?>
                            </div>
                    </div>
                    
                    <div class="col-md-2">
                    	<div class="form-group">
                       
                                <?php echo lang('bodyweight', 'bodyweight'); ?>
                                <?php
                                $ge[''] = array('0' => lang('Tonne'), '1' => lang('Kilogram'));
                                echo form_dropdown('taxi_bodyweight_formate', $ge, $dataSettings->taxi_bodyweight_formate, 'class="tip form-control" id="taxi_bodyweight_formate" ');
                                ?>
                            </div>
                    </div>
                    
                    <div class="col-md-2">
                    	<div class="form-group">
                       
                                <?php echo lang('bodysize', 'bodysize'); ?>
                                <?php
                                $ge[''] = array('0' => lang('Foot'), '1' => lang('Inch'), '2' => lang('Centimetre'), '3' => lang('Metre'), '4' => lang('Kilometre'));
                                echo form_dropdown('taxi_bodysize_formate', $ge, $dataSettings->taxi_bodysize_formate, 'class="tip form-control" id="taxi_bodysize_formate" ');
                                ?>
                            </div>
                    </div>

                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('emission_norms', 'emission_norms_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('emission_norms_enable', $ge, $dataSettings->emission_norms_enable, 'class="tip form-control" id="emission_norms_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('vehicle_tracking', 'vehicle_tracking_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('vehicle_tracking_enable', $ge, $dataSettings->vehicle_tracking_enable, 'class="tip form-control" id="vehicle_tracking_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('fire_extinguisher', 'fire_extinguisher_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('fire_extinguisher_enable', $ge, $dataSettings->fire_extinguisher_enable, 'class="tip form-control" id="fire_extinguisher_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('child_lock_mechanism', 'child_lock_mechanism_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('child_lock_mechanism_enable', $ge, $dataSettings->child_lock_mechanism_enable, 'class="tip form-control" id="child_lock_mechanism_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('interior_vehicle', 'interior_vehicle_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('interior_vehicle_enable', $ge, $dataSettings->interior_vehicle_enable, 'class="tip form-control" id="interior_vehicle_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('taxi_roof_sign', 'taxi_roof_sign_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('taxi_roof_sign_enable', $ge, $dataSettings->taxi_roof_sign_enable, 'class="tip form-control" id="taxi_roof_sign_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('e_challans_clearance', 'e_challans_clearance_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('e_challans_clearance_enable', $ge, $dataSettings->e_challans_clearance_enable, 'class="tip form-control" id="e_challans_clearance_enable" ');
                                ?>
                            </div>
                    </div>
                    
                    
                </fieldset>
                    
                <div class="cleafix"></div>
                <div class="form-group">
                    <div class="controls">
                        <?= form_submit('update_settings', lang("update_settings"), 'class="btn btn-primary"'); ?>
                    </div>
                </div>
                <?= form_close(); ?>
            </div>
			<?php
			}else{
			?>
            <div class="form-group col-sm-4 <?php if($this->session->userdata('group_id') != 1){ echo 'hidden'; } ?>">
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
            <?php
			}
			?>
        </div>
    </div>
</div>
<script>
$(document).on('change', '#is_country', function(){
	
        var site = '<?php echo site_url() ?>';
		var is_country = $('#is_country').val();
	  window.location.href = site+"admin/masters/index?countryCode="+is_country;
		
    
});

$('#driver_time').bind('keyup paste', function(){
   this.value = this.value.replace(/[^1-9-0]/g, '');
   var driver_time  = this.value;
   var driver_count = $('#driver_count').val();
   var customer_time = driver_time * driver_count;
   $('#customer_time').val(customer_time);
   
});
$('#driver_count').bind('keyup paste', function(){
     this.value = this.value.replace(/[^1-9-0]/g, '');
	 var driver_count  = this.value;
	   var driver_time = $('#driver_time').val();
	   var customer_time = driver_time * driver_count;
	   $('#customer_time').val(customer_time);
});

/*$('#day_shift_from_time').change(function() {
  var date2 = $('#day_shift_from_time').datepicker('getDate'); 
  date2.setHours(date2.getHours()+10); 
    //alert(date2);
  $('#day_shift_to_time').val(date2);
  ('#night_shift_from_time').val(date2);
});*/
//day_shift_from_time
/*$(function () {
	$('#day_shift_from_time').datetimepicker({
		 format: 'hh:ii',
		 showMeridian: true,
         autoclose: true,
         todayBtn: true,
		
	}).on("change", function() {
		var oldDate = $(this).val();
		alert(oldDate);
		 oldDate.setHours(oldDate+10); 
		//var hour = oldDate;
		//var newDate = oldDate.setHours(hour + 12);
		$('#day_shift_to_time').val(oldDate);
		$('#night_shift_from_time').val(oldDate);
		//var tonewDate = newDate.setHours(hour + 12);
		$('#night_shift_to_time').val(oldDate);
	});
	
	//var hour = oldDate.getHours();
//var newDate = oldDate.setHours(hour + 1);

});*/
$(function () {
	$('#day_shift_from_time').datetimepicker({
		 format: 'hh:ii',
		 showMeridian: true,
         autoclose: true,
         todayBtn: true,
	});
	
});
</script>