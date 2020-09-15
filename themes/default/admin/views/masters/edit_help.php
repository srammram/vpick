

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
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_help'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator','class' => 'edit_from', 'role' => 'form');
        echo admin_form_open_multipart("masters/edit_help/".$id, $attrib); ?>
        <div class="modal-body">
           <h2 class="box_he_de"> <?= lang('enter_info'); ?></h2>

            <div class="col-md-6">
            <div class="form-group">
                <?= lang('type', 'type'); ?>
               
                <select name="type[]" id="type" class="form-control" multiple>
                	<option  value="Customer" <?php if(in_array("Customer", json_decode($result->type))){ echo 'selected'; }else{ echo ''; } ?>>Customer</option>
                    <option value="Driver" <?php if(in_array("Driver", json_decode($result->type))){ echo 'selected'; }else{ echo ''; } ?>>Driver</option>
                    <option value="Vendor" <?php if(in_array("Vendor", json_decode($result->type))){ echo 'selected'; }else{ echo ''; } ?>>Vendor</option>
                </select>
            </div>
            <div class="form-group">
                <?= lang('name', 'name'); ?>
                <?php echo form_input('name', $result->name, 'class="form-control" onkeyup="inputFirstUpper(this)" id="name" required="required"'); ?>
            </div>
            </div>
            
            <div style="clear: both;height: 10px;"></div>
            
            

        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_help', lang('submit'), 'class="btn btn-primary change_btn_save center-block"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?= @$modal_js ?>