<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    <?php /*?><div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('driver_payment_status'); ?></h2>
    </div><?php */?>
    <div class="box-content">
        <div class="row">
           	
            <div class="col-lg-12">
            	<?php $attrib = array('class' => 'form-horizontal','class' => 'edit_from', 'data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("account/driver_to_admin/active/".$id, $attrib);
                ?>
                
                <div class="row">
					<div class="col-md-12">    	
						<h2 class="box_he_de">Payment Details</h2>
							<div class="col-md-12">
							<input type="hidden" name="driver_id" value="<?= $id ?>">	
							 <input type="hidden" name="driver_payment_id" value="<?= $payment->id ?>">	

						<div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('total_ride', 'total_ride'); ?>
							<div class="controls">
								<input type="text" id="total_ride" value="<?= $payment->total_ride ?>" readonly name="total_ride" class="form-control"/>
							</div>
						</div>

						<div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('total_ride_amount', 'total_ride_amount'); ?>
							<div class="controls">
								<input type="text" id="total_ride_amount" value="<?= $payment->total_ride_amount ?>" readonly name="total_ride_amount" class="form-control"/>
							</div>
						</div>



						<div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('payment_status', 'payment_status'); ?>
							<?php
							$ge[''] = array('2' => lang('Offline'), '1' => lang('Online'));
							echo form_dropdown('payment_status', $ge, 2, 'class="tip form-control" id="payment_status" data-placeholder="' . lang("select") . ' ' . lang("payment_status") . '" readonly');
							?>
						</div>

						<div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('Payment', 'Payment'); ?>
							<?php
							$cc[''] = 'Select Payment';
							foreach ($payment_type as $ptype) {
								$cc[$ptype->id] = $ptype->name;
							}

							echo form_dropdown('payment_id', $cc, 1, 'class="tip form-control" id="payment_id" data-placeholder="' . lang("select") . ' ' . lang("payment") . '" readonly');
							?>
						</div>

						<div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('payment_amount', 'payment_amount').' ('.$payment->payment_percentage.'%)'; ?>
							<div class="controls">
                            	<?php
								$payment_amount = $payment->total_ride_amount * $payment->payment_percentage / 100;
								?>
								<input type="text" id="payment_amount" value="<?= round($payment_amount) ?>" readonly name="payment_amount" class="form-control"/>
							</div>
						</div>

						<div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('paid_amount', 'paid_amount'); ?>
							<div class="controls">
								<input type="text" id="paid_amount" value=""  name="paid_amount" class="form-control"/>
							</div>
						</div>

						<div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('balance_amount', 'balance_amount'); ?>
							<div class="controls">
								<input type="text" id="balance_amount" readonly value=""  name="balance_amount" class="form-control"/>
							</div>
						</div>


						<div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('payment_date', 'payment_date'); ?>
							<div class="controls">
								<input type="text" id="payment_date" name="payment_date" onkeypress="dateCheck(this);" class="form-control" value="<?= $driver_result->payment_date == '0000-00-00' || $driver_result->payment_date == NULL  ? '' : $driver_result->payment_date ?>" required="required"/>
							</div>
							<p class="help-block-small"><?= lang('ex_date') ?></p>
						</div>



							</div>
					</div>
                 </div>
                <div class="col-sm-12 last_sa_se"><?php echo form_submit('driver_status', lang('submit'), 'class="btn btn-primary change_btn_save center-block"'); ?></div>
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


   	
$(document).ready(function(){
	$('#payment_status').select2({readonly:true});
	$('#payment_id').select2({readonly:true});
	$('.driver_and_driver').hide();
	
	
	$('#operator').change(function(){
		$('.driver_and_driver').hide();
		
		if($('#operator').val() == 'driver_and_driver') {
            $('.driver_and_driver').show();
        } else {
			
            $('.driver_and_driver').hide();
        } 
	});
	
	$("#payment_status").select2("readonly", true);
	$("#payment_id").select2("readonly", true);
	
	
});
$(document).on('change', '#paid_amount', function(){
		var payment_amount = parseInt($('#payment_amount').val());
		var paid_amount = $(this).val();
		var balance_amount = 0;
		if(payment_amount <= paid_amount){
			balance_amount = paid_amount - payment_amount;
			$('#balance_amount').val(balance_amount);
		}else{
			$('#balance_amount').val(balance_amount);
			$('#paid_amount').val('');
		}
	});
</script>