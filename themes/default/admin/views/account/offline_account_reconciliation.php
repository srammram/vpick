<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="box">
    <?php /*?><div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('driver_payment_status'); ?></h2>
    </div><?php */?>
    <div class="box-content">
    
   
            <div class="row">
           	
            <div class="col-lg-12">
            	<?php $attrib = array('class' => 'form-horizontal','class' => 'edit_from', 'data-toggle' => 'validator', 'id' => 'razorpay-form', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("account/offline_account_reconciliation/".$account_id, $attrib);
                ?>
                
                <div class="row">
					<div class="col-md-12">    	
						<h2 class="box_he_de"><?= lang('payment_details') ?></h2>
							<div class="col-md-12">
								<input type="hidden" name="account_id" id="account_id" value="<?= $account_id ?>" />
                                
                               
						
							<div class="col-lg-6">
                            	
                                <table class="table">
									<tbody>
                                    	<tr>
                                        	<td>type</td>
                                            <td><?php 
											if($result->type == 1){ echo 'Wallet'; }elseif($result->type == 2){ echo 'Ride'; }elseif($result->type == 3){ echo 'Incentive'; }elseif($result->type == 4){ echo 'Offer'; }else{ echo ''; } ?></td>
                                        </tr>
                                        <tr>
                                        	<td>payment_mode</td>
                                            <td><?= $result->payment_mode == 1 ? 'Online' : 'Offline' ?></td>
                                        </tr>
                                        <tr>
                                        	<td>payment_type</td>
                                            <td><?= $result->payment_type ?></td>
                                        </tr>
                                        <tr>
                                        	<td>amount</td>
                                            <td><?php if($result->credit != 0.00){ echo $result->credit; }elseif($result->debit != '0.00'){ echo $result->debit; }else{ echo '0.00'; } ?></td>
                                        </tr>
                                        <tr>
                                        	<td>status</td>
                                            <td><?php if($result->account_status == 1){ echo 'Bank Process'; }elseif($result->account_status == 2){ echo 'Reconcilation'; }elseif($result->account_status == 3){ echo 'Complete'; }else{ echo 'Pending'; } ?></td>
                                        </tr>
                                        <tr>
                                        	<td>account_transaction_no</td>
                                            <td><?= $result->account_transaction_no ?></td>
                                        </tr>
                                        <tr>
                                        	<td>account_transaction_date</td>
                                            <td><?= $result->account_transaction_date ?></td>
                                        </tr>
                                    </tbody>
                                </table>
								<div class="form-group col-sm-12 col-xs-12">
									<?= lang("verify", "verify"); ?><br>
                                    <div class="switch-field">
    									
                                        <input type="radio" value="0" id="switch_left_account_verify" class="skip" name="account_verify" <?php echo ($result->account_verify==0) ? "checked" : ''; ?>>
                                        <label for="switch_left_account_verify">OFF</label>
                                        <input type="radio" value="1" id="switch_right_account_verify" class="skip" name="account_verify" <?php echo ($result->account_verify==1) ? "checked" : ''; ?>>
                                        <label for="switch_right_account_verify">ON</label>
    
                                    </div>
                                </div>
							</div>
					</div>
                 </div>
                 
                <div class="col-sm-12 last_sa_se">
                <input type="submit" name="offline_submit" value="submit" class="btn btn-primary  change_btn_save center-block offline_submit" />
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


</script>
