<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
   <?php /*?> <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('driver_payment_status'); ?></h2>
    </div><?php */?>
    <div class="box-content">
        <div class="row">
           	
            <div class="col-lg-12">
            	<?php $attrib = array('class' => 'form-horizontal','class' => 'edit_from', 'data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("account/admin_to_driver/".$status."/".$id, $attrib);
                ?>
                
                <div class="row">
					<div class="col-md-12">    	
						<h2 class="box_he_de"><?= lang('payment_details') ?></h2>
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
							<?php echo lang('payment', 'Payment'); ?>
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
								<input type="text" id="payment_amount" value="<?= $payment->payment_amount ?>" readonly name="payment_amount" class="form-control"/>
							</div>
						</div>

						<div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('paid_amount', 'paid_amount'); ?>
							<div class="controls">
								<input type="text" id="paid_amount" value="<?= $payment->paid_amount ?>" readonly  name="paid_amount" class="form-control"/>
							</div>
						</div>

						<div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('balance_amount', 'balance_amount'); ?>
							<div class="controls">
								<input type="text" id="balance_amount" readonly value="<?= $payment->balance_amount ?>"  name="balance_amount" class="form-control"/>
							</div>
						</div>


						<div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('payment_date', 'payment_date'); ?>
							<div class="controls">
								<input type="text" id="payment_date" name="payment_date" disabled  class="form-control" value="<?= $payment->payment_date == '0000-00-00' || $payment->payment_date == NULL  ? '' : $payment->payment_date ?>" required="required"/>
							</div>
							<p class="help-block-small"><?= lang('ex_date') ?></p>
						</div>

						<div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('admin_account_no', 'admin_account_no'); ?>
							<?php
							$aa[''] = 'Select Payment';
							foreach ($banks as $bank) {
								$aa[$bank->account_no] = $bank->account_holder_name.'('.$bank->account_no.')';
							}

							echo form_dropdown('admin_account_no', $aa, '', 'class="tip form-control" id="admin_account_no" data-placeholder="' . lang("select") . ' ' . lang("account_no") . '"');
							?>
						</div>

						<div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('deposit_bank_name', 'deposit_bank_name'); ?>
							<div class="controls">
								<input type="text" id="deposit_bank_name" readonly onkeyup="inputFirstUpper(this)" name="deposit_bank_name" value="" class="form-control"/>
							</div>
						</div>
						<div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('deposit_branch_name', 'deposit_branch_name'); ?>
							<div class="controls">
								<input type="text" id="deposit_branch_name" readonly onkeyup="inputFirstUpper(this)" name="deposit_branch_name" value="" class="form-control"/>
							</div>
						</div>
						<div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('deposit_ifscode', 'deposit_ifscode'); ?>
							<div class="controls">
								<input type="text" id="deposit_ifscode" readonly onkeyup="inputUpper(this)" name="deposit_ifscode" value="" class="form-control"/>
							</div>
							<p class="help-block-small"><?= lang('ex_ifsc') ?></p>
						</div>

						<div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('deposit_date', 'deposit_date'); ?>
							<div class="controls">
								<input type="text" id="deposit_date" name="deposit_date"   onkeypress="dateCheck(this);" class="form-control" value="<?= $payment->deposit_date == '0000-00-00' || $payment->deposit_date == NULL  ? '' : $payment->deposit_date ?>" required="required"/>
							</div>
							<p class="help-block-small"><?= lang('ex_date') ?></p>
						</div>

						  <div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('transaction_no', 'transaction_no'); ?>
							<div class="controls">
								<input type="text" id="transaction_no" onkeyup="inputUpper(this)" name="transaction_no" value="" class="form-control"/>
							</div>
						</div>


						<div class="form-group col-sm-3 col-xs-12">
							<div class="form-group all ">
								<?= lang("transaction_image", "transaction_image") ?>
								<input id="transaction_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="transaction_image" data-show-upload="false"
									   data-show-preview="false" class="form-control file" accept="im/*">
							</div>
							<div class="col-sm-12 img_box_se_head">
									<div class="img_box_se">
									   <a href="<?= $payment->transaction_image ?>" class="without-caption image-link magnifier-thumb-wrapper">
											<img src="<?= $payment->transaction_image ?>" class="img"  data-large-img-url="<?= $payment->transaction_image ?>" data-large-img-wrapper="preview">  
										</a>
									</div>
								</div>
								<button type="button" class="btn btn-info pull-right change_btn_sec" style="width: 100%;">
									<span class="pull-left">
									<input type="file" id="selectedFile" style="display: none;" />
									<input type="button" value="Change" onclick="document.getElementById('selectedFile').click();" class="change_btn_s" />
								   </span> <i class="fa fa-rotate-right pull-right"></i>
								</button>
								<div class="magnifier-preview" id="preview" style="width: 300px; height:300px;position: absolute;right: -80%;"></div>
						</div>
							</div>
						</div>
                 </div>
               <div class="col-sm-12 last_sa_se"><?php echo form_submit('verified', lang('submit'), 'class="btn btn-primary change_btn_save center-block"'); ?></div>
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
	$('.driver_and_driver').hide();
	$('#payment_status').select2({readonly:true});
	$('#payment_id').select2({readonly:true});
	
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


$(document).on('change', '#admin_account_no', function(){
	var account_no = $(this).val();
	
	$.ajax({
		type: 'POST',
		url: '<?=admin_url('account/getBank')?>',
		data: {account_no: account_no},
		dataType: "json",
		cache: false,
		success: function (res) {
			$('#deposit_bank_name').val(res.bank_name);
			$('#deposit_branch_name').val(res.branch_name);
			$('#deposit_ifscode').val(res.ifsc_code);
		}
	});
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