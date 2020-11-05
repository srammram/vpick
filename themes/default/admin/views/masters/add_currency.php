
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

                <p class="introtext"><?php echo lang('add_currency'); ?></p>

                <?php $attrib = array('class' => 'form-horizontal','class' => 'add_from','data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("masters/add_currency", $attrib);
                ?>
                <div class="row">
                	
                    <div class="col-md-6">
                    	<div class="form-group col-sm-12 <?php if($this->session->userdata('group_id') != 1){ echo 'hidden'; } ?>">
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
                        <div class="col-md-12">                                   
                            <div class="form-group">
                                <?php echo lang('base_currency_name', 'base_currency_name'); ?>
                                
                                <select class="form-control select currency"  name="name" id="currency">
                                    <option value="">Select Currency</option>
                                    <?php
                                    foreach($unicodesymbol as $unicodesymbols){
                                    ?>
                                    <option value="<?= $unicodesymbols->name ?>" data-unicode="<?= $unicodesymbols->unicode ?>" data-symbol="<?= $unicodesymbols->symbol ?>" ><?= $unicodesymbols->name.'('.$unicodesymbols->symbol.')' ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                                
                                <!--<div class="controls">
                                    <input type="text" id="name" name="name" onkeyup="inputFirstUpper(this)"	 class="form-control" required="required"/>
                                </div>-->
                            </div>
                            <div class="form-group">
                                <?php echo lang('symbol', 'symbol'); ?>
                                <div class="controls">
                                    <input type="text" id="symbol" name="symbol" class="form-control" readonly
                                          />
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('unicode', 'unicode'); ?>
                                <div class="controls">
                                    <input type="text" id="unicode_symbol" name="unicode_symbol" class="form-control" readonly
                                           />
                                </div>
                            </div>
                           <!-- <div class="form-group">
                                <?php echo lang('iso_code', 'iso_code'); ?>
                                <div class="controls">
                                    <input type="text" id="iso_code" name="iso_code" class="form-control"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('numeric_iso_code', 'numeric_iso_code'); ?>
                                <div class="controls">
                                    <input type="text" id="numeric_iso_code" name="numeric_iso_code" class="form-control"/>
                                </div>
                            </div>-->
                            
                            
                            
                            <div class="form-group">
                                <input type="checkbox" class="checkbox" id="is_default" name="is_default" value="1"/>
                                <label for="extras" class="padding05"><?= lang('is_default') ?></label>
                            </div>
                            
                        </div>
                       
                       
                    </div>                   
                </div>

                <p><?php echo form_submit('add_currency', lang('submit'), 'class="btn btn-primary"'); ?></p>

                <?php echo form_close(); ?>
 </div>

        </div>
    </div>
</div>


