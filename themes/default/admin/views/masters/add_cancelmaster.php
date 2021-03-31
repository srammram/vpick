
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

                <p class="introtext"><?php echo lang('add_cancelmaster'); ?></p>

                <?php $attrib = array('class' => 'form-horizontal','class' => 'add_from','data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("masters/add_cancelmaster", $attrib);
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
                                <?php echo lang('group', 'group'); ?>
                                <?php
                                $ge[''] = array('4' => lang('driver'), '5' => lang('customer'));
                                echo form_dropdown('group_id', $ge, '', 'class="wallettype form-control" id="wallettype" data-placeholder="' . lang("select") . ' ' . lang("group") . '" required="required"');
                                ?>
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('title', 'title'); ?>
                                <div class="controls">
                                    <input type="text" id="title" name="title" class="form-control" required
                                          />
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('message', 'message'); ?>
                                <div class="controls">
									<textarea id="message" name="message" class="form-control"></textarea>
                                    
                                </div>
                            </div>
                            <!--<div class="form-group">
                                <?php echo lang('charge_type', 'charge_type'); ?>
                                <?php
                                $d[''] = array('1' => lang('driver_side'), '2' => lang('customer_side'));
                                echo form_dropdown('charge_type', $d, '', 'class="form-control" id="charge_type" data-placeholder="' . lang("select") . ' ' . lang("charge_type") . '" required="required"');
                                ?>
                            </div>-->
                          
                            
                        </div>
                       
                       
                    </div>                   
                </div>

                <p><?php echo form_submit('add_cancelmaster', lang('submit'), 'class="btn btn-primary"'); ?></p>

                <?php echo form_close(); ?>
 </div>

        </div>
    </div>
</div>



