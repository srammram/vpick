<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
$productinfo = 'Testing Payment';
$txnid = time();
$surl = $surl;
$furl = $furl;        
$key_id = RAZOR_KEY_ID;
$currency_code = $currency_code;            
$total = ($itemInfo['price']* 100); 
$amount = $itemInfo['price'];
$merchant_order_id = 'Hello';
$card_holder_name = 'TechArise Team';
$email = 'info@techarise.com';
$phone = '90000000011';
$name = 'HEYYCAB';
$return_url = admin_url().'account/callback';
?>
<div class="box">
    <?php /*?><div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('driver_payment_status'); ?></h2>
    </div><?php */?>
    <div class="box-content">
    
   
            <div class="row">
           	
            <div class="col-lg-12">
            	<?php $attrib = array('class' => 'form-horizontal','class' => 'edit_from', 'data-toggle' => 'validator', 'id' => 'razorpay-form', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("wallet/addmoney/".$group_id, $attrib);
                ?>
                
                <div class="row">
					<div class="col-md-12">    	
						<h2 class="box_he_de"><?= lang('payment_details') ?></h2>
							<div class="col-md-12">
							  <input type="hidden" name="group_id" value="<?= $group_id ?>">	
							  
                              
                            <div class="col-lg-3 <?php if($this->session->userdata('group_id') != 1){ echo 'hidden'; } ?>">
                            <div class="form-group">
								<?php echo lang('country', 'Country'); ?>
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
                            </div> 
                            
                            <div class="form-group col-sm-3 col-xs-12">
								<?php echo lang('Users', 'Users'); ?>
                                <?php
                                $u[''] = 'Select Users';
                                foreach ($users as $user) {
                                    $u[$user->id] = $user->first_name.'('.$user->mobile.')';
                                }
    
                                echo form_dropdown('user_id', $u, '', 'class="tip form-control" id="user_id" required data-placeholder="' . lang("select") . ' ' . lang("users") . '" ');
                                ?>
                            </div>
                            <?php
							if($group_id == 4){
							?>
                            <div class="form-group col-sm-3 col-xs-12">
								<?php echo lang('offer', 'Offer'); ?>
                               
                                
                                <select class="form-control select offer_id" name="offer_id" id="offer_id">
                                <option data-offer="0" value="0">Custom Recharge</option>
                                <?php
                                foreach ($walletoffers as $walletoffer) {
                                    if($walletoffer->type == 1){
                                      $type_name = 'City';
                                    }elseif($walletoffer->type == 2){
                                      $type_name = 'Rental';
                                    }elseif($walletoffer->type == 3){
                                      $type_name = 'Outstation';
                                    }else{
                                      $type_name = 'Custom offer - '.$walletoffer->offer_amount;
                                    }
                                ?>
                                <option  data-offer="<?= $walletoffer->offer_amount ?>" value="<?= $walletoffer->id ?>" ><?= $walletoffer->name.'('.$type_name.')' ?></option>
                                <?php
                                }
                                ?>
                                </select>
                                
                            </div>
                           <?php
							}else{
						   ?> 
                           <input type="hidden" name="offer_id" value="0" id="offer_id">
                           <?php
							}
						   ?>
						<div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('amount', 'amount'); ?>
							<div class="controls">
								<input type="text" id="paid_amount" value="" name="paid_amount" class="form-control" required/>
							</div>
						</div>

						
						
                        
                        <div class="form-group col-sm-3 col-xs-12 payment_gateway_id">
							<?php echo lang('payment_gateway', 'Payment Gateway'); ?>
							<?php
							$pg[''] = 'Select Payment Gateway';
							foreach ($payment_gateway as $pgateway) {
								$pg[$pgateway->id] = $pgateway->name;
							}

							echo form_dropdown('payment_gateway_id', $pg, '', 'class="tip form-control" required id="payment_gateway_id" data-placeholder="' . lang("select") . ' ' . lang("payment gateway") . '" ');
							?>
						</div>

						



							</div>
					</div>
                 </div>
                 
                <div class="col-sm-12 last_sa_se">
                <?php echo form_submit('driver_status', lang('submit'), 'class="btn btn-primary change_btn_save center-block online_submit"'); ?>
                <!--<input  id="submit-pay" type="button" value="Pay Now" class="btn btn-primary  change_btn_save center-block online_submit" />-->
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

$(document).on('change', '#offer_id', function(){
	var offer_amount = $('#offer_id option:selected').attr('data-offer');
	
	if(offer_amount == 0){
		$('#paid_amount').val('');
		$('#paid_amount').attr({"readonly":false});
	}else{
		$('#paid_amount').val(offer_amount);
		$('#paid_amount').attr({"readonly":true});
	}
})

$(document).on('change', '#is_country', function(){
		var group_id = '<?php echo $group_id ?>';
        var site = '<?php echo site_url() ?>';
		var is_country = $('#is_country').val();
	  window.location.href = site+"admin/wallet/addmoney/"+group_id+"/?is_country="+is_country;
		
    
})
</script>
