
<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <?php $attrib = array('class' => 'form-horizontal','class' => 'add_from','data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("masters/add_bank", $attrib);
                ?>
                <div class="row">
               	  <div class="instance_country col-sm-12">
                	  <div class="form-group col-sm-3 <?php if($this->session->userdata('group_id') != 1){ echo 'hidden'; } ?>">
						<?php echo lang('instance_of_country', 'instance_of_country'); ?>
                        <select <?php if($this->session->userdata('group_id') == 1){ echo 'required'; } ?> class="form-control select is_country"  name="is_country" id="is_country">
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
					  <div class="col-md-12">  
						<h2 class="box_he_de">Bank Details</h2>
						<div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('account_holder_name', 'account_holder_name'); ?>
							<div class="controls">
								<input type="text" id="account_holder_name" onkeyup="inputFirstUpper(this)" name="account_holder_name"  class="form-control"/>
							</div>
						</div>
						<div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('account_no', 'account_no'); ?>
							<div class="controls">
								<input type="text" id="account_no" name="account_no" class="form-control"/>
							</div>
						</div>
						<div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('bank_name', 'bank_name'); ?>
							<div class="controls">
								<input type="text" id="bank_name" name="bank_name" onkeyup="inputFirstUpper(this)" class="form-control"/>
							</div>
						</div>
						<div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('branch_name', 'branch_name'); ?>
							<div class="controls">
								<input type="text" id="branch_name" name="branch_name" onkeyup="inputFirstUpper(this)" class="form-control"/>
							</div>
						</div>
						<div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('ifsc_code', 'ifsc_code'); ?>
							<div class="controls">
								<input type="text" id="ifsc_code" name="ifsc_code" onkeyup="inputUpper(this)" class="form-control"/>
							</div>
							<p class="help-block-small"><?= lang('ex_ifsc') ?></p>
						</div>

						<div class="form-group col-sm-3 col-xs-12" style="margin-top: 20px;">
							<input type="checkbox" class="checkbox" id="is_default" name="is_default" value="1"/>
							<label for="extras" class="padding05"><?= lang('is_default') ?></label>
						</div>

					</div>         
                </div>

                <div class="col-sm-12 last_sa_se"><?php echo form_submit('add_bank', lang('add_bank'), 'class="btn btn-primary change_btn_save center-block"'); ?></div>

                <?php echo form_close(); ?>
 </div>

        </div>
    </div>
</div>
