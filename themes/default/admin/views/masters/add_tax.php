
<script>
$('form[class="add_from"]').bootstrapValidator({
        fields: {
            name: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the Name'
                    },
                   
                }
            },
            
            symbol:{
                validators: {
                    notEmpty: {
                        message: 'Please Enter The symbol'
                    }
                }
            },
			
			unicode_symbol:{
                validators: {
                    notEmpty: {
                        message: 'Please Enter The symbol'
                    }
                }
            },
			
            iso_code:{
                validators: {
                    notEmpty: {
                        message: 'Please Enter The iso_code'
                    }
                }
            },

            numeric_iso_code:{
                validators: {
                    notEmpty: {
                        message: 'Please Enter The numeric_iso_code'
                    }
                }
            },
            
        },
        submitButtons: 'input[type="submit"]'
    });
    </script>


<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
   
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <?php $attrib = array('class' => 'form-horizontal','class' => 'add_from','data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("masters/add_tax", $attrib);
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
                          <h2 class="box_he_de">Add Tax</h2>
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('name', 'name'); ?>
                                <div class="controls">
                                    <input type="text" id="tax_name" name="tax_name" onkeyup="inputUpper(this)"	 class="form-control" required="required"/>
                                </div>
                            </div>
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('percentage', 'percentage'); ?>
                                <div class="controls">
                                    <input type="text" id="percentage" name="percentage" class="form-control"
                                           required="required"/>
                                </div>
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12" style="margin-top: 20px;">
                                <input type="checkbox" class="checkbox" id="is_default" name="is_default" value="1"/>
                                <label for="extras" class="padding05"><?= lang('is_default') ?></label>
                            </div>
                            
                        </div>
                       
                </div>

               <div class="col-sm-12 last_sa_se"><?php echo form_submit('add_tax', lang('submit'), 'class="btn btn-primary change_btn_save center-block"'); ?></div>

                <?php echo form_close(); ?>
 </div>

        </div>
    </div>
</div>

