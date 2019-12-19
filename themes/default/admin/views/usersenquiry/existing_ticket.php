
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
           
            
        },
        submitButtons: 'input[type="submit"]'
    });
    </script>


<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('existing_ticket'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'class' => 'add_from', 'role' => 'form');
        echo admin_form_open_multipart("enquiry/existing_ticket", $attrib); ?>
        <div class="modal-body">
            <h2 class="box_he_de"><?= lang('enter_info'); ?></h2>
            <div class="form-group col-sm-12 <?php if($this->session->userdata('group_id') != 1){ echo 'hidden'; } ?>">
            <?php echo lang('instance_of_country', 'instance_of_country'); ?>
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
            
            <div class="col-md-12">
            
            	<div class="form-group col-md-6 col-xs-12">
					<?= lang('type', 'type'); ?>
                    <?php
                    $c[''] = 'Select type';
                    $c['5'] = 'Customer';
                    $c['4'] = 'Driver';
                    $c['3'] = 'Vendor';
                    
                    echo form_dropdown('customer_type', $c, '', 'class="form-control select-customer_type select"  id="customer_type" required="required"'); ?>
                </div>
                
                <div class="form-group col-sm-6 col-xs-12">
					<?php echo lang('country_code', 'country_code'); ?>
                    <?php
                    $cc[''] = 'Select Country Code';
                    foreach ($country_code as $cc_row) {
                        $cc[$cc_row->phonecode] = '(+'.$cc_row->phonecode.') '.$cc_row->name;
                    }
                    
                    echo form_dropdown('country_code', $cc, '', 'class="tip form-control" id="country_code" data-placeholder="' . lang("select") . ' ' . lang("country_code") . '" required="required"');
                    ?>
                </div>
                
                <div class="form-group col-sm-6 col-xs-12">
                    <?php echo lang('mobile', 'mobile'); ?>
                    <div class="controls">
                        <input type="text" id="mobile" name="mobile" class="form-control" required="required"/>
                    </div>
                </div>
                
            
            </div>
            
            <div style="clear: both;height: 10px;"></div>
            
            

        </div>
        <div class="modal-footer">
            <?php echo form_submit('ticket', lang('submit'), 'class="btn btn-primary change_btn_save center-block"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= @$modal_js ?>