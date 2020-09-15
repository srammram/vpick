<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
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
<div class="box">
    <?php /*?><div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('driver_payment_status'); ?></h2>
    </div><?php */?>
    <div class="box-content">
           <div class="row">
           <div class="col-lg-12">
			<?php $attrib = array('class' => 'form-horizontal','class' => 'edit_from', 'data-toggle' => 'validator', 'id' => 'settlement-form', 'role' => 'form', 'autocomplete' => "off");
            echo admin_form_open_multipart("account/account_settlement_verify/".$result->id, $attrib);
            ?>
            
         
          	
          	<table class="table col-lg-12">
            	<tbody>
                	<tr><td>Transaction Code</td><td><?= $result->settlement_code ?></td></tr>
                    <tr><td>Date</td><td><?= $result->settlement_date ?></td></tr>
                    <tr><td>Amount</td><td><?= $result->settlement_amount ?></td></tr>
                    <tr><td>Transaction Type</td><td><?= $result->settlement_type == 1 ? 'Cash' : 'Bank' ?></td></tr>
                    <tr><td>From user</td><td><?= $result->from_user ?></td></tr>
                    <tr><td>From Company</td><td><?= $result->from_company.' - Branch' ?></td></tr>
                    <tr><td>From Bank</td><td><?= $result->from_bank_name == '' ? $result->from_account_no.' - Cash' : $result->from_account_no.' - '.$result->from_bank_name ?></td></tr>
                    <tr><td>To user</td><td><?= $result->to_user ?></td></tr>
                    <tr><td>To Company</td><td><?= $result->to_company.' - Headoffice' ?></td></tr>
                    <tr><td>To Bank</td><td><?= $result->to_bank_name == '' ? $result->to_account_no.' - Cash' : $result->to_account_no.' - '.$result->to_bank_name ?></td></tr>
                     <?php
					 if($result->bank_challan != ''){
					 ?>
                     <tr><td>Bank Challan</td><td><img src="<?= base_url('assets/uploads/').$result->bank_challan ?>" width="100"></td></tr>
                     <?php
					 }
					 ?>
                    <tr><td>Status</td><td><?= $result->settlement_status == 1 ? 'Bank Process' : $result->settlement_status == 2 ? 'Reconcilation' : $result->settlement_status == 3 ? 'Complete' : 'Pending' ?></td></tr>
                    <tr><td>Verify</td><td><div class="switch-field">
        
                    <input type="radio" value="0" id="switch_left_to_verify" class="skip" name="to_verify" checked>
                    <label for="switch_left_to_verify">OFF</label>
                    <input type="radio" value="1" id="switch_right_to_verify" class="skip" name="to_verify">
                    <label for="switch_right_to_verify">ON</label>
                </div></td></tr>
                </tbody>
            </table>
            <div class="form-group">
                
            </div>
            
            <div class="col-sm-12 last_sa_se">
                <?php echo form_submit('settlement_branch', lang('submit'), 'class="btn btn-primary change_btn_save center-block settlement"'); ?>
             </div>
            <?php echo form_close(); ?>
            </div>
              	

        </div>
    </div>
</div>
<script>

</script>
