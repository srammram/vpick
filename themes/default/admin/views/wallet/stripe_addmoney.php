<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
$productinfo = 'Wallet Addmoney';
$txnid = 'HEYYCAB'.time();
$surl = $surl;
$furl = $furl;        
$key_id = STRIPE_KEY_ID;
$currency_code = $currency_code;            
$total = ($paid_amount* 100); 
$amount = round($paid_amount);
$merchant_order_id = 'HEYYCAB'.time();
$card_holder_name = $user_data->first_name;
$email = $user_data->email == '' ? 'admin@srammram.com' : $user_data->email;
$phone = $user_data->countrycode.$user_data->mobile;
$name = $user_data->first_name;
$return_url = admin_url().'wallet/strip_addmoney/'.$group_id;
?>
<div class="box">
    <?php /*?><div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('driver_payment_status'); ?></h2>
    </div><?php */?>
    <div class="box-content">
    
   
            <div class="row">
            
            <div class="col-lg-12">
            <h2 class="box_he_de"><?= lang('payment_details') ?></h2>
            <!-- stripe payment form -->
            	<?php $attrib = array('class' => 'form-horizontal','class' => 'edit_from', 'data-toggle' => 'validator', 'id' => 'paymentFrm', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("wallet/stripe_addmoney/".$group_id, $attrib);
                ?>
                	<input type="hidden" name="payment_gateway" id="payment_gateway" value="<?= $payment_gateway ? $payment_gateway : 0 ?>" />
                    <input type="hidden" name="payment_mode" id="payment_mode" value="<?= $payment_mode ? $payment_mode : 0 ?>" />
                                
                               
                    <input type="hidden" name="is_country" id="user_id" value="<?= $is_country ?>" />
                    <input type="hidden" name="user_id" id="user_id" value="<?= $user_id ?>" />
                    <input type="hidden" name="offer" id="offer" value="<?= $offer ?>" />
                    <input type="hidden" name="group_id" id="group_id" value="<?= $group_id ?>" />
                    <input type="hidden" name="paid_amount" id="paid_amount" value="<?= $paid_amount ?>" />
                    <input type="hidden" name="amount" id="amount" value="<?= $paid_amount ?>" />
                    <input type="hidden" class="form-control" id="stripe-name" name="stripe_name" value="<?= $card_holder_name ?>">
                    <input type="hidden" class="form-control" id="stripe-email" name="stripe_email" value="<?= $email ?>">
                    
                    <div class="col-lg-12">
                    	<div class="well text-center">
                            <h1><?=$paid_amount ?></h1>
                        </div>
                    	
                        <div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('Card Number', 'Card Number'); ?>
                            <div class="controls">
                                <input type="text" name="stripe_cart_no" id="stripe-card-number" value="" class="form-control" required="required"/>
                            </div>
                        </div>
                        
                        <div class="form-group col-sm-1 col-xs-12">
							<?php echo lang('Month', 'Month'); ?>
                            <div class="controls">
                                <input type="text" id="stripe-card-expiry-month" name="stripe_month" placeholder="MM" maxlength="2" value="" class="form-control" required="required"/>
                            </div>
                        </div>
                        <div class="form-group col-sm-1 col-xs-12">
							<?php echo lang('Year', 'Year'); ?>
                            <div class="controls">
                                <input type="text" id="stripe-card-expiry-year" name="stripe_year" placeholder="YYYY" maxlength="4" value="" class="form-control" required="required"/>
                            </div>
                        </div>
                        <div class="form-group col-sm-1 col-xs-12">
							<?php echo lang('CVC', 'CVC'); ?>
                            <div class="controls">
                                <input type="text" id="stripe-card-cvc" name="cvc" placeholder="CVC" maxlength="3" value="" class="form-control" required="required"/>
                            </div>
                        </div>
                        
                    </div> 
                    <div class="col-sm-12 last_sa_se">
                        <input  id="payBtn" type="button" value="Pay Now" class="btn btn-primary  change_btn_save center-block online_submit" />
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

<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
    <script type="text/javascript">
  //set your publishable key
 
  Stripe.setPublishableKey('<?= $key_id ?>');
  //callback to handle the response from stripe
  function stripeResponseHandler(status, response) {
      if (response.error) {
          //enable the submit button
          $('#payBtn').removeAttr("disabled");
          //display the errors on the form
          $(".payment-errors").html('<div class="alert alert-danger">'+response.error.message+'</div>');
      } else {
          var form$ = $("#paymentFrm");
          //get token id
          var token = response['id'];
          //insert the token into the form
          form$.append("<input type='hidden' name='stripeToken' value='" + token + "' />");
          //submit form to the server
          form$.get(0).submit();
      }
  }

  $(document).ready(function() {
      //on form submit
      $("#payBtn").click(function(event) {
		 
          //disable the submit button to prevent repeated clicks
          $('#payBtn').attr("disabled", "disabled");
          
          //create single-use token to charge the user
          Stripe.createToken({
              number: $('#stripe-card-number').val(),
              cvc: $('#stripe-card-cvc').val(),
              exp_month: $('#stripe-card-expiry-month').val(),
              exp_year: $('#stripe-card-expiry-year').val()
          }, stripeResponseHandler);
          //submit from callback
          return false;
      });
  });  
  </script>