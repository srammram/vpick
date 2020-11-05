<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
$productinfo = 'Wallet Addmoney';
$txnid = 'HEYYCAB'.time();
$surl = $surl;
$furl = $furl;        
$key_id = RAZOR_KEY_ID;
$currency_code = $currency_code;            
$total = ($paid_amount* 100); 
$amount = round($paid_amount);
$merchant_order_id = 'HEYYCAB'.time();
$card_holder_name = $user_data->first_name;
$email = $user_data->email == '' ? 'admin@srammram.com' : $user_data->email;
$phone = $user_data->countrycode.$user_data->mobile;
$name = $user_data->first_name;
$return_url = admin_url().'wallet/razorpay_addmoney/'.$group_id;
?>
<div class="box">
    <?php /*?><div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('driver_payment_status'); ?></h2>
    </div><?php */?>
    <div class="box-content">
    
   
            <div class="row">
           	
            <div class="col-lg-12">
            	<?php $attrib = array('class' => 'form-horizontal','class' => 'edit_from', 'data-toggle' => 'validator', 'id' => 'razorpay-form', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("wallet/razorpay_addmoney/".$group_id, $attrib);
                ?>
                
                <div class="row">
					<div class="col-md-12">    	
						<h2 class="box_he_de"><?= lang('payment_details') ?></h2>
							<div class="col-md-12">
                            	<input type="hidden" name="payment_gateway" id="payment_gateway" value="<?= $payment_gateway ? $payment_gateway : 0 ?>" />
                                <input type="hidden" name="payment_mode" id="payment_mode" value="<?= $payment_mode ? $payment_mode : 0 ?>" />
                                
                               
								<input type="hidden" name="is_country" id="user_id" value="<?= $is_country ?>" />
                                <input type="hidden" name="user_id" id="user_id" value="<?= $user_id ?>" />
                                <input type="hidden" name="offer" id="offer" value="<?= $offer ?>" />
                                <input type="hidden" name="group_id" id="group_id" value="<?= $group_id ?>" />
                                <input type="hidden" name="paid_amount" id="paid_amount" value="<?= $paid_amount ?>" />
                                
                                <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id" />
                                <input type="hidden" name="merchant_order_id" id="merchant_order_id" value="<?php echo $merchant_order_id; ?>"/>
                                <input type="hidden" name="merchant_trans_id" id="merchant_trans_id" value="<?php echo $txnid; ?>"/>
                                <input type="hidden" name="merchant_product_info_id" id="merchant_product_info_id" value="<?php echo $productinfo; ?>"/>
                                <input type="hidden" name="merchant_surl_id" id="merchant_surl_id" value="<?php echo $surl; ?>"/>
                                <input type="hidden" name="merchant_furl_id" id="merchant_furl_id" value="<?php echo $furl; ?>"/>
                                <input type="hidden" name="card_holder_name_id" id="card_holder_name_id" value="<?php echo $card_holder_name; ?>"/>
                                <input type="hidden" name="merchant_total" id="merchant_total" value="<?php $total; ?>"/>
                                <input type="hidden" name="merchant_amount" id="merchant_amount" value="<?php $amount; ?>"/>
						
							<div class="well text-center">
                            	<h1><?=$paid_amount ?></h1>
                            </div>


							</div>
					</div>
                 </div>
                 
                <div class="col-sm-12 last_sa_se">
                <input  id="submit-pay" type="button" value="Pay Now" class="btn btn-primary  change_btn_save center-block online_submit" />
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


   	
$(document).ready(function(){
	
	//$('#payment_status').select2({readonly:true});
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
	
	//$("#payment_status").select2("readonly", true);
	//$("#payment_id").select2("readonly", true);
	
	
});


</script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
var razorpay_options = {
    key: "<?php echo $key_id; ?>",
    amount: "<?php echo $total; ?>",
    name: "<?php echo $name; ?>",
    description: "Order # <?php echo $merchant_order_id; ?>",
    netbanking: true,
    currency: "<?php echo $currency_code; ?>",
    prefill: {
      name:"<?php echo $card_holder_name; ?>",
      email: "<?php echo $email; ?>",
      contact: "<?php echo $phone; ?>"
    },
    notes: {
      soolegal_order_id: "<?php echo $merchant_order_id; ?>",
    },
    handler: function (transaction) {
		//alert(transaction.razorpay_payment_id);
        document.getElementById('razorpay_payment_id').value = transaction.razorpay_payment_id;
        document.getElementById('razorpay-form').submit();
    },
    "modal": {
        "ondismiss": function(){
            location.reload()
        }
    }
  };
  var razorpay_submit_btn, razorpay_instance;

 
$(document).on('click', '.online_submit',  function(el){
	
	 razorpaySubmit(el);
 });

function razorpaySubmit(el){
	 
    if(typeof Razorpay == 'undefined'){
		
      setTimeout(razorpaySubmit, 200);
      if(!razorpay_submit_btn && el){
        razorpay_submit_btn = el;
        el.disabled = true;
        el.value = 'Please wait...';  
      }
    } else {
		
      if(!razorpay_instance){
		  
		  
		  
        razorpay_instance = new Razorpay(razorpay_options);
        if(razorpay_submit_btn){
          razorpay_submit_btn.disabled = false;
          razorpay_submit_btn.value = "Pay Now";
        }
      }
      razorpay_instance.open();
    }
  }  
</script>