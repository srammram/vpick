
<script>
$('form[class="edit_from"]').bootstrapValidator({
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

                <p class="introtext"><?php echo lang('edit_walletoffer'); ?></p>

                <?php $attrib = array('class' => 'form-horizontal', 'class' => 'edit_from', 'data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("masters/edit_walletoffer/".$walletoffer->id, $attrib);
                ?>
                <div class="row">
                	
                    <div class="col-md-6">
                        <div class="form-group col-md-12 <?php if($this->session->userdata('group_id') != 1){ echo 'hidden'; } ?>">
						<?php echo lang('instance_of_country', 'instance_of_country'); ?>
                        <select <?php if($this->session->userdata('group_id') == 1){ echo 'required'; } ?> class="form-control select is_country"  name="is_country" id="is_country">
                            <option value="">Select Country</option>
                            <?php
                            foreach($AllCountrys as $AllCountry){
                            ?>
                            <option value="<?= $AllCountry->iso ?>" <?php if($AllCountry->iso == $walletoffer->is_country){ echo 'selected'; }else{ echo ''; } ?>><?= $AllCountry->name ?></option>
                            <?php
                            }
                            ?>
                        </select>
                        </div>
                            
                             <div class="col-md-12">                                   
                            <div class="form-group">
                                <?php echo lang('type', 'Type'); ?>
                                <?php
                                $ge[''] = array('0' => lang('custom'), '1' => lang('City'), '2' => lang('Rental'), '3' => lang('Outstation'));
                                echo form_dropdown('type', $ge, $walletoffer->type, 'class="wallettype form-control" id="wallettype" data-placeholder="' . lang("select") . ' ' . lang("type") . '" required="required"');
                                ?>
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('name', 'name'); ?>
                                <div class="controls">
                                    <input type="text" id="name" value="<?= $walletoffer->name ?>" name="name" class="form-control" required
                                          />
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('amount', 'amount'); ?>
                                <div class="controls">
                                    <input type="text" id="amount" name="amount" value="<?= $walletoffer->amount ?>" class="form-control" required
                                           />
                                </div>
                            </div>
                            
                            <div class="form-group custom">
                                <?php echo lang('offer_amount', 'offer_amount'); ?>
                                <div class="controls">
                                    <input type="text" id="offer_amount" name="offer_amount" value="<?= $walletoffer->offer_amount ?>" class="form-control"
                                           />
                                </div>
                            </div>
                            <div class="form-group custom">
                                <?php echo lang('offer_date', 'offer_date'); ?>
                                <div class="controls">
                                    <input type="text" id="offer_date" name="offer_date" value="<?= $walletoffer->offer_date ?>" class="form-control"
                                           />
                                </div>
                            </div>
                          
                            
                            
                            <div class="form-group is_default">
                                <input type="checkbox" class="checkbox" id="is_default" name="is_default" <?= $walletoffer->is_default == 1 ? 'checked' : '' ?> value="1"/>
                                <label for="extras" class="padding05"><?= lang('is_default') ?></label>
                            </div>
                            
                        </div>
                      
                    </div>
                </div>
                   

                <p><?php echo form_submit('update_walletoffer', lang('submit'), 'class="btn btn-primary"'); ?></p>

                <?php echo form_close(); ?>
 </div>

        </div>
    </div>
</div>

<style>
    .input-group .form-control{
        z-index:1 !important;
    }
</style>
<script>
$(document).ready(function(){
	<?php
	if($walletoffer->type != 0){
	?>
	$('.custom').hide();
	$('.is_default').show();
	
	<?php
	}else{
	?>
	$('.custom').show();
	$('.is_default').hide();
	<?php
	}
	?>
	$('.wallettype').change(function(){
		var wallettype = $(this).val();
		if(wallettype == 0){
			$('.custom').show();
			$('.is_default').hide();
		}else{
			$('.custom').hide();
			$('.is_default').show();
		}
	});
});
</script>
