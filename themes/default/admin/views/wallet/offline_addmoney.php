<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="box">
    <?php /*?><div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('driver_payment_status'); ?></h2>
    </div><?php */?>
    <div class="box-content">
    
   
            <div class="row">
           	
            <div class="col-lg-12">
            	<?php $attrib = array('class' => 'form-horizontal','class' => 'edit_from', 'data-toggle' => 'validator', 'id' => 'razorpay-form', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("wallet/offline_addmoney/".$group_id, $attrib);
                ?>
                
                <div class="row">
					<div class="col-md-12">    	
						<h2 class="box_he_de"><?= lang('payment_details') ?></h2>
							<div class="col-md-12">
								<input type="hidden" name="is_country" id="user_id" value="<?= $is_country ?>" />
                                <input type="hidden" name="user_id" id="user_id" value="<?= $user_id ?>" />
                                <input type="hidden" name="offer" id="offer" value="<?= $offer ? $offer : 0 ?>" />
                                <input type="hidden" name="group_id" id="group_id" value="<?= $group_id ?>" />
                                <input type="hidden" name="paid_amount" id="paid_amount" value="<?= $paid_amount ?>" />
                                <input type="hidden" name="payment_gateway" id="payment_gateway" value="<?= $payment_gateway ? $payment_gateway : 0 ?>" />
                                <input type="hidden" name="payment_mode" id="payment_mode" value="<?= $payment_mode ? $payment_mode : 0 ?>" />
                                
                               
						
							<div class="well text-center col-lg-12">
                            	<h1><?=$paid_amount ?></h1>
                                
                                <div class="form-group all col-sm-3 col-xs-12">
								<?= lang("transaction_image", "transaction_image") ?>
                                <input id="transaction_image" <?= $payment_gateway == 0 ? 'disabled' : '' ?> type="file" data-browse-label="<?= lang('browse'); ?>" name="transaction_image" data-show-upload="false"
                                       data-show-preview="false" class="form-control file" accept="im/*">
                            	</div>
                            
                                <div class="form-group col-sm-3 col-xs-12">
                                    <?php echo lang('transaction_no', 'transaction_no'); ?>
                                    <div class="controls">
                                        <input type="text" id="transaction_no" value="<?= $payment_gateway == 0 ? 'TRANS'.date('YmdHis') : '' ?>" name="transaction_no" <?= $payment_gateway == 0 ? 'readonly' : '' ?> class="form-control" required="required"/>
                                    </div>
                                    
                                </div>
                                
                                <div class="form-group col-sm-3 col-xs-12">
                                    <?php echo lang('transaction_date', 'transaction_date'); ?>
                                    <div class="controls">
                                        <input type="text" value="<?= $payment_gateway == 0 ? date('Y-m-d') : '' ?>" id="transaction_date" name="transaction_date" <?= $payment_gateway == 0 ? 'readonly' : '' ?> class="form-control"  required="required"/>
                                    </div>
                                    
                                </div>
                                
                                <div class="form-group col-md-3 col-xs-12">
									<?= lang("companys", "companys"); ?>
                                   <?php
								   $c[''] = 'Select Companys';
									foreach ($companys as $company) {
										if($company->is_office == 1){
											$company_name = $company->name.' - Branch Office';
										}else{
											$company_name = $company->name.' - Head Office';
										}
										$c[$company->id] = $company_name;
									}
                                    echo form_dropdown('company_id', $c, '', 'class="form-control select" id="company_id" required="required"'); ?>
                                </div>

							</div>
					</div>
                 </div>
                 
                <div class="col-sm-12 last_sa_se">
                <input type="submit" name="offline_submit" value="Pay Now" class="btn btn-primary  change_btn_save center-block offline_submit" />
                </div>
                
                
                
                <?php echo form_close(); ?>
            </div>

        </div>
    </div>
    
</div>
<style>
.switch-field {
  display: inline;
}

.switch-title {
  margin-bottom: 6px;
}

.switch-field input {
    position: absolute !important;
    clip: rect(0, 0, 0, 0);
    height: 1px;
    width: 1px;
    border: 0;
    overflow: hidden;
}

.switch-field label {
  float: left;
}

.switch-field label {
  display: inline-block;
  width: 35px;
  background-color: #fffff;
  color: #000000;
  font-size: 14px;
  font-weight: normal;
  text-align: center;
  text-shadow: none;
  padding: 3px 5px;
  border: 1px solid rgba(0, 0, 0, 0.2);
  -webkit-box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.3), 0 1px rgba(255, 255, 255, 0.1);
  box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.3), 0 1px rgba(255, 255, 255, 0.1);
  -webkit-transition: all 0.1s ease-in-out;
  -moz-transition:    all 0.1s ease-in-out;
  -ms-transition:     all 0.1s ease-in-out;
  -o-transition:      all 0.1s ease-in-out;
  transition:         all 0.1s ease-in-out;
}

.switch-field label:hover {
	cursor: pointer;
}

.switch-field input:checked + label {
  background-color: #2489c5;
  -webkit-box-shadow: none;
  box-shadow: none;
  color: #fff;
}

.switch-field label:first-of-type {
  border-radius: 13px 0 0 13px;
}

.switch-field label:last-of-type {
  border-radius: 0 13px 13px 0;
}
</style>


<script>
$(document).ready(function(e) {
    var dateFormat =  "dd/mm/yy";
		
	var start_date = $("#transaction_date") .datepicker({
       defaultDate: "+1w",
	   
	   dateFormat: "yy-mm-dd" ,
		changeMonth: true,
		changeYear: true,
		
		maxDate: 0,
		numberOfMonths: 1,
		yearRange: '-100:+0',
		
	})
});

</script>
