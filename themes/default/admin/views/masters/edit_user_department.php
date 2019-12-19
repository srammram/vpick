
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
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_user_department'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'class' => 'edit_from', 'role' => 'form');
        echo admin_form_open_multipart("masters/edit_user_department/".$id, $attrib); ?>
        <div class="modal-body">
            <h2 class="box_he_de text-center"><?= lang('enter_info'); ?></h2>
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
            <div class="col-md-6">
				<div class="form-group">
					<?= lang('name', 'name'); ?>
					<?php echo form_input('name', $result->name, 'class="form-control" id="name" onkeyup="inputFirstUpper(this)" required="required"'); ?>
				</div>
            </div>
            
            <div style="clear: both;height: 10px;"></div>
            
        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_user_department', lang('submit'), 'class="btn btn-primary change_btn_save center-block"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?= @$modal_js ?>